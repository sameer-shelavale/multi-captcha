<?php

include_once( "../src/captcha.php");


$captcha = new \MultiCaptcha\Captcha([
    'secret'=>    "form1-secret-key",
    'options' =>  [
        'ascii' => [
            'maxCodeLength' => 8,
            'fonts'=>array(
                'banner' => 4, //render with font size 4px or it becomes too big
                'doom' => 8, //render with font size 8px
                'small' =>'8' //render with font size 8px, "small" font is at src/types/ascii/fonts/small.flf
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

