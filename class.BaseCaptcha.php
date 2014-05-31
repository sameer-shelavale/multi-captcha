<?php
/**
 * Created by PhpStorm.
 * User: Sameer Shelavale
 * Date: 5/14/14
 * Time: 5:41 PM
 * Provides basic functions of captcha class
 */

class BaseCaptcha {

    var $secretKey = '';
    var $life = 10;         //life/validity time of captcha in hours
    var $customFieldName = null;
    var $cipherIsFieldName = true;

    public function BaseCaptcha( $secKey, $captchaLife, $customFieldName = null ){
        $this->secretKey = $secKey;
        $this->life = $captchaLife;
        $this->customFieldName = $customFieldName;
    }

    /*
     * function getHtml()
     * @return html of the captcha code, this is to be inserted in the forms directly
     */
    public function getHtml(){
        return '';
    }


    /*
     * function validate()
     * @param $data posted data or array containing the customField value and challangeFieldValue
     *              for example if $customFieldName is "my_captcha_field"
     *              then you can pass array( 'my_captcha_field'=>$_POST['my_captcha_field'], 'my_captcha_field_response'=>$_POST['my_captcha_field_challange'] )
     * @param $remoteAddress remote IP address
     * @return boolean
     */
    public function validateForm( $formData=array(), $remoteAddress = null ){

        return false;
    }


    /*
     * function getCaptchaField()
     * @param $data posted data
     * @return string field name for the captcha
     */
    public function getCaptchaField( $data ){

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

        return base64_encode( $iv.$cipherTextDec );

    }


    public function decrypt( $cipherText ){

        $ivSize = mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC );

        $cipherTextDec = base64_decode( $cipherText );

        if( !$cipherTextDec ){
            //cipherText is not base64 encoded so its invalid/corrupt
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


    public function validate( $cipherText, $answer  ){
        $plainText = self::decrypt( $cipherText, $this->secretKey );

        if( !$plainText ){
            //cipherText is not base64 encoded so its invalid/corrupt
            return false;
        }

        if( preg_match( "/^([a-zA-Z0-9]{4,9})_([a-zA-Z0-9]{6})_([a-zA-Z0-9]*)_([a-zA-Z0-9]+)$/", $plainText, $matches ) ){
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

    public function isAnsweredRecently(){
        return false;
    }

    public function recordSuccessfulAnswer(){

    }

} 