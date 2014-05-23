<?php
/**
 * Created by Sameer Shelavale.
 * User: sameer-shelavale
 * Date: 5/12/14
 * Time: 8:26 PM
 */

include_once( 'class.captchafactory.php');

class MultiCaptcha {

    var $secretKey = "{{((o=All-Hands-More-Sail=o))}}"; //you MUST change this in live environment

    var $supportedTypes = array(
        'recaptcha' => 'Recaptcha',    //captcha by google
        'code'      => 'CodeCaptcha',  //user has to type code displayed in captcha image
        'honeypot'  => 'HoneyPot',     //honeypot, mainly for spambots, adds an hidden field which only the spambots fill in
        'checkbox'  => 'Checkbox',     //adds an checkbox which is supposed to be left blank by humans, but bots mark it checked
        'math'      => 'Math',         //Math captcha which asks answer of simple mathematical questions
        'ascii'     => 'Ascii',        //ASCII captcha, displays the captcha in ASCII decorative style
        'animated'  => 'Animated',     //GIF animated captcha
    );

    var $enabledTypes = array();    //multiple types can also be specified,
                                    //in that case the captcha will be randomized from the selected types


    var $customFieldName = null;    //use custom field name for captcha instead of random names
                                    //this is not applicable to Recaptcha
                                    //if you specify custom field, it will also add a extra hidden field which will contain the encrypted code
                                    //this extra field will be named $customFieldName."_challenge"

    function MultiCaptcha( $enableTypes ){
        foreach( $enableTypes as $type ){
            $this->enableType( $type );
        }

    }

    function enableType( $type ){
        $this->enabledTypes[] = $type;
    }


    /*
     * function getHtml()
     * @return html of the captcha code, this is to be inserted in the forms directly
     */
    public function getHtml( $type = null ){
        if( count( $this->enabledTypes ) == 0 ){
            return false;
        }

        if( !$type ){
            //no type specified, use random captcha from enabled types
            $type = array_rand( $this->enabledTypes );
        }elseif( !isset( $this->enabledTypes[ $type ] ) ){
            //selected type of captcha does not exist or is not enabled
            return false;
        }

        $captcha = new $this->enabledTypes[$type];

        return $captcha->getHtml();

    }


    /*
     * function validate()
     * @param $data posted data or array containing the customField value and challangeFieldValue
     *              for example if $customFieldName is "my_captcha_field"
     *              then you can pass array( 'my_captcha_field'=>$_POST['my_captcha_field'], 'my_captcha_field_response'=>$_POST['my_captcha_field_challange'] )
     * @param $remoteAddress remote IP address
     * @return boolean
     */
    public function validate( $data=array(), $remoteAddress = null ){
        $fieldName = $this->customFieldName;

        //if customFieldName is disabled, we are using random field names
        //try find the field name
        if( !$fieldName ){
            $fieldName = $this->getCaptchaFieldName( $data );
        }


        if( !$fieldName ){
            //There is no captcha field in the submitted data,
            //validation fails
            return false;
        }



        return false;
    }


    /*
     * function getCaptchaFieldName()
     *      this function finds the name of chaptcha response field from the submitted data
     *      it does so by trying to decrypt each field name in the submitted form
     * @param $data posted data
     * @return string field name for the captcha
     */
    public function getCaptchaFieldName( $data ){
        foreach( $data as $key => $value ){
            //check if the key matches our particular challange format after decryption
            if( preg_match( "/^([a-zA-Z0-9]+)_([a-zA-Z0-9]{6})_([a-zA-Z0-9]{4,9})$/", $this->decrypt( $key ) ) ){
                //this is captcha field
                return $key;
            }
        }

    }


    public function decrypt( $cipherText ){

        $ivSize = mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC );

        $cipherTextDec = base64_decode( $cipherText );

        # retrieves the IV, iv_size should be created using mcrypt_get_iv_size()
        $ivDec = substr( $cipherTextDec, 0, $ivSize );

        # retrieves the cipher text (everything except the $iv_size in the front)
        $cipherTextDec = substr( $cipherTextDec, $ivSize );

        # may remove 00h valued characters from end of plain text
        return mcrypt_decrypt( MCRYPT_RIJNDAEL_256, $this->secretKey, $cipherTextDec, MCRYPT_MODE_CBC, $ivDec );

    }

}