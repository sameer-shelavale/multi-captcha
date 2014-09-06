<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 6/3/14
 * Time: 5:43 PM
 */
namespace MultiCaptcha;

include_once( 'GIFEncoder.class.php' );

class GifCaptcha extends BaseCaptcha {

    var $minCodeLength = 4; // minimum length of code displayed on captcha image
    var $maxCodeLength = 10; // maximum length of code displayed on captcha image( max value is 20 )
    var $maxRequired = 5;   // maximum number of characters that can be asked to identify
    var $minRequired = 3;   // minimum number of characters that can be asked to identify

    var $noise      = true;
    var $noiseLevel = 25;   // number of background noisy characters
    var $width      = 150;  // width of image in pixels
    var $height     = 40;   // height of the image in pixels

    var $font = 'comic.ttf';


    var $totalFrames = 40;
    var $delay = 5;

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
        $result['question']['url'] = $this->getGifImage( $code );
        $result['description'] = $questionText;
        $result['answer'] = $answer;


        return $result;
    }

    function generateCode( $length ){
        $set = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $result = '';
        for( $i=0; $i < $length; $i++ ){
            $result .= $set[ rand(0, strlen( $set )) ];
        }
        return $result;
    }

    function getGifImage( $code ){

        $codeLength = strlen( $code );
        $animationData = array();

        $maxFontSize = min( $this->width/($codeLength+2), $this->height/2.2 );
        $minFontSize = $maxFontSize * 0.8;

        $bgColor = array(
            intval( rand(200,255) ),
            intval( rand(200,255) ),
            intval( rand(200,255) )
        );

        //initialize noise and captcha code characters
        //add character noise
        if( $this->noise ){
            // add random characters in background with random position, angle, color
            for( $i=0;  $i < $this->noiseLevel; $i++ ){
                $char['type'] = 'noise';
                $char = array();
                $char['size'] = intval( rand( $minFontSize/2, $maxFontSize-2 ) );
                $char['text'] = $this->generateCode( 1 );
                $char['color'] = array(
                    intval( rand(130,224) ),
                    intval( rand(130,224) ),
                    intval( rand(130,224) )
                );

                $char['angle'] = intval( rand(0,360) );
                $char['x'] = rand( 5, $this->width-5 ) ;
                $char['y'] = rand( 5, $this->height-5 ) ;

                $char['speedX'] = rand( -25, 25 ) /10; // per frame
                $char['speedY'] = rand( -20, 20 ) /10; // per frame
                $char['speedR'] = rand( -40, 40 ) /10; // rotation speed per frame
                $char['maxR'] = rand( 0, 360 ); // rotation angle
                $animationData[] = $char;
            }
        }

        //add code
        // output the captcha code on top of everything
        $textPositionY = $maxFontSize + ( $this->height - $maxFontSize )/2;
        $textPositionX = $maxFontSize - 2;
        for( $i=0, $x=5; $i < $codeLength; $i++ ){

            $char = array();
            $char['type'] = 'code';
            $char['size'] = intval( rand( $minFontSize, $maxFontSize ) );
            $char['text'] = substr( $code, $i, 1 );
            $char['color'] = array(
                intval( rand(0,128) ),
                intval( rand(0,128) ),
                intval( rand(0,128) )
            );

            $char['shadowColor'] = array(
                $char['color'][0] + intval( rand(0,128) ),
                $char['color'][1] + intval( rand(0,128) ),
                $char['color'][2] + intval( rand(0,128) )
            );

            $char['angle'] = rand( -50, 50 );
            $char['x'] = $textPositionX;
            $char['y'] = $textPositionY;
            $char['speedX'] = 0; // per frame
            $char['speedY'] = 0; // per frame
            $char['speedR'] = rand( -40, 40 ) /10; // rotation speed per frame
            $char['maxR'] = 50; // rotation speed per frame

            $textPositionX += $maxFontSize + 2;
            $animationData[] = $char;
        }

        //var_dump( $animationData );
        //animate for given number of frames
        $frames = array();
        $framesDelay = array();
        for( $i=0; $i < $this->totalFrames; $i++ ){

            //create new image
            $img = imagecreatetruecolor(
                $this->width,
                $this->height
            );

            //add image background
            $bg = ImageColorAllocate(
                $img,
                $bgColor[0],
                $bgColor[1],
                $bgColor[2]
            );

            //create a rectangle with random background color
            ImageFilledRectangle(
                $img,
                0,
                0,
                $this->width,
                $this->height,
                $bg
            );

            foreach( $animationData as $idx => $char ){

                if( $char['type'] == 'code' ){
                    //add shadow
                    $color = imagecolorallocate(
                        $img,
                        $char['shadowColor'][0],
                        $char['shadowColor'][1],
                        $char['shadowColor'][2]
                    );

                    ImageTTFText(
                        $img,
                        $char['size'],
                        $char['angle'],
                        $char['x']+2,
                        $char['y']+2,
                        $color,
                        $this->font,
                        $char['text']
                    );
                }

                $color = imagecolorallocate(
                    $img,
                    $char['color'][0],
                    $char['color'][1],
                    $char['color'][2]
                );

                ImageTTFText(
                    $img,
                    $char['size'],
                    $char['angle'],
                    intval($char['x']),
                    intval($char['y']),
                    $color,
                    $this->font,
                    $char['text']
                );

                //move the charcters for next frame

                    $animationData[$idx]['x'] += $char['speedX'];
                    $animationData[$idx]['y'] += $char['speedY'];
                    $animationData[$idx]['angle'] += $char['speedR'];

                    //character bounce of the boundries
                    if( $animationData[$idx]['x'] <= 0 || $animationData[$idx]['x'] >= $this->width ){
                        $animationData[$idx]['speedX'] = 0 - $animationData[$idx]['speedX'];
                    }
                    if( $animationData[$idx]['y'] <= 0 || $animationData[$idx]['y'] >= $this->height){
                        $animationData[$idx]['speedY'] = 0 - $animationData[$idx]['speedY'];
                    }

                    if( $animationData[$idx]['angle'] <= 0-$char['maxR'] || $animationData[$idx]['angle'] >= $char['maxR']){
                        $animationData[$idx]['speedR'] = 0 - $animationData[$idx]['speedR'];
                    }



            }

            ob_start();
            imagegif( $img );
            $frames[] = ob_get_contents();
            ob_end_clean();
            $framesDelay[] = $this->delay;

            //echo '<img src="data:image/gif;base64,' . base64_encode( $frames[count( $frames )-1] ).'" /><br/><br/>' ;
            ImageDestroy( $img );
        }

        $gif = new GIFEncoder	(
            $frames,
            $framesDelay,   //delay
            0,              //loops
            2,              //Disposal
            255, 255, 255,  //bg
            "bin"           //binary data
        );


        return ('data:image/gif;base64,' . base64_encode( $gif->GetAnimation() ) );
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

                $size = intval( rand( $this->minFontSize/2, $this->maxFontSize-2 ) );
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