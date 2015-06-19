<?php

include_once( "../src/captcha.php");


$captcha = new \MultiCaptcha\Captcha([
    'secret'=>    "form1-secret-key",
] );

if( isset( $_REQUEST['submit'] ) ){
    var_dump( $captcha->validate( $_POST, $_SERVER['REMOTE_ADDR'] ) );
}
?>
<h2>Display a random captcha from types initialized using "<i>options</i>" parameter.</h2>
<form action="" method="post">
    <?php
    echo $captcha->render() ;
    ?>
    <input type="submit" name="submit" value="Submit">

</form>

