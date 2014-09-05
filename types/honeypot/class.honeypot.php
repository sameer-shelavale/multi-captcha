<?php
/**
 * Created by PhpStorm.
 * User: Sameer Shelavale
 * Date: 5/14/14
 * Time: 5:39 PM
 */
namespace MultiCaptcha;
include_once( 'recaptchalib.php' );

class HoneyPot extends BaseCaptcha {

    var $description = "If you are human leave this blank";
    var $id = false;
    var $class = false;
    var $style = false;

    public function generateQuestion(){
        $answer = '';

        $result['description'] = $this->description;
        $result['answer'] = $answer;
        $result['cipher'] = $this->encrypt( $answer, 'honeypot' );
        return $result;
    }


} 