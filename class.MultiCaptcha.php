<?php
/**
 * Created by Sameer Shelavale.
 * User: sameer-shelavale
 * Date: 5/12/14
 * Time: 8:26 PM
 */
namespace MultiCaptcha;

include_once( dirname(__FILE__).'/class.BaseCaptcha.php' );

class Captcha extends BaseCaptcha {

    var $secretKey = "{{((o=All-Hands-More-Sail=o))}}"; //you MUST change this in live environment
    var $life = 10;     //validity period of captcha in HOURS
    //it will be invalid after this time
    //basically we will remove all logs of successful captchas older than this amount of time.
    //this will reduce load on database

    var $customFieldName = null;    //use custom field name for captcha instead of random names
    //this is not applicable to Recaptcha
    //if you specify custom field, it will also add a extra hidden field which will contain the encrypted code
    //this extra field will be named $customFieldName."_challenge"

    var $supportedTypes = array(
        'recaptcha' => 'Recaptcha',     //captcha by google
        'image'     => 'ImageCaptcha',  //user has to type code displayed in captcha image
        'honeypot'  => 'HoneyPot',      //honeypot, mainly for spambots, adds an hidden field which only the spambots fill in
        'checkbox'  => 'Checkbox',      //adds an checkbox which is supposed to be left blank by humans, but bots mark it checked
        'math'      => 'MathCaptcha',   //Math captcha which asks answer of simple mathematical questions
        'ascii'     => 'AsciiCaptcha',  //ASCII captcha, displays the captcha in ASCII decorative style
        'gif'       => 'GifCaptcha',    //GIF animated captcha
        'video'     => 'VideoCaptcha'   //Video Captcha
    );

    var $enabledTypeOptions = array();    //multiple types can also be specified,
    //in that case the captcha will be randomized from the selected types

    var $objects = array();

    /*
     * constructor
     */
    function __construct( $secretKey, $typeOptions= array(), $life = 10, $customFieldName= null){
        $this->secretKey = $secretKey;
        $this->life = $life;
        $this->customFieldName = $customFieldName;

        $this->setOptions( $typeOptions );
    }


    /* function setOptions()
     *      Sets options for various captcha types.
     *      It also updates the options in the captcha objects, if the objects are initialized
     * @param $typeOptions Array of options with captchaType as keys
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
            if( is_array( $this->enabledTypeOptions[$type] ) ){
                $this->enabledTypeOptions[$type] = array_merge( $this->enabledTypeOptions[ $type ], $options );
            }else{
                $this->enabledTypeOptions[$type] = $options ;
            }
            //if the captcha type object is already initialized we will update the options in the object
            if( isset( $this->objects[$type] ) ){
                $this->objects[$type]->setOptions( $this->enabledTypeOptions[$type] );
            }
        }
    }

    public function getObject( $type ){
        if( !isset( $this->objects[$type] ) ){
            include_once( dirname(__FILE__).'/types/'.$type.'/class.'.$type.'.php' );

            $name = 'MultiCaptcha\\'.$this->supportedTypes[$type]; //Investigate Why is it not working without namespace prefix? we are in same namespace
            $this->objects[$type] = new $name(
                $this->secretKey,
                $this->life,
                $this->customFieldName
            );
            $this->objects[$type]->setOptions( $this->enabledTypeOptions[$type] );
        }
        return $this->objects[$type];
    }


    /*
     * function getHtml()
     * @return html of the captcha code, this is to be inserted in the forms directly
     */
    public function getHtml( $type = null ){

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

        return $obj->getHtml();
    }


    /*
     * function validateForm()
     * @param $data posted data or array containing the customField value and challangeFieldValue
     *              for example if $customFieldName is "my_captcha_field"
     *              then you can pass array( 'my_captcha_field'=>$_POST['my_captcha_field'], 'my_captcha_field_response'=>$_POST['my_captcha_field_challange'] )
     * @param $remoteAddress remote IP address
     * @return boolean
     */
    public function validateForm( $data = array(), $remoteAddress = null ){
        $fieldName = $this->customFieldName;

        if( isset( $this->enabledTypeOptions['recaptcha'] )
            && isset( $data['recaptcha_response_field'] )
            && isset( $data['recaptcha_challenge_field'] ) ){

            $obj = $this->getObject( 'recaptcha' );

            return $obj->validate( $data, $remoteAddress );
        }

        //if customFieldName is disabled, we are using random field names
        //try find the field name
        if( !$fieldName ){
            foreach( $data as $key => $value ){
                //check if the key matches our particular challange format after decryption
                if( $this->validate( $key, $value ) ){
                    //this is captcha field
                    return true;
                }
            }
        }

        return false;
    }

}
