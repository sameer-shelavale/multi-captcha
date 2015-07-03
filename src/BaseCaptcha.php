<?php
/**
 * Created by PhpStorm.
 * User: Sameer Shelavale
 * Date: 5/14/14
 * Time: 5:41 PM
 * Provides basic functions of captcha class
 */
namespace MultiCaptcha;

class BaseCaptcha {
    var $language = 'en';
    var $tooltip = array();
    var $helpHtml = array();


    var $secretKey = '';
    var $life = 1;         //life/validity time of captcha in hours
    var $customFieldName = null;
    var $cipherIsFieldName = true;
    var $theme = 'Default';
    var $themeOptions = array();

    public function __construct( $secKey, $captchaLife, $customFieldName = null ){
        $this->secretKey = $secKey;
        $this->life = $captchaLife;
        $this->customFieldName = $customFieldName;
    }

    /*
     * function data()
     * @return  the details of the generated captcha as array
     *          the array includes fields like cipher, question, customFieldName, tooltip etc.
     *          This data in turn is used for rendering the captcha
     */
    public function data(){

        $data = $this->generateQuestion();

        $data['cipher'] = $this->encrypt( $data['answer'], 'math' );

        $data['customFieldName'] = $this->customFieldName;
        if( is_array( $this->tooltip ) ){
            $data['tooltip'] = isset( $this->tooltip[ $this->language ] )? $this->tooltip[ $this->language ]: '';
        }elseif( is_string( $this->tooltip ) ){
            $data['tooltip'] = $this->tooltip;
        }

        $data['helpHtml'] = isset( $this->helpHtml[ $this->language ] )? $this->helpHtml[ $this->language ]: '';

        if( $data['customFieldName'] ){
            //custom fieldName is set
            $data['fieldName'] = $data['customFieldName'];
            $data['hidden'] = '<input type="hidden" name="'.$data['fieldName'].'_challenge" value="'.$data['cipher'] .'" /> ';
        }else{
            $data['fieldName'] = $data['cipher'];
            $data['hidden'] = '';
        }

        return $data;
    }

    /*
     * function render()
     *      renders the captcha using the selected theme
     * @returns the html for rendering the captcha
     */

    public function render(){

        $themeName = $this->theme. 'Theme' ;
        include_once( dirname( __FILE__ ).'/themes/'.$themeName.'.php' );
        if( class_exists( $themeName ) ){
            $themeObj = new $themeName( $this->themeOptions );
        }else{
            $themeObj = new DefaultTheme( $this->themeOptions );
        }
        return $themeObj->render( $this->data() );
    }


    public function setOptions( $options = array() ){

        foreach( $options as $key => $value ){
            if( property_exists( $this, $key ) ){
                $this->$key = $value;
            }
        }
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


    public function verify( $cipherText, $answer  ){
        $plainText = self::decrypt( $cipherText );

        if( !$plainText ){
            //cipherText is not base64 encoded so its invalid/corrupt
            return false;
        }

        if( preg_match( "/^([a-zA-Z0-9]{4,9})_([a-zA-Z0-9]{6})_([a-zA-Z0-9\-]*)_([a-zA-Z0-9]+)$/", $plainText, $matches ) ){
            $uid = base_convert( $matches[1], 36, 10 );
            $time = base_convert( $matches[2], 36, 10 );
            $correctAnswer = $matches[3];
            $type = $matches[4];

            //check if the captcha is too old
            $age = time() - $time;
            if( $age > $this->life * 3600 || $age < 0 ){
                return false;
            }


            //check if answer is correct
            if( $answer != $correctAnswer ){
                return false;
            }

            //check if this captcha is answered successfully recently
            if( $this->isAnsweredRecently() ){
                //this captcha has been answered successfully recently
                //or it can not be saved in log of recent answers right now
                return false;
            }else{
                $this->recordSuccessfulAnswer();
                return true;
            }

        }
        return false;
    }

    /***********************************************************************
     * function generateQuestion()
     * @return array() returns captcha question data as array.
     * following elements are required to be passed
     *      question    - will contain html of any image, video or even main text of the question
     *      description - will be text description of the question telling what is expected
     *      answer      - the correct answer
     *      tooltip     - will contain help text for solving the captcha
     *      refreshUrl  - url to refresh the captcha
     *
     ***********************************************************************/
    public function generateQuestion(){
        return array(
            'question'  => null,
            'description'=>null,
            'answer'    => null,
            'tooltip'   => null,
            'refreshUrl'=> null
        );
    }

    public function isAnsweredRecently(){
        return false;
    }

    public function recordSuccessfulAnswer(){

    }


} 