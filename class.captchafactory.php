<?php
/**
 * Created by PhpStorm.
 * User: Sameer Shelavale
 * Date: 5/14/14
 * Time: 5:41 PM
 */

class CaptchaFactory {


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
    public function validate( $data=array(), $remoteAddress = null ){

        return false;
    }


    /*
     * function getCaptchaField()
     * @param $data posted data
     * @return string field name for the captcha
     */
    public function getCaptchaField( $data ){

    }
} 