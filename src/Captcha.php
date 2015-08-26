<?php
/**
 * Created by Sameer Shelavale.
 * User: sameer-shelavale
 * Date: 5/12/14
 * Time: 8:26 PM
 */
namespace MultiCaptcha;

require_once ( rtrim( __DIR__, '/'). '/BaseCaptcha.php' );
//Auto load the required php classes
spl_autoload_register( function($className){
    $curDir = rtrim( __DIR__, '/');
    //$cls = array_pop( explode('\\', $className) );

    if( preg_match( '/MultiCaptcha\\\Types\\\(.+)\\\(.+)$/i', $className, $matches ) ){
        $cls = ltrim( strtolower( preg_replace( '/([A-Z])/', '_$1',$matches[2])), '_');
        $dir = ltrim( strtolower( preg_replace( '/([A-Z])/', '_$1',$matches[1])), '_');
        require_once( $curDir.'/types/'. $dir.'/'. $cls.'.php');
    }elseif( preg_match( '/MultiCaptcha\\\Types\\\(.+)$/i', $className, $matches ) ){
        $cls = ltrim( strtolower( preg_replace( '/([A-Z])/', '_$1',$matches[1])), '_');
        require_once( $curDir.'/types/'. $cls.'/'. $cls.'.php');
    }elseif( preg_match( '/MultiCaptcha\\\Themes\\\(.+)$/i', $className, $matches ) ){
        $cls = $matches[1];
        require_once( $curDir.'/themes/'. $cls.'.php');
    }
});

class Captcha extends BaseCaptcha {

    var $secretKey = "{{((o=All-Hands-More-Sail=o))}}"; //you MUST change this in live environment
    var $life = 1;     //validity period of captcha in HOURS
    //it will be invalid after this time
    //basically we will remove all logs of successful captchas older than this amount of time.
    //this will reduce load on database

    var $customFieldName = null;    //use custom field name for captcha instead of random names
    //this is not applicable to Recaptcha
    //if you specify custom field, it will also add a extra hidden field which will contain the encrypted code
    //this extra field will be named $customFieldName."_challenge"

    var $supportedTypes = array(
        'recaptcha' => 'Recaptcha', //captcha by Google
        'image'     => 'Image',     //user has to type code displayed in captcha image
        'honeypot'  => 'HoneyPot',  //honeypot, mainly for spambots, adds an hidden field which only the spambots fill in
        'checkbox'  => 'Checkbox',  //adds an checkbox which is supposed to be left blank by humans, but bots mark it checked
        'math'      => 'Math',      //Math captcha which asks answer of simple mathematical questions
        'ascii'     => 'Ascii',     //ASCII captcha, displays the captcha in ASCII decorative style
        'gif'       => 'Gif',       //GIF animated captcha
        'nocaptcha' => 'Nocaptcha',  //nocaptcha by Google
        //'video'     => 'Video'      //Video Captcha(not implemented yet)
    );

    var $enabledTypeOptions = array();    //multiple types can also be specified,
    //in that case the captcha will be randomized from the selected types

    var $objects = array();

    var $error = false;

    var $msgInvalidCaptcha = "Invalid captcha challenge."; // if the captcha field is not found or the provided cipherText cant be decrypted
    var $msgExpiredCaptcha = "Captcha challenge expired."; //if the question is expired i.e. user took too long to answer
    var $msgAnsweredRecently = "The captcha challenge is invalid. Did you resubmit the form? "; //if the question is already asked quiet recently
    var $msgFieldNotFound = "Captcha challenge field is not found."; // Captcha field is not found in the submitted data

    var $refreshUrl = null;
    var $helpUrl = null;
    var $cacheSize = 10; //total files to be used for caching idz of submitted captcha
    var $cacheDir;

    var $data;  // used to store captcha data created by make() function for rendering
    /*
     * constructor
     */
    function __construct( $params ){
        $this->cacheDir = rtrim( __DIR__). '/cache/';
        $this->setParams( $params );

        if( isset( $params['life'] )&& is_numeric( $params['life'] ) ){
            $this->life = $params['life'];
        }else{
            $this->life = 10;
        }

        if( isset( $params['options'] ) ){
            $this->setOptions( $params['options'] );
        }else{
            $this->setOptions( [
                'image' => [
                    'maxCodeLength' => 8,
                    'font'=>rtrim( __DIR__). '/fonts/comic.ttf',
                    'width'=>175
                ]
            ] );
        }


    }


    /* function setOptions()
     *      Sets options for various captcha types.
     *      It also updates the options in the captcha objects, if the objects are initialized
     *      @param $typeOptions Array of options with captchaType as keys
     *      e.g. array(
     *              'recaptcha' => array(
     *                  'publicKey' => 'mypublickey',
     *                  'privateKey'=> 'myprivatekey'
     *              ),
     *              'image' => array(
     *                  'maxCodeLength' => '8'
     *              )
     *          );
     *      so we can set options for multiple captcha types at once
     *      NOTE: This function overrides the setOptions() in BaseCaptcha
     */
    function setOptions( $typeOptions = array()){
        foreach( $typeOptions as $type => $options ){
            if( isset( $this->enabledTypeOptions[$type]) && is_array( $this->enabledTypeOptions[$type] ) ){
                $this->enabledTypeOptions[$type] = array_merge( $this->enabledTypeOptions[ $type ], $options );
            }else{
                $this->enabledTypeOptions[$type] = $options ;
            }
            //if the captcha type object is already initialized we will update the options in the object
            if( isset( $this->objects[$type] ) ){
                $this->objects[$type]->setParams( $this->enabledTypeOptions[$type] );
            }
        }
    }

    public function getObject( $type ){
        if( !isset( $this->objects[$type] ) ){
            $name = 'MultiCaptcha\\Types\\'.$this->supportedTypes[$type]; //Investigate Why is it not working without namespace prefix? we are in same namespace
            $this->objects[$type] = new $name(
                $this->secretKey,
                $this->life,
                $this->customFieldName
            );
            $this->objects[$type]->setParams( $this->enabledTypeOptions[$type] );
        }
        return $this->objects[$type];
    }


    /*
     * function getHtml()
     * @return html of the captcha code, this is to be inserted in the forms directly
     */
    public function render( $type = null, $refresh=false ){

        //first select the type
        if( !$this->data ){
            if( !$this->make( $type ) ){
                return false;
            }
        }

        if( $this->data['type'] == 'recaptcha' || $this->data['type'] == 'nocaptcha' ){
            return $this->data['question']['content'];
        }

        //create a theme object of specified theme name for the given captcha type
        $themeName = $this->data['theme'];

        if( $themeName && class_exists( $themeName ) ){
            $themeObj = new $themeName( $this->data['themeOptions'] );
        }else{
            $themeObj = new Themes\DefaultTheme( $this->data['themeOptions'] );
        }

        //now render the captcha by calling the render function of the theme
        if( $refresh ){
            return $themeObj->refresh( $this->data );
        }
        return $themeObj->render( $this->data );
    }

    /*
     * function getHtml()
     * @return html of the captcha code, this is to be inserted in the forms directly
     */
    public function refresh( $type = null ){
        return $this->render( $type, true );
    }

    /*
     * function make()
     *      returns the rendering data for the given/random type of captcha
     *      This function is intended to be used for customizing the captcha look
     *      in third party libraries/softwares which may not want to use the themes
     */
    public function make( $type = null ){

        if( count( $this->enabledTypeOptions ) == 0 ){
            return false;
        }

        if( !$type ){
            //no type specified, use random captcha from enabled types
            $type = array_rand( $this->enabledTypeOptions );
        }elseif( !isset( $this->enabledTypeOptions[ $type ] ) ){
            //selected type of captcha does not exist or is not enabled
            return false;
        }

        $obj = $this->getObject( $type );
        $data = $obj->generate();

        $data['type'] = $type;
        if( $type != 'recaptcha' && $type != 'nocaptcha' ){
            $data['cipher'] = $this->encrypt( $data['answer'], $type );

            if( $data['customFieldName'] ){
                //custom fieldName is set
                $data['fieldName'] = $data['customFieldName'];
                $data['hidden'] = '<input type="hidden" name="'.$data['fieldName'].'_challenge" value="'.$data['cipher'] .'" /> ';
            }else{
                $data['fieldName'] = $data['cipher'];
                $data['hidden'] = '';
            }
        }

        $data['refreshUrl'] = $this->refreshUrl;
        $data['helpUrl'] = $this->helpUrl;

        $this->data = $data;
        return $data;
    }



    /*
     * function validate()
     * @param $data posted data or array containing the customField value and challangeFieldValue
     *              for example if $customFieldName is "my_captcha_field"
     *              then you can pass array( 'my_captcha_field'=>$_POST['my_captcha_field'], 'my_captcha_field_response'=>$_POST['my_captcha_field_challange'] )
     * @param -$remoteAddress remote IP address (Optional)
     * @return boolean
     */
    public function validate( $data = array(), $remoteAddress = null ){
        $fieldName = $this->customFieldName;

        if( isset( $this->enabledTypeOptions['recaptcha'] )
            && isset( $data['recaptcha_response_field'] )
            && isset( $data['recaptcha_challenge_field'] ) ){

            $obj = $this->getObject( 'recaptcha' );

            //call verify function in Recaptcha class, (it has different implementation)
            if( $obj->verify( $data, $remoteAddress ) ){
                $this->error = false;
                return true;
            }else{
                $this->error = $obj->errorMsg;
                return false;
            }
        }elseif( isset( $this->enabledTypeOptions['nocaptcha'] )
            && isset( $data['g-recaptcha-response'] ) ){

            $obj = $this->getObject( 'nocaptcha' );

            //call verify function in Recaptcha class, (it has different implementation)
            if( $obj->verify( $data, $remoteAddress ) ){
                $this->error = false;
                return true;
            }else{
                $this->error = $obj->errorMsg;
                return false;
            }
        }

        //if customFieldName is disabled, we are using random field names
        //try find the field name
        if( !$fieldName ){
            foreach( $data as $key => $value ){
                //check if the key matches our particular challange format after decryption
                if( $this->verify( $key, $value, $isCaptchaField ) ){
                    //this is captcha field
                    return true;
                }else{
                    if( $isCaptchaField ){
                        //we have found captcha field and the answer is wrong
                        return false;
                    }
                }
            }
            $this->error = $this->msgInvalidCaptcha;
        }else{
            if( isset( $data[$fieldName], $data[$fieldName.'_challenge'] ) ){
                if( $this->verify( $data[$fieldName.'_challenge'], $data[$fieldName] ) ){
                    return true;
                }
            }
        }
        return false;
    }

    public function encrypt( $answer, $captchaType ){

        $ivSize = mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC );

        $iv = mcrypt_create_iv( $ivSize, MCRYPT_RAND );

        $time =  base_convert( time(), 10, 36 );

        $uid =  base_convert( uniqid(), 10, 36 );

        $plainText = $uid.'_'.$time.'_'.$answer.'_'.$captchaType;

        $cipherTextDec = mcrypt_encrypt( MCRYPT_RIJNDAEL_256, $this->secretKey, $plainText, MCRYPT_MODE_CBC, $iv ) ;

        return rtrim( base64_encode( $iv.$cipherTextDec ), '=' ) ;

    }


    public function decrypt( $cipherText ){

        $ivSize = mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC );

        $cipherTextDec = base64_decode( $cipherText );

        if( !$cipherTextDec ){
            //cipherText is not base64 encoded so its invalid/corrupt
            return false;
        }

        if( strlen( $cipherTextDec) < $ivSize ){
            return false;
        }

        # retrieves the IV, iv_size should be created using mcrypt_get_iv_size()
        $ivDec = substr( $cipherTextDec, 0, $ivSize );


        # retrieves the cipher text (everything except the $iv_size in the front)
        $cipherTextDec = substr( $cipherTextDec, $ivSize );

        # may remove 00h valued characters from end of plain text
        return rtrim(
            mcrypt_decrypt(
                MCRYPT_RIJNDAEL_256,
                $this->secretKey,
                $cipherTextDec,
                MCRYPT_MODE_CBC,
                $ivDec
            ),
            "\0"
        );

    }


    public function verify( $cipherText, $answer, &$isCaptchaField = false  ){
        $plainText = self::decrypt( $cipherText );

        if( !$plainText ){
            //cipherText is not base64 encoded so its invalid/corrupt
            $this->error = $this->msgInvalidCaptcha;
            return false;
        }

        if( preg_match( "/^([a-zA-Z0-9]{4,9})_([a-zA-Z0-9]{6})_([a-zA-Z0-9\-]*)_([a-zA-Z0-9]+)$/", $plainText, $matches ) ){
            $isCaptchaField = true;
            $uid = base_convert( $matches[1], 36, 10 );
            $time = base_convert( $matches[2], 36, 10 );
            $correctAnswer = $matches[3];
            $type = $matches[4];

            //check if the captcha is too old
            $age = time() - $time;
            if( $age > $this->life * 3600 || $age < 0 ){
                $this->error = $this->msgExpiredCaptcha;
                return false;
            }


            //check if this captcha is submitted recently
            if( $this->isSubmitted( $uid, $time ) ){
                //this captcha has already been submitted quiet recently
                $this->error = $this->msgAnsweredRecently;
                return false;
            }

            //check if answer is correct
            if( $answer != $correctAnswer ){
                $obj = $this->getObject( $type);
                $this->error = $obj->errorMsg;
                return false;
            }else{
                $this->error = false;
                return true;
            }
        }

        $this->errorString = $this->msgInvalidCaptcha;
        return false;
    }


    /*
     * function isSubmitted()
     *      Checks if a captcha is answered(correct or incorrect) by the same
     *      or another user earlier as well, if its not submitted this function
     *      will create a cache record for the captcha using its uid and expiration
     *      time. Implementing this function is very essential to avoid brute force
     *      attack with single captcha but different responses.
     *      This function also removes from cache all records of expired captcha.
     * @param $uid the unique id of the captcha as retrieved after decoding
     * @param $createdAt the creation timestamp, used to find expiration time
     * @return boolean true is the captcha is answered earlier else false
     *
     * Note: This function uses file cache the cache directory is specified by
     *      $this->cacheDir.
     *      It spreads out the records in multiple files in cache to avoid single file
     *      becoming too big and slowing down things. The number of storage files in
     *      cache are specifyed by $this->cacheSize
     *      and default value is 10. Its recommended to use large value like 1000 or more
     *      for high traffic websites.
     *      Alternatively you can extend this class to override this function and
     *      implement your own caching mechanism.
     */
    public function isSubmitted( $uid, $createdAt ){
        //make out the full file name
        $fileName = $this->cacheDir . ( intval($uid) % $this->cacheSize );
        $records = array();
        //if the file exists fetch the records
        if( file_exists( $fileName ) ){
            $records = json_decode( file_get_contents( $fileName ), true );
        }

        if( isset( $records[$uid] ) ){
            //captcha is already answered
            $result = true;
        }else{
            //captcha is not answered before
            $result = false;
            //add the uid and its expiration time
            $records[ $uid ] = $createdAt + ( $this->life * 3600 );
        }
        //remove the records of expired captcha
        $currentTime = time();
        foreach( $records as $i => $expTime ){
            if( $expTime < $currentTime ){
                unset( $records[$i] );
            }
        }
        //save the updated records back in the file
        file_put_contents( $fileName, json_encode( $records ));
        return $result;

    }

}
