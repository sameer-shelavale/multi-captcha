<?php
/**
 * Created by PhpStorm.
 * User: Sameer Shelavale
 * Date: 6/3/14
 * Time: 5:43 PM
 * This class creates CAPTCHA using ASCII text.
 * It uses Figlet fonts by http://figlet.org
 * the PhpFiglet Class is property of Lucas Baltes
 *
 */
namespace MultiCaptcha\Types;
use MultiCaptcha\BaseCaptcha;
use MultiCaptcha\Types\Ascii\PhpFiglet;

class Ascii extends BaseCaptcha {

    var $minCodeLength = 4; // minimum length of code displayed on captcha image
    var $maxCodeLength = 10; // maximum length of code displayed on captcha image( max value is 20 )
    var $maxRequired = 5;   // maximum number of characters that can be asked to identify
    var $minRequired = 3;   // minimum number of characters that can be asked to identify

    var $fontPath;
    var $fonts = array(
        'standard' => 10,   //fontname as key and fontsize as value
    );
    var $maxFontSize = 12;
    var $minFontSize = 5;

    var $errorMsg = "You didn't type the code accurately.";

    private static $numberTypes = array(
        array( '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '17', '16', '18', '19', '20'),
        array( 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen', 'twenty'),
        array( 'first', 'second', 'third', 'fourth', 'fifth', 'sixth', 'seventh', 'eighth', 'ninth', 'tenth', 'eleventh', 'twelfth', 'thirteenth', 'fourteenth', 'fiftheenth', 'sixteenth', 'seventeenth', 'eighteenth', 'nineteenth', 'twentieth'),
        array( '1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th', '11th', '12th', '13th', '14th', '15th', '17th', '16th', '18th', '19th', '20th')
    );



    function generateQuestion( ){

        $codeLength = rand( $this->minCodeLength, $this->maxCodeLength );

        $code = $this->generateCode( $codeLength ); //code to be displayed in image

        $type = rand( 1,3);
        switch( $type ){
            case 1:
                //ast for characters at starting position
                $required = rand( $this->minRequired, min( $this->maxRequired, $codeLength ) );
                $answer = substr( $code, 0, $required );
                $desc = array(
                        "Please enter $required characters at start of the above code",
                        "Please enter first $required characters of the above code",
                );
                $questionText = $desc[ array_rand( $desc ) ];
                break;
            case 2:
                //ask for characters at end
                $required = rand( $this->minRequired, min( $this->maxRequired, $codeLength )  );
                $answer = substr( $code, 0-$required );
                $desc = array(
                        "Please enter $required characters at end of the above code",
                        "Please enter last  $required characters of above code",
                );
                $questionText = $desc[ array_rand( $desc ) ];
                break;
            case 3:
            default:
                //ask for characters at random position

                $required = rand( $this->minRequired, min( $this->maxRequired, $codeLength ) );
                $ansSet = array();
                $pool = array();

                $numType = rand( 0, 3 ); // from $this->numberTypes
                for( $i=1; $i <= $codeLength; $i++ ){
                    $pool[] = $i;
                }
                for( $i=1; $i <= $required; $i++ ){
                    $tmp = array_rand( $pool );
                    $idx = $pool[$tmp];
                    $ansSet[] = $idx;
                    unset( $pool[$tmp] );
                }
                sort( $ansSet );
                $answer = '';
                foreach( $ansSet as $key=>$ans ){
                    $ansSet[$key] = self::$numberTypes[ $numType ][ $ans-1];
                    $answer .= $code[ $ans-1 ];
                }
                $ansSet = implode( ', ', $ansSet );
                if( $numType == 0 || $numType == 1 ){
                    $questionText = "Please enter characters at position {$ansSet} in the above code";
                }else{
                    $questionText = "Please enter {$ansSet} characters in the above code";
                }
                break;
        }

        $result['question']['type'] = 'ascii';
        $result['question']['content'] = $this->getFiglet( $code );
        $result['description'] = $questionText;
        $result['answer'] = $answer;

        return $result;
    }

    function generateCode( $length ){
        $set = 'abcdefghijkmnopqrstuvwxyz123456789'; //some capital letters and 'l','0' are removed to avoid confusion
        $result = '';
        for( $i=0; $i < $length; $i++ ){
            $result .= $set[ rand(0, strlen( $set )-1) ];
        }
        return $result;
    }


    function getFiglet( $code ) {
        $phpFiglet = new PhpFiglet();
        $font =  array_rand( $this->fonts );
        if( $this->fontPath && is_dir( $this->fontPath )){
            $path = rtrim( $this->fontPath ).'/';
        }else{
            $path = rtrim( __DIR__,'/').'/fonts/';
        }
        if ($phpFiglet->loadFont(  $path.$font.'.flf' ) ) {
            $result = $phpFiglet->fetch( $code );
        } else {
            return false;
        }

        return '<pre style="font-size:'.$this->fonts[$font].'px">'.$result.'</pre>';
    }



} 