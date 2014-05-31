<?php
/**
 * Created by PhpStorm.
 * User: Sameer Shelavale
 * Date: 5/14/14
 * Time: 5:39 PM
 */
include_once( 'recaptchalib.php' );

class HoneyPot extends BaseCaptcha {

    var $description = "If you are human leave this blank";
    var $id = false;
    var $class = false;
    var $style = false;

    public function getHtml(){

        $answer = '';   //only bots will fill in answer

        $cipher = $this->encrypt( $answer, 'honeypot' );

        if( $this->customFieldName ) {
            $fieldName = $this->customFieldName;
        }else{
            //use cipher as field name
            $fieldName = $cipher;
        }

        if( $this->id ){
            $id = "id=\"{$this->id}\"";
        }

        if( $this->class ){
            $class = "class=\"{$this->class}\"";
        }

        if( $this->style ){
            $style = "style=\"{$this->style}\"";
        }

        if( $fieldName == $cipher ){
            $html = '<input type="text" name="'.$fieldName.'" value="" '.$id.' '.$class.' '.$style.' /> ';
            $html .= $this->description;
        }else{
            $html = '<input type="text" name="'.$fieldName.'" value="" '.$id.' '.$class.' '.$style.' /> ';
            $html .= $this->description;
            $html .= '<input type="hidden" name="'.$fieldName.'_challenge" value="'.$cipher.'" /> ';
        }

        return $html;
    }


} 