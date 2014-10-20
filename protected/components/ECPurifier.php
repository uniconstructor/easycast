<?php

/**
 * Наш собственный класс с дополнительными функциями очистки данных
 */
class ECPurifier extends CHtmlPurifier
{
    /**
     * @var string
     */
    public static $encoding = 'UTF-8';
    
    /**
     * Обрезать все кавычки из названия
     * 
     * @param unknown $string
     * @return string
     */
    public static function trimQuotes($string)
    {
        $string = mb_trim($string);
        // стандартная кодировка для всех регулярных выражений
        mb_regex_encoding(self::$encoding);
        
        if ( mb_eregi("^['\"«»„“]{1-2}.{1+}['\"«»„“]{1-2}$", $string) )
        {// проверим, что строка начинается и заканчивается кавычками, чтобы не отрезать лишнееы
            // в случаях типа: БАЛЕТ "ЛЕБЕДИНОЕ ОЗЕРО"
            $string = mb_trim($string, " \t\n\r\0\x0B'\"«»„“");
        }
        return $string;
    }
    
    /**
     * Получить массив значений для использования в элементе select2
     * 
     * @param array $data - значения для select-списка 
     *                      формат массива соответствует возвращаемому из CHtml::listData()
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
     * Используется для того чтобы получать ссылки на картинки, которые сразу же отображаются в письмах,
     * отправляемых сайтом
     * @see http://www.campaignmonitor.com/resources/will-it-work/image-blocking/
     *      (тут можно посмотреть таблицу о том в каких клиентах и сервисах как отображаются картинки из писем)
     *      
     * @param string $url - ссылка на изображение
     * @return string
     * 
     * @todo сделать CFilter на основе этой функции, и применять его на production ко всему html
     *       в исходящих письмах. После этого убрать все вызовы этой функции из модуля CMailComposer.
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
    
    /**
     * Translit text from cyrillic to latin letters.
     * 
     * @param string $text the text being translit.
     * @return string
     */
    public static function translit($text, $toLowCase=false)
    {
        // стандартная кодировка для всех регулярных выражений
        mb_regex_encoding(self::$encoding);
        
        $matrix = array(
            "й"=>"i","ц"=>"c","у"=>"u","к"=>"k","е"=>"e","н"=>"n",
            "г"=>"g","ш"=>"sh","щ"=>"shch","з"=>"z","х"=>"h","ъ"=>"",
            "ф"=>"f","ы"=>"y","в"=>"v","а"=>"a","п"=>"p","р"=>"r",
            "о"=>"o","л"=>"l","д"=>"d","ж"=>"zh","э"=>"e","ё"=>"e",
            "я"=>"ya","ч"=>"ch","с"=>"s","м"=>"m","и"=>"i","т"=>"t",
            "ь"=>"","б"=>"b","ю"=>"yu",
            "Й"=>"I","Ц"=>"C","У"=>"U","К"=>"K","Е"=>"E","Н"=>"N",
            "Г"=>"G","Ш"=>"SH","Щ"=>"SHCH","З"=>"Z","Х"=>"X","Ъ"=>"",
            "Ф"=>"F","Ы"=>"Y","В"=>"V","А"=>"A","П"=>"P","Р"=>"R",
            "О"=>"O","Л"=>"L","Д"=>"D","Ж"=>"ZH","Э"=>"E","Ё"=>"E",
            "Я"=>"YA","Ч"=>"CH","С"=>"S","М"=>"M","И"=>"I","Т"=>"T",
            "Ь"=>"","Б"=>"B","Ю"=>"YU",
            /*"«"=>"","»"=>""," "=>"-",*/
    
            /*"\""=>"", "\."=>"", "–"=>"-", "\,"=>"", "\("=>"", "\)"=>"",
            "\?"=>"", "\!"=>"", "\:"=>"",*/
    
            /*'#' => '', */'№' => '#',/*' - '=>'-', '/'=>'-', ' '=>' ',*/
        );
        // Enforce the maximum component length
        $maxlength = 255;
        $decodedText = html_entity_decode($text, ENT_NOQUOTES, self::$encoding);
        $noBrText    = explode('<br>', wordwrap(mb_trim(strip_tags($decodedText)), $maxlength, '<br>', false));
        $text        = implode(array_slice($noBrText, 0, 1));
    
        foreach ( $matrix as $from => $to )
        {
            $text = mb_eregi_replace($from, $to, $text);
        }
        if ( $toLowCase )
        {// Optionally convert to lower case.
            $text = mb_strtolower($text, self::$encoding);
        }
        return $text;
    }
    
    /**
     * Создать случайную строку заданной длины из указанных символов
     * @see http://stackoverflow.com/questions/4356289/php-random-string-generator
     *
     * @param  int    $length - длина строки
     * @param  string $characters - набор символов
     * @return string
     */
    public static function getRandomString($length=10, $characters=null)
    {
        $randomString = '';
    
        if ( ! $characters )
        {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        if ( $length < 0 )
        {
            $length = 0;
        }
        for ( $i = 0; $i < $length; $i++ )
        {
            $randomString .= $characters[rand(0, mb_strlen($characters, self::$encoding) - 1)];
        }
        return $randomString;
    }
    
    /**
     * Исправляет первую букву строки на заглавную
     * (одноименная функция PHP, к сожалению не работает с русским языком)
     * 
     * @return string - исправленная строка
     * @todo настроить работу с utf-8
     */
    public static function ucfirst($str)
    {
        $fc = mb_strtoupper(mb_substr($str, 0, 1, self::$encoding));
        return $fc.mb_substr($str, 1, self::$encoding);
    }
}