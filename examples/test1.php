<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 5/22/14
 * Time: 5:24 PM
 */
//Public Key: 	6Le1XPQSAAAAAPteoelqqH7JQzKGOikcTc3HrpAA
//Private Key: 	6Le1XPQSAAAAABu9LOXfj6Wxvec_cQFXCHjOJzG3


include_once( "../src/captcha.php");


$captcha = new \MultiCaptcha\Captcha([
    'secret'=>    "form1-secret-key",
    'options' =>  [
    /*
        'honeypot' => array(
            'description'=> "Leave this field empty if you are human",
            'class' => 'error'
        ),
        'math' => array(
            'description'=> "Answer following question if you are human",
            'level' => 4
        ),*/
        'image' => array(
            'maxCodeLength' => 8,
            'font'=>'../src/types/image/comic.ttf',
            'width'=>180
        )/*


        'ascii' => array(
            'maxCodeLength' => 8,
            'fontPath'=>'../src/types/ascii/fonts/',
            'fonts'=>array(
                'banner'=> 4,
                'doom'=> 8,
                'small'=>'8'
            )
        ),
        'gif' => array(
            'maxCodeLength' => 8,
            'font'=>'../src/types/image/comic.ttf',
            'width'=>180,
            'height'=>60,
            'totalFrames'=>50,
            'delay'=>20
        )
*/
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

