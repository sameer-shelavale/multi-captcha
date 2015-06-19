<?php
/**
 * Created by PhpStorm.
 * User: Sameer Shelavale
 * Date: 5/14/14
 * Time: 5:39 PM
 */
namespace MultiCaptcha\Types;
use MultiCaptcha\BaseCaptcha;

include_once('recaptchalib.php');

class Recaptcha extends BaseCaptcha {
    var $publicKey = false;
    var $privateKey = false;

    public function render(){
        return recaptcha_get_html( $this->publicKey );
    }


    public function verify( $postData, $remoteAddress ){
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