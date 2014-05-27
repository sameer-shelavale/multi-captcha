<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 5/26/14
 * Time: 11:44 PM
 */

class CaptchaCrypt {


    public function encrypt( $captchaCode, $captchaType, $secretKey ){

        $ivSize = mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC );

        $iv = mcrypt_create_iv( $ivSize, MCRYPT_RAND );

        $time =  base_convert( time(), 10, 36 );

        $uid =  base_convert( uniqid(), 10, 36 );

        $plainText = $captchaType.'_'.$captchaCode.'_'.$time.'_'.$uid;

        $cipherTextDec = mcrypt_encrypt( MCRYPT_RIJNDAEL_256, $secretKey, $plainText, MCRYPT_MODE_CBC, $iv ) ;

        return base64_encode( $iv.$cipherTextDec );

    }


    public function decrypt( $cipherText, $secretKey ){

        $ivSize = mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC );

        $cipherTextDec = base64_decode( $cipherText );

        # retrieves the IV, iv_size should be created using mcrypt_get_iv_size()
        $ivDec = substr( $cipherTextDec, 0, $ivSize );

        # retrieves the cipher text (everything except the $iv_size in the front)
        $cipherTextDec = substr( $cipherTextDec, $ivSize );

        # may remove 00h valued characters from end of plain text
        return mcrypt_decrypt( MCRYPT_RIJNDAEL_256, $secretKey, $cipherTextDec, MCRYPT_MODE_CBC, $ivDec );

    }


    public function validate( $decryptedText ){

    }

} 