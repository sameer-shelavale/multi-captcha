<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 6/3/14
 * Time: 5:43 PM
 */

namespace types\image;


class ImageCaptcha {

    var $codeLength = 8;    // total characters in captcha
    var $noiseLevel = 25;   // number of background noisy characters
    var $width      = 100;  // width of image in pixels
    var $height     = 30;   // height of the image in pixels

    var $font = 'comic.ttf';
    var $maxFontSize = 15;
    var $minFontSize = 13;



    public function getHtml(){

    }

    function generateQuestion( $codeLength = 8, $requiredCharacters = 4 ){
        $symbols1 = array( '+', '-', 'X', '/' );
        $symbols2 = array( '+', '-', 'X' );

        $q = array();
        $q[] = rand(0,10);
        for( $i=1; $i < $level; $i++ ){
            //select operator

            if( $i == 1 && $q[0] != 0 ){
                $operator = $symbols1[array_rand( $symbols1 )];
            }else{
                $operator = $symbols2[array_rand( $symbols2 )];
            }

            //select operand
            if( $operator == '/' ){
                $factors = $this->getFactors( $q[0]);
                $operand = $factors[array_rand( $factors )];
            }elseif( $operator == 'X'  ){
                $operand = rand( 0, 5 );
            }else{
                $operand = rand( 0, 10 );
            }
            $q[] = $operator;
            $q[] = $operand;
        }

        $result['question'] = implode( ' ',$q ).' = ';
        $result['answer'] = "{$this->expEval( $q )}";

        return $result;
    }

    function GenerateCode( ){
        $r = rand( 0, 31-$this->codeLength-1 );
        $this->code = substr( md5(time()), $r, $this->codeLength );
    }



    function getImage( $noise = true ) {

        //calculate font sizes so that things fit in on image
        $this->maxFontSize = $this->width / ( $this->codeLength + 1 );
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
        for( $i=0, $x=5; $i < $this->codeLength; $i++ ){
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
            $text = strtoupper( substr( $this->code, $i, 1 ) );

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