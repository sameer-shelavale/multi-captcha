<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 5/22/14
 * Time: 5:24 PM
 */
//Public Key: 	6Le1XPQSAAAAAPteoelqqH7JQzKGOikcTc3HrpAA
//Private Key: 	6Le1XPQSAAAAABu9LOXfj6Wxvec_cQFXCHjOJzG3


include_once( "../class.multiCaptcha.php");


$captcha = new MultiCaptcha(
    "form1-secret-key",
    /*array(
        'honeypot' => array(
            'description'=> "Get lost you bot",
            'class' => 'error'
        )
    )*/
    array(
        'math' => array(
            'description'=> "Answer following question if you are human",
            'level' => 4
        )
    )
);

if( isset( $_REQUEST['submit'] ) ){
    var_dump( $captcha->validateForm( $_POST, $_SERVER['REMOTE_ADDR'] ) );
}
?>

<form action="<?php echo $_SERVER['PHPSELF']; ?>" method="post">
    <?php
    echo $captcha->getHtml() ;
    ?>
    <input type="submit" name="submit" value="Submit">

</form>

