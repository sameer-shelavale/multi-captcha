<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 8/4/14
 * Time: 6:55 PM
 */
namespace MultiCaptcha\Themes;

class DefaultTheme {
    var $fieldClass = '';
    var $fieldStyle = 'background-color:#f66a03; border:2px solid #fff; font-size:120%; font-weight:bold; border-radius:3px;width:144px;';
    var $containerStyle = 'border:3px solid #000; border-radius: 5px; padding: 10px; display: table; margin: 2px; background-color: #f69d03; font-family: Arial; font-size: 14px; max-width: 180px;position:relative;';
    var $questionImageStyle = 'border-radius:3px; margin-bottom:5px;';
    var $questionTextStyle = 'font-size:120%; font-weight:bold;background-color:#ccc; border-radius:3px; padding:4px;margin-bottom:2px;text-align:center;display:block;';
    var $questionAsciiStyle = 'background-color:#ccc; border-radius:3px; padding:4px;margin-bottom:2px;text-align:center;display:block;';
    var $questionContainerStyle = '';
    var $labelStyle = 'font-size:80%; line-height:100%;';

    var $helpBtnClass = 'btn-help';
    var $helpBtnText = '?';
    var $refreshBtnClass = 'btn-refresh';
    var $refreshBtnText = '&#8634;';
    var $extraHtml = <<<'EOT'
<style type="text/css">
a.btn-refresh, a.btn-help{
    background-color:#fff;
    text-decoration:none;
    color:#f66a03;
    padding:1px 2px;
    border-radius:2px;
    vertical-align:top;
    margin-left:2px;
    display:inline-block;
    width:12px;
    height:12px;
    text-align:center;
    line-height:100%;
    font-size:12px;
}
</style>
EOT;



    function __construct( $customValues ){
        foreach( $customValues as $key => $val ){
            if( property_exists( get_class( $this ), $key ) ){
                $this->$key = $val;
            }
        }
    }


    function render( $data ){
        $html =
            '<div
                style="'.$this->containerStyle.'" >';

        $html .= '<div style="'.$this->questionContainerStyle.'">'. $this->renderChallenge( $data ).'</div>';

        $html .= '<div>'.$this->renderResponseField( $data ).$this->renderTools( $data ).'</div>';

        $html .= '<div>'.$this->renderLabel( $data ).'</div>';



        $html .= '</div>';
        $html .= $this->renderRefreshScript( $data );
        $html .= $this->extraHtml;

        return $html;

    }

    function renderChallenge( $data ){
        $html = '';
        if( isset( $data['question'] ) && $data['question']['type'] == 'image' ){
            $html .= '<img src="'.$data['question']['content'].'" style="'.$this->questionImageStyle.'"/>';
        }elseif( isset( $data['question'] ) && $data['question']['type'] == 'text' ){
            $html .= '<span style="'.$this->questionTextStyle.'">'.$data['question']['content'].'</span>';
        }elseif( isset( $data['question'] ) && $data['question']['type'] == 'ascii' ){
            $html .= '<span style="'.$this->questionAsciiStyle.'" >'.$data['question']['content'].'</span>';
        }
        return $html;
    }

    function renderLabel( $data ){
        return '<span style="'.$this->labelStyle.'">'.$data['description']. '</span>';
    }

    function renderResponseField( $data ){
        $html = '<input type="text" name="'.$data['fieldName'].
                    '"style="'.$this->fieldStyle.'" class="'.$this->fieldClass;
        if( isset( $data['tooltip'] ) ){
            $html .= 'title="'.$data['tooltip'].'"';
        }
        $html .= '/>';
        if( isset( $data['hidden'] ) ){
            $html .= $data['hidden'];
        }
        return $html;
    }

    function renderTools( $data ){

        $help = '';
        if( isset( $data['helpUrl'] ) && strlen( $data['helpUrl'] ) > 0){
            $help = '<a href="'.$data['helpUrl'].'" target="_blank" title="Help" class="'.$this->helpBtnClass.'">'.$this->helpBtnText.'</a>';
        }

        $refresh = '';
        if( isset( $data['refreshUrl'] ) && strlen( $data['refreshUrl'] ) > 0 ){
            $refresh = '<a href="'.$data['refreshUrl'].'" title="Refresh"  class="'.$this->refreshBtnClass.'" onclick="return captcha_refresh(this);">'.$this->refreshBtnText.'</a>';
        }
        return  $refresh.$help;
    }

    function renderRefreshScript( $data ){
        $script = <<<"EOT"
<script type="text/javascript">
    function captcha_refresh( btnObj ) {
        var AJAX = null;
        if (window.XMLHttpRequest) {
            AJAX=new XMLHttpRequest();
        } else {
            AJAX=new ActiveXObject("Microsoft.XMLHTTP");
        }
        if (AJAX==null) {
            return false;
        }

        AJAX.onreadystatechange = function() {
            if (AJAX.readyState==4) {
                //update captcha html
                btnObj.parentElement.parentElement.parentElement.outerHTML = this.responseText;
                AJAX=null;
            }
        }

        AJAX.open("GET", '{$data['refreshUrl']}', true);
        AJAX.send(null);
        return false;
    }

</script>
EOT;
        return $script;
    }


    /*
     * returns the data for refreshing the captcha as json encoded array
     */
    function renderRefresh( $data ){

    }
} 