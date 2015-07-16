<?php

include_once( "../src/Captcha.php");
include_once( "../vendor/autoload.php");//not required if you have PSR-4 autoloader


$captcha = new \MultiCaptcha\Captcha([
    'secret'=>    "form1-secret-key",
    'options' =>  [
        'nocaptcha' => [
            'siteKey'=> "6LfObQkTAAAAADPFtykBvYkNegv2lkGjThGxEvqC", // replace this with your site key
            'secretKey' => "6LfObQkTAAAAADnUOa3Ry5H_6_Iymt-8-RFDQGdH" // replace this with your secret key
        ]
    ],
    'refreshUrl'=>'ascii-captcha.php?captcha=refresh',
    'helpUrl'=>'http://github.com/sameer-shelavale/multi-captcha'
] );


if( isset( $_REQUEST['submit'] ) ){
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

