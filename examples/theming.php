<?php

include_once( "../src/Captcha.php");


$captcha = new \MultiCaptcha\Captcha([
    'secret'=>    "form1-secret-key",
    'options' =>  [
        'math' => array(
            'description'=> "Answer following question if you are human",
            'level' => 4
        ),
        'image' => array(
            'maxCodeLength' => 8,
            'font'=>'../src/types/image/comic.ttf',
            'width'=>180,
            'themeOptions' => [
                'containerStyle' => 'border:1px solid #0f702d; border-radius: 5px; padding: 5px; display: table; margin: 2px; background-color: #29713f; font-family: Helvetica; font-size: 14px; max-width: 180px;position:relative;',
                'fieldStyle' => 'background-color:#52785e; border:2px solid #fff; color:#fff; font-size:120%; font-weight:bold; border-radius:3px;width:144px;',
                'labelStyle' => 'font-size:80%; line-height:100%; color: #fff;',
            ]
        ),
        'ascii' => array(
            'maxCodeLength' => 8,
            'fonts'=>array(
                'banner'=> 4,
                'doom'=> 8,
                'small'=>'8'
            ),
            'themeOptions' => [
                'containerStyle' => 'border:1px solid #1e2a37; border-radius: 5px; padding: 10px; display: table; margin: 2px; background-color: #374c63; font-family: Helvetica; font-size: 14px; max-width: 180px;position:relative;',
                'fieldStyle' => 'background-color:#4d5d6f; border:2px solid #fff; color:#fff; font-size:120%; font-weight:bold; border-radius:3px;width:144px;',
                'labelStyle' => 'font-size:80%; line-height:100%; color: #fff;',

            ]
        ),
        'gif' => array(
            'maxCodeLength' => 8,
            'font'=>'../src/types/image/comic.ttf',
            'width'=>180,
            'height'=>60,
            'totalFrames'=>50,
            'delay'=>20,
            'themeOptions' => [
                'containerStyle' => 'border:4px solid #05531d; border-radius: 5px; padding: 10px; display: table; margin: 2px; background-color: #29713f; font-family: Helvetica; font-size: 14px; max-width: 180px;position:relative;',
                'fieldClass' => 'c-field',
                'fieldStyle' => 'text-align:center; font-size:120%; font-weight:bold; border-radius:5px;width:144px;',
                'labelStyle' => 'font-size:80%; line-height:100%; color: #bfd7c6;',
                'extraHtml' => '<style type="text/css">
                                a.btn-refresh, a.btn-help{
                                    background-color:#76be8c;
                                    text-decoration:none;
                                    color:#05531d;
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
                                    cursor:pointer;
                                }
                                a.btn-refresh:hover, a.btn-help:hover{
                                    background-color: #82c797;

                                }
                                input.c-field{
                                    background-color:#36874f;
                                    border:2px solid #76be8c;
                                    color:#fff;
                                }
                                input.c-field:hover{
                                    background-color: #347c4a;
                                }
                                input.c-field:focus{
                                    background-color: #508861;
                                    border:2px solid #97c5a5;
                                }

                                </style>',
            ]
        ),

    ],
    'refreshUrl'=>'random.php?captcha=refresh',
    'helpUrl'=>'http://github.com/sameer-shelavale/multi-captcha',

] );

if( isset($_GET['captcha']) &&  $_GET['captcha'] == 'refresh' ){
    echo $captcha->refresh();
    exit;
}elseif( isset( $_REQUEST['submit'] ) ){
    if( $captcha->validate( $_POST, $_SERVER['REMOTE_ADDR'] ) ) {
        echo "Correct.";
    }else{
        echo "Wrong: ". $captcha->error;
    }
}
?>
<h2>Customizing the default theme using "<i>themeOptions</i>".</h2>
<form action="" method="post">
    <?php
    echo $captcha->render() ;
    ?>
    <input type="submit" name="submit" value="Submit">

</form>

