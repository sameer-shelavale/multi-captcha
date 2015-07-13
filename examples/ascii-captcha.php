<?php

include_once( "../src/captcha.php");


$captcha = new \MultiCaptcha\Captcha([
    'secret'=>    "form1-secret-key",
    'options' =>  [
        'ascii' => [
            'maxCodeLength' => 8,
            'fonts'=>array(
                'banner' => 4, //render with font size 4px or it becomes too big
                'doom' => 5, //render with font size 8px
                'small' =>8 //render with font size 8px, "small" font is at src/types/ascii/fonts/small.flf
            )
        ]
    ],
    'refreshUrl'=>'ascii-captcha.php?captcha=refresh',
    'helpUrl'=>'http://github.com/sameer-shelavale/multi-captcha'
] );

if( isset($_GET['captcha']) &&  $_GET['captcha'] == 'refresh' ){
    echo $captcha->render();
    exit;
}elseif( isset( $_REQUEST['submit'] ) ){
    if( $captcha->validate( $_POST, $_SERVER['REMOTE_ADDR'] ) ) {
        echo "Correct.";
    }else{
        echo "Wrong: ". $captcha->error;
    }
}
?>

<form action="" method="post">
    <?php
    echo $captcha->render() ;
    ?>
    <input type="submit" name="submit" value="Submit">

</form>

