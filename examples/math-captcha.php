<?php

include_once( "../src/captcha.php");


$captcha = new \MultiCaptcha\Captcha([
    'secret'=>    "form1-secret-key",
    'options' =>  [
        'math' => [
            'description'=> "Answer following question if you are human",
            'level' => 4
        ]
    ],
    'refreshUrl'=>'math-captcha.php?captcha=refresh',
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

