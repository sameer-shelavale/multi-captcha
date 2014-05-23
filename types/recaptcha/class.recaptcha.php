<?php
/**
 * Created by PhpStorm.
 * User: Sameer Shelavale
 * Date: 5/14/14
 * Time: 5:39 PM
 */

class Recaptcha extends CaptchaFactory {
    var $publicKey = false;
    var $privateKey = false;

    public function getHtml(){
        return recaptcha_get_html( $this->publicKey );
    }


    public function validate( $postData, $remoteAddress ){
        $resp = recaptcha_check_answer(
            $this->privateKey,
            $remoteAddress,
            $postData["recaptcha_challenge_field"],
            $postData["recaptcha_response_field"]
        );

        if ( $resp->is_valid ) {
            return true;
        }

        return false;
    }

} 