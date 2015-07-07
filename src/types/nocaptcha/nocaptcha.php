<?php
/**
 * Created by PhpStorm.
 * User: Sameer Shelavale
 * Date: 5/14/14
 * Time: 5:39 PM
 */
namespace MultiCaptcha\Types;
use MultiCaptcha\BaseCaptcha;

class Nocaptcha extends BaseCaptcha {
    var $siteKey = false;
    var $secretKey = false;
    var $lang = 'en';

    var $errorMsg = "You didn't type the code accurately.";

    public function generateQuestion(){

        $result['html'] = '<div class="g-recaptcha" data-sitekey="'. $this->siteKey .'"></div>
            <script type="text/javascript"
                    src="https://www.google.com/recaptcha/api.js?hl='.$this->lang.'">
            </script>';
        return $result;
    }


    public function verify( $postData, $remoteAddress ){
        if( ini_get( 'allow_url_fopen' ) ){
            $recaptcha = new \ReCaptcha\ReCaptcha( $this->secretKey );
        }else{
            // If file_get_contents() is locked down on your PHP installation to disallow
            // its use with URLs, then you can use the alternative request method instead.
            // This makes use of fsockopen() instead.
            $recaptcha = new \ReCaptcha\ReCaptcha($this->secretKey, new \ReCaptcha\RequestMethod\SocketPost());
        }


        // Make the call to verify the response and also pass the user's IP address
        $resp = $recaptcha->verify( $postData['g-recaptcha-response'], $remoteAddress );

        if ( $resp->isSuccess() ) {
            return true;
        }

        return false;
    }

} 