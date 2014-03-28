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
    
    /**
     * Translit text from cyrillic to latin letters.
     * @static
     * @param string $text the text being translit.
     * @return string
     */
    public static function translit($text, $toLowCase=false)
    {
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
        $text = implode(array_slice(explode('<br>', wordwrap(trim(strip_tags(html_entity_decode($text))), $maxlength, '<br>', false)), 0, 1));
        //$text = substr(, 0, $maxlength);
    
        foreach ( $matrix as $from => $to )
        {
            $text = mb_eregi_replace($from, $to, $text);
        }
    
        // Optionally convert to lower case.
        if ( $toLowCase )
        {
            $text = strtolower($text);
        }
    
        return $text;
    }
}