<?php
/**
 * Created by PhpStorm.
 * User: Sameer Shelavale
 * Date: 5/14/14
 * Time: 5:39 PM
 */

class MathCaptcha extends BaseCaptcha {

    var $description = "Answer following question if you are human";
    var $id = false;
    var $class = false;
    var $style = false;
    var $level = 3;

    public function getHtml(){

        $data = $this->generateQuestion( $this->level );

        $cipher = $this->encrypt( $data['answer'], 'math' );

        if( $this->customFieldName ) {
            $fieldName = $this->customFieldName;
        }else{
            //use cipher as field name
            $fieldName = $cipher;
        }

        if( $this->id ){
            $id = "id=\"{$this->id}\"";
        }

        if( $this->class ){
            $class = "class=\"{$this->class}\"";
        }

        if( $this->style ){
            $style = "style=\"{$this->style}\"";
        }

        if( $fieldName == $cipher ){
            $html = $this->description.'<br/>';
            $html .= $data['question'];
            $html .= '<input type="text" name="'.$fieldName.'" value="" '.$id.' '.$class.' '.$style.' /> ';
        }else{
            $html = $this->description.'<br/>';
            $html .= $data['question'];
            $html .= '<input type="text" name="'.$fieldName.'" value="" '.$id.' '.$class.' '.$style.' /> ';
            $html .= '<input type="hidden" name="'.$fieldName.'_challenge" value="'.$cipher.'" /> ';
        }

        return $html;
    }


    function generateQuestion( $level = 3 ){
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

    function expEval( $exp = array() ){
        $str = str_replace( 'X', '*',  implode( $exp ) );
        return( eval('return '.$str.';') );
    }

    function getFactors( $number ){
        $result[] = 1;
        if( $number != 1){
            $result[] = $number;
        }
        $limit = $number;
        for( $i=2; $i < $limit; $i++ ){
            if( $number % $i == 0 ){
                $result[] = $i;
                $tmp = $number/$i;
                if( $tmp != $i ){
                    $result[] = $tmp;
                }
                $limit = $number/$i;
            }
        }
        return $result;
    }


} 