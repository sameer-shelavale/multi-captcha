<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 6/3/14
 * Time: 5:43 PM
 */

namespace MultiCaptcha\Types;
use MultiCaptcha\BaseCaptcha;

class Image extends BaseCaptcha {

    var $minCodeLength = 4; // minimum length of code displayed on captcha image
    var $maxCodeLength = 10; // maximum length of code displayed on captcha image( max value is 20 )
    var $maxRequired = 5;   // maximum number of characters that can be asked to identify
    var $minRequired = 3;   // minimum number of characters that can be asked to identify

    var $noiseLevel = 25;   // number of background noisy characters
    var $width      = 150;  // width of image in pixels
    var $height     = 40;   // height of the image in pixels

    var $font = 'comic.ttf';
    var $maxFontSize = 15;
    var $minFontSize = 13;

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
                        "Please enter $required characters at start of the code in image",
                        "Please enter first $required characters of code in image",
                );
                $questionText = $desc[ array_rand( $desc ) ];
                break;
            case 2:
                //ask for characters at end
                $required = rand( $this->minRequired, min( $this->maxRequired, $codeLength )  );
                $answer = substr( $code, 0-$required );
                $desc = array(
                        "Please enter $required characters at end of the code in image",
                        "Please enter last  $required characters of code in image",
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
                    $questionText = "Please enter characters at position {$ansSet} in the image";
                }else{
                    $questionText = "Please enter {$ansSet} characters in the image";
                }
                break;
        }

        $result['question']['type'] = 'image';
        $result['question']['content'] = $this->getImage( $code );
        $result['description'] = $questionText;
        $result['answer'] = $answer;


        return $result;
    }

    function generateCode( $length ){
        $set = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $result = '';
        for( $i=0; $i < $length; $i++ ){
            $result .= $set[ rand(0, strlen( $set )-1 ) ];
        }
        return $result;
    }


    function getImage( $code,  $noise = true ) {
        $codeLength = strlen( $code );

        //calculate font sizes so that things fit in on image
        $this->maxFontSize = $this->width / ( $codeLength + 1 );
        if( $this->maxFontSize > $this->height * 2 / 3 ){
            $this->maxFontSize > intval( $this->height * 2 / 3 );
        }
        $this->minFontSize = $this->maxFontSize*4/5;
        $textPositionY = $this->maxFontSize + ( $this->height - $this->maxFontSize )/2;
        $textPositionX = $this->maxFontSize/2 - 2;


        $image = imagecreatetruecolor(
            $this->width,
            $this->height
        );

        //select random background color
        //200-255 range ensures its lighter color
        $back = ImageColorAllocate(
            $image,
            intval( rand(200,255) ),
            intval( rand(200,255) ),
            intval( rand(200,255) )
        );

        //create a rectangle with random background color
        ImageFilledRectangle(
            $image,
            0,
            0,
            $this->width,
            $this->height,
            $back
        );

        if( $noise ){
            // add random characters in background with random position, angle, color
            for( $i=0;  $i < $this->noiseLevel; $i++ ){

                $size = intval( rand( $this->maxFontSize/2, $this->maxFontSize-2 ) );
                $angle = intval( rand(0,360) );
                $x = intval( rand( 10, $this->width-5 ) );
                $y = intval( rand( 0, $this->height-5 ) );
                $color = imagecolorallocate(
                    $image,
                    intval( rand(130,224) ),
                    intval( rand(130,224) ),
                    intval( rand(130,224) )
                );
                $text = chr( intval( rand(45,250) ) );
                ImageTTFText(
                    $image,
                    $size,
                    $angle,
                    $x,
                    $y,
                    $color,
                    $this->font,
                    $text
                );
            }
        }else{ // random grid color

            //draw horizontal grid lines
            for( $i=0; $i < $this->width; $i+=10 ){
                $color = imagecolorallocate(
                    $image,
                    intval( rand(160,224) ),
                    intval( rand(160,224) ),
                    intval( rand(160,224) )
                );
                imageline(
                    $image,
                    $i,
                    0,
                    $i,
                    $this->height,
                    $color
                );
            }

            //draw vertical grid lines
            for ( $i=0; $i < $this->height; $i+=10 ){
                $color = imagecolorallocate(
                    $image,
                    intval( rand(160,224) ),
                    intval( rand(160,224) ),
                    intval( rand(160,224) )
                );
                imageline(
                    $image,
                    0,
                    $i,
                    $this->width,
                    $i,
                    $color
                );
            }
        }

        // output the captcha code on top of everything
        for( $i=0, $x=5; $i < $codeLength; $i++ ){

            //generate random text color
            $r = intval( rand(0,128) );
            $g = intval( rand(0,128) );
            $b = intval( rand(0,128) );
            $color = ImageColorAllocate(
                $image,
                intval( rand(0,128) ),
                intval( rand(0,128) ),
                intval( rand(0,128) )
            );

            //select shadow color darker than the above generated text color
            $shadow = ImageColorAllocate( $image, $r+128, $g+128, $b+128 );
            $size = intval( rand( $this->minFontSize, $this->maxFontSize ) );
            $angle = intval( rand(-30,30) );
            $text = substr( $code, $i, 1 );

            //add the character shadow
            ImageTTFText(
                $image,
                $size,
                $angle,
                $textPositionX+2,
                $textPositionY+2,
                $shadow,
                $this->font,
                $text
            );

            //add the character slightly displaced than shadow
            ImageTTFText(
                $image,
                $size,
                $angle,
                $textPositionX,
                $textPositionY,
                $color,
                $this->font,
                $text
            );
            $textPositionX += $size + 2;
        }

        ob_start();
        imagejpeg( $image );
        $imageStr = ob_get_contents();
        ob_end_clean();

        ImageDestroy( $image );

        return ('data:image/jpg;base64,' . base64_encode( $imageStr ) );
    }


}