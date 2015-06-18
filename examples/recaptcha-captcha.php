<?php

include_once( "../src/captcha.php");


$captcha = new \MultiCaptcha\Captcha([
    'secret'=>    "form1-secret-key",
    'options' =>  [
        'recaptcha' => [
            'publicKey'=> "6Le1XPQSAAAAAPteoelqqH7JQzKGOikcTc3HrpAA",
            'privateKey' => "6Le1XPQSAAAAABu9LOXfj6Wxvec_cQFXCHjOJzG3"
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

