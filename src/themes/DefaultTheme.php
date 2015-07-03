<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 8/4/14
 * Time: 6:55 PM
 */
namespace MultiCaptcha;

class DefaultTheme {
    var $fieldClass = '';

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
                style="border:3px solid #000;
                    border-radius: 5px;
                    padding: 10px;
                    display: table;
                    margin: 2px;
                    background-color: #f69d03;
                    font-family: Arial;
                    font-size: 14px;
                    max-width: 200px" >';
        if( isset( $data['question'] ) && $data['question']['type'] == 'image' ){
            $html .=
                '<div>
                    <img
                        src="'.$data['question']['url'].'"
                        alt=""
                        style="
                            border-radius:3px;
                            margin-bottom:5px;
                        "
                    />
                </div>';
        }

        if( isset( $data['question'] ) && $data['question']['type'] == 'text' ){
            $html .=
                '<div
                    style="font-size:120%;
                    font-weight:bold;">'
                .$data['question']['content']
                .'</div>';
        }

        if( isset( $data['question'] ) && $data['question']['type'] == 'ascii' ){
            $html .=
                '<div
                    style="
                        background-color:#ccc;
                        border-radius:3px;
                    " >'
                .$data['question']['content']
                .'</div>';
        }

        $html .= '<div>
                    <input
                        type="text"
                        name="'.$data['fieldName'].'"
                        title="'.$data['tooltip'].'"
                        style="
                            background-color:#f66a03;
                            border:2px solid #fff;
                            font-size:120%;
                            font-weight:bold;
                            border-radius:3px;
                        "
                        class="'.$this->fieldClass.'"
                    />';

        $html .= $data['hidden'];
        $html .= '</div>';

        $html .= '<div
                    style="font-size:80%;
                    line-height:100%;">'.$data['description'].'</div>';

        if( isset( $data['helpUrl'] ) || isset( $data['helpHtml'] ) || isset( $data['refreshUrl'] ) ){
            $help = '';
            if( isset( $data['helpUrl'] ) ){
                $help = '<a href="'.$data['helpUrl'].'" target="_blank" title="Help">Help</a>';
            }

            $refresh = '';
            if( isset( $data['refreshUrl'] ) ){
                $refresh = '<a href="'.$data['refreshUrl'].'" title="Refresh" onclick="return multicaptcha_refresh();">Refresh</a>';
            }
            $html .= '
                <div>
                    '.$help.$refresh.'
                </div>';
        }


        $html .= '</div>';

        $script = '
        <script type="text/javascript">

            function multicaptcha_refresh(){
                return false;
            }
        </script>
        ';

        return $html;

    }
} 