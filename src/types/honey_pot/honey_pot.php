<?php
/**
 * Created by PhpStorm.
 * User: Sameer Shelavale
 * Date: 5/14/14
 * Time: 5:39 PM
 */
namespace MultiCaptcha\Types;
use MultiCaptcha\BaseCaptcha;

class HoneyPot extends BaseCaptcha {

    var $description = "If you are human leave this blank";
    var $id = false;
    var $class = false;
    var $style = false;

    var $errorMsg = "You didn't leave the captcha field blank.";

    public function generateQuestion(){
        $answer = '';

        $result['description'] = $this->description;
        $result['answer'] = $answer;
        return $result;
    }


} 