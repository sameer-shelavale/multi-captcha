<?php

include_once( "../src/Captcha.php");


$captcha = new \MultiCaptcha\Captcha([
    'secret'=>    "form1-secret-key",
] );

if( isset( $_REQUEST['submit'] ) ){
    if( $captcha->validate( $_POST, $_SERVER['REMOTE_ADDR'] ) ) {
        echo "Correct.";
    }else{
        echo "Wrong: ". $captcha->error;
    }
}
?>
<h2>Display a random captcha from types initialized using "<i>options</i>" parameter.</h2>
<form action="" method="post">
    <?php
    echo $captcha->render() ;
    ?>
    <input type="submit" name="submit" value="Submit">

</form>

