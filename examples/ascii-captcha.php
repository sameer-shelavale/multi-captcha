<?php

include_once( "../src/captcha.php");


$captcha = new \MultiCaptcha\Captcha([
    'secret'=>    "form1-secret-key",
    'options' =>  [
        'ascii' => [
            'maxCodeLength' => 8,
            'fontPath'=>'../src/types/ascii/fonts/',
            'fonts'=>array(
                'banner'=> 4,
                'doom'=> 8,
                'small'=>'8'
            )
        ]
    ]
] );

if( isset( $_REQUEST['submit'] ) ){
    var_dump( $captcha->validateForm( $_POST, $_SERVER['REMOTE_ADDR'] ) );
}
?>

<form action="" method="post">
    <?php
    echo $captcha->render() ;
    ?>
    <input type="submit" name="submit" value="Submit">

</form>

