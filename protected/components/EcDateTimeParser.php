<?php

/**
 * @todo документировать класс
 * @todo объявить форматы дат константами
 */
class EcDateTimeParser extends CDateTimeParser
{
    /**
     * 
     * @param  string $value
     * @param  string $pattern
     * @param  array $defaults
     * @return int
     */
    public static function parse($value, $pattern='dd.MM.yyyy', $defaults=array())
    {
        if ( ! $pattern )
        {
            $pattern = Yii::app()->params['yiiDateFormat'];
        }
        return parent::parse($value, $pattern, $defaults);
    }
}