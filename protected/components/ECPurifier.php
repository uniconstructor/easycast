<?php

/**
 * Наш собственный класс с дополнительными функциями очистки данных
 */
class ECPurifier extends CHtmlPurifier
{
    /**
     * Обрезать все кавычки из названия
     * @param unknown $string
     * @return string
     * @return void
     */
    public static function trimQuotes($string)
    {
        $string = trim($string);
        if ( mb_eregi("^['\"«»„“]{1-2}.{1+}['\"«»„“]{1-2}$", $string) )
        {// проверим, что строка начинается и заканчивается кавычками, чтобы не отрезать лишнееы
            // в случаях типа: БАЛЕТ "ЛЕБЕДИНОЕ ОЗЕРО"
            $string = trim($string, " \t\n\r\0\x0B'\"«»„“");
        }
        return $string;
    }
    
    /**
     * Получить массив значений для использования в элементе select2
     * @param array $data - значения по умолчанию в формате ключ-значение
     * @return array
     */
    public static function getSelect2Options($data)
    {
        $options = array();
        foreach ( $data as $id => $text )
        {
            $options[] = array('id' => $id, 'text' => CHtml::encode($text));
        }
        
        return $options;
    }
    
    /**
     * Получить URL для отображения изображений через прокси-сервера google
     * Используется для того чтобы получать ссылки на картинки, которые сразу же отображаются в письмах
     * @param string $url - ссылка на изображение
     * @return string
     */
    public static function getImageProxyUrl($url)
    {
        if ( ! isset(Yii::app()->params['useGoogleImageProxy']) OR ! Yii::app()->params['useGoogleImageProxy'] )
        {// использование прокси-серверов google отключено (для сборки разработчика)
            return $url;
        }
        $prefix = 'https://images2-focus-opensocial.googleusercontent.com/gadgets/proxy?url=';
        $suffix = '&container=focus&gadget=a&no_expand=1&resize_h=0&rewriteMime=image%2F*';
        
        return $prefix.urlencode($url).$suffix;
    }
}