<?php
/**
 * Created by Sameer Shelavale.
 * User: sameer-shelavale
 * Date: 5/12/14
 * Time: 8:26 PM
 */

class MultiCaptcha {

    var $secretKey = "{{((o=All-Hands-More-Sail=o))}}"; //you MUST change this in live environment

    var $supportedCaptchaTypes = array(
        'recaptcha',    //captcha by google
        'code',         //user has to type code displayed in captcha image
        'honeypot',     //honeypot, mainly for spambots, adds an hidden field which only the spambots fill in
        'checkbox',     //adds an checkbox which is supposed to be left blank by humans, but bots mark it checked
        'math',         //Math captcha which asks answer of simple mathematical questions
        'ascii',        //ASCII captcha, displays the captcha in ASCII decorative style
        'animated',     //GIF animated captcha
    );

    var $enabledCaptchaTypes = array();    //multiple types can also be specified,
                                    //in that case the captcha will be randomized from the selected types


    function MultiCaptcha( $enableTypes ){
        foreach( $enableTypes as $type ){
            if( !in_array( $type,  $this->enabledCaptchaTypes ) ){
                include_once( 'class.'.$type.'.php' );
                $this->enabledCaptchaTypes[] = $type;

            }
        }

    }
    /*
     * function captcha()
     * @return html of the captcha code, this is to be inserted in the forms directly
     */
    public function captcha(){

    }


    /*
     * function validate()
     * @param $fieldName name of the captcha field
     * @param $fieldValue value of the captcha field as typed by the user
     * @return boolean
     */
    public function validate( $fieldName, $fieldValue ){

        return false;
    }


    /*
     * function validate()
     * @param $data posted data
     * @return string field name for the captcha
     */
    public function getCaptchaField( $data ){

    }

}