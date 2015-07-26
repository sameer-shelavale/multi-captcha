<?php

include_once( "../src/Captcha.php");


$captcha = new \MultiCaptcha\Captcha([
    'secret'=>    "form1-secret-key",
    'options' =>  [
        'image' => [
            'maxCodeLength' => 8,
            //'font'=>'../src/fonts/comic.ttf', //you can set your custom font here
            'width'=>180
        ]
    ],
    'refreshUrl'=>'image-captcha.php?captcha=refresh',
    'helpUrl'=>'http://github.com/sameer-shelavale/multi-captcha'
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

<form action="" method="post">
    <?php
    echo $captcha->render() ;
    ?>
    <input type="submit" name="submit" value="Submit">

</form>

