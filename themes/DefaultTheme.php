<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 8/4/14
 * Time: 6:55 PM
 */
namespace MultiCaptcha;

class DefaultTheme {
    var $options = array();

    function DefaultTheme( $customValues ){

        $this->options = array_merge( $this->options, $customValues );

    }


    function render( $data ){
        return
            '<div id="MultiCaptchaWrapper">
                <div id="MultiCaptchaGraphic">
                    <img src="" alt="" />
                </div>
                <div id="MultiCaptchaTools">
                    <a href="" title="Help">test1</a>
                    <a href="" title="Refresh">test2</a>
                </div>
                <div id="MultiCaptchaQuestion">

                </div>
                <div id="MultiCaptchaInput">
                    <input type="text" name="" />
                </div>
            </div>';
    }
} 