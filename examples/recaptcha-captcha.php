<?php

include_once( "../src/captcha.php");


$captcha = new \MultiCaptcha\Captcha([
    'secret'=>    "form1-secret-key",
    'options' =>  [
        'recaptcha' => [
            'publicKey'=> "6Le1XPQSAAAAAPteoelqqH7JQzKGOikcTc3HrpAA", // replace this with your public key
            'privateKey' => "6Le1XPQSAAAAABu9LOXfj6Wxvec_cQFXCHjOJzG3" // replace this with your private key
        ]
    ]
] );


if( isset( $_REQUEST['submit'] ) ){
    var_dump( $captcha->validate( $_POST, $_SERVER['REMOTE_ADDR'] ) );
}
?>

<form action="" method="post">
    <?php
    echo $captcha->render() ;
    ?>
    <input type="submit" name="submit" value="Submit">

</form>

