<?php
/**
 * Класс - валидатор
 *
 * User: ADrushka
 * Date: 18.06.2017
 * Time: 13:28
 */

namespace crm;


class Validator
{
    public static function validateAll($data){
        return false;
    }

    public static function str($str){
        if(is_string($str)){
            return true;
        }
        return false;
    }
}