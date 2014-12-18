<?php

/**
 * Наш собственный класс с дополнительными функциями очистки данных
 *
 * @todo добавить метод translitUrl для понятных ссылок
 */
class EcPurifier extends CHtml
{
    /**
     *
     * @var string
     */
    public static $encoding = 'utf-8';

    /**
     * Обрезать все кавычки которые стоят одновременно сначала и с конца строки
     * чтобы не отображать названия объектов в двойных кавычках
     *
     * @param string $string
     * @return string
     */
    public static function trimQuotes($string)
    {
        // стандартная кодировка для всех регулярных выражений
        mb_regex_encoding(self::$encoding);
        
        if ( mb_eregi("^['\"«»„“]{1-2}.{1+}['\"«»„“]{1-2}$", $string) )
        { // проверим, что строка начинается и заканчивается кавычками, чтобы не отрезать лишнееы
          // в случаях типа: БАЛЕТ "ЛЕБЕДИНОЕ ОЗЕРО"
            $string = trim($string, " \t\n\r\0\x0B'\"«»„“");
        }
        return $string;
    }

    /**
     * Format Characters
     * @see https://code.google.com/p/yii-trackstar-sample/source/browse/trunk/CodeIgniter_1.7.3/system/libraries/Typography.php?r=11
     *
     * This function mainly converts double and single quotes
     * to curly entities, but it also converts em-dashes,
     * double spaces, and ampersands
     *
     * @access public
     * @param  string
     * @return string
     */
    public static function formatCharacters($str)
    {
        $table = array(
            // nested smart quotes, opening and closing
            // note that rules for grammar (English) allow only for two levels deep
            // and that single quotes are _supposed_ to always be on the outside
            // but we'll accommodate both
            // Note that in all cases, whitespace is the primary determining factor
            // on which direction to curl, with non-word characters like punctuation
            // being a secondary factor only after whitespace is addressed.
            '/\'"(\s|$)/' => '&#8217;&#8221;$1', 
            '/(^|\s|<p>)\'"/' => '$1&#8216;&#8220;', 
            '/\'"(\W)/' => '&#8217;&#8221;$1', 
            '/(\W)\'"/' => '$1&#8216;&#8220;', 
            '/"\'(\s|$)/' => '&#8221;&#8217;$1', 
            '/(^|\s|<p>)"\'/' => '$1&#8220;&#8216;', 
            '/"\'(\W)/' => '&#8221;&#8217;$1', 
            '/(\W)"\'/' => '$1&#8220;&#8216;', 
            // single quote smart quotes
            '/\'(\s|$)/' => '&#8217;$1', 
            '/(^|\s|<p>)\'/' => '$1&#8216;', 
            '/\'(\W)/' => '&#8217;$1', 
            '/(\W)\'/' => '$1&#8216;', 
            // double quote smart quotes
            '/"(\s|$)/' => '&#8221;$1', 
            '/(^|\s|<p>)"/' => '$1&#8220;', 
            '/"(\W)/' => '&#8221;$1', 
            '/(\W)"/' => '$1&#8220;', 
            // apostrophes
            "/(\w)'(\w)/" => '$1&#8217;$2', 
            // Em dash and ellipses dots
            '/\s?\-\-\s?/' => '&#8212;', 
            '/(\w)\.{3}/' => '$1&#8230;', 
            // double space after sentences
            '/(\W)  /' => '$1&nbsp; ', 
            // ampersands, if not a character entity
            '/&(?!#?[a-zA-Z0-9]{2,};)/' => '&amp;',
        );
        return preg_replace(array_keys($table), $table, $str);
    }
    
    /**
     * Format Characters
     *
     * This function mainly converts double and single quotes
     * to curly entities, but it also converts em-dashes,
     * double spaces, and ampersands and then decodes entities back to specials
     *
     * @access public
     * @param  string
     * @return string
     */
    public static function normalizeCharacters($str)
    {
        return html_entity_decode(self::formatCharacters($str), null, self::$encoding);
    }

    /**
     * Получить массив значений для использования в элементе select2
     *
     * @param array $data - значения для select-списка
     *        формат массива соответствует возвращаемому из CHtml::listData()
     * @param bool $encode - применить CHtml::encode() к значениям
     * @return array
     */
    public static function getSelect2Options($data, $encode = true)
    {
        $options = array();
        foreach ( $data as $id => $text )
        {
            if ( $encode )
            {
                $text = CHtml::encode($text);
            }
            $options[] = array(
                'id' => $id, 
                'text' => $text);
        }
        return $options;
    }

    /**
     * Получить массив значений для использования в элементе select/checklist для TbEditableField
     *
     * @param array $data - значения для select-списка
     *        формат массива соответствует возвращаемому из CHtml::listData()
     * @return array
     */
    public static function getEditableSelectOptions($data, $encode = true)
    {
        $options = array();
        foreach ( $data as $id => $text )
        {
            if ( $encode )
            {
                $text = CHtml::encode($text);
            }
            $options[] = array(
                'value' => $id, 
                'text' => $text);
        }
        return $options;
    }

    /**
     * Получить URL для отображения изображений через прокси-сервера google
     * Используется для того чтобы получать ссылки на картинки, которые сразу 
     * же отображаются в письмах, отправляемых сайтом
     * 
     * @see http://www.campaignmonitor.com/resources/will-it-work/image-blocking/ (тут можно посмотреть таблицу о том в каких клиентах и сервисах как отображаются картинки из писем)
     *     
     * @param string $url - ссылка на изображение
     * @return string
     *
     * @todo сделать CFilter на основе этой функции, и применять его на production ко всему html
     *       в исходящих письмах. После этого убрать все вызовы этой функции из модуля CMailComposer.
     */
    public static function getImageProxyUrl($url)
    {
        if ( !isset(Yii::app()->params['useGoogleImageProxy']) or !Yii::app()->params['useGoogleImageProxy'] )
        { // использование прокси-серверов google отключено (для сборки разработчика)
            return $url;
        }
        $prefix = 'https://images2-focus-opensocial.googleusercontent.com/gadgets/proxy?url=';
        $suffix = '&container=focus&gadget=a&no_expand=1&resize_h=0&rewriteMime=image%2F*';
        
        return $prefix . urlencode($url) . $suffix;
    }

    /**
     * Translit text from cyrillic to latin letters.
     *
     * @param string $text the text being translit.
     * @return string
     */
    public static function translit($text, $toLowCase = false)
    {
        // стандартная кодировка для всех регулярных выражений
        mb_regex_encoding(self::$encoding);
        
        $matrix = array(
            "й" => "i", 
            "ц" => "c", 
            "у" => "u", 
            "к" => "k", 
            "е" => "e", 
            "н" => "n", 
            "г" => "g", 
            "ш" => "sh", 
            "щ" => "shch", 
            "з" => "z", 
            "х" => "h", 
            "ъ" => "", 
            "ф" => "f", 
            "ы" => "y", 
            "в" => "v", 
            "а" => "a", 
            "п" => "p", 
            "р" => "r", 
            "о" => "o", 
            "л" => "l", 
            "д" => "d", 
            "ж" => "zh", 
            "э" => "e", 
            "ё" => "e", 
            "я" => "ya", 
            "ч" => "ch", 
            "с" => "s", 
            "м" => "m", 
            "и" => "i", 
            "т" => "t", 
            "ь" => "", 
            "б" => "b", 
            "ю" => "yu", 
            "Й" => "I", 
            "Ц" => "C", 
            "У" => "U", 
            "К" => "K", 
            "Е" => "E", 
            "Н" => "N", 
            "Г" => "G", 
            "Ш" => "SH", 
            "Щ" => "SHCH", 
            "З" => "Z", 
            "Х" => "X", 
            "Ъ" => "", 
            "Ф" => "F", 
            "Ы" => "Y", 
            "В" => "V", 
            "А" => "A", 
            "П" => "P", 
            "Р" => "R", 
            "О" => "O", 
            "Л" => "L", 
            "Д" => "D", 
            "Ж" => "ZH", 
            "Э" => "E", 
            "Ё" => "E", 
            "Я" => "YA", 
            "Ч" => "CH", 
            "С" => "S", 
            "М" => "M", 
            "И" => "I", 
            "Т" => "T", 
            "Ь" => "", 
            "Б" => "B", 
            "Ю" => "YU",
            /*"«"=>"","»"=>""," "=>"-",*/
    
            /*
             * "\""=>"", "\."=>"", "–"=>"-", "\,"=>"", "\("=>"", "\)"=>"", "\?"=>"", "\!"=>"", "\:"=>"",
             */
    
            /*'#' => '', */'№' => '#',/*' - '=>'-', '/'=>'-', ' '=>' ',*/
        );
        // Enforce the maximum component length
        $maxlength = 255;
        $decodedText = html_entity_decode($text, ENT_NOQUOTES, self::$encoding);
        $noBrText = explode('<br>', wordwrap(trim(strip_tags($decodedText)), $maxlength, '<br>', false));
        $text = implode(array_slice($noBrText, 0, 1));
        
        foreach ( $matrix as $from => $to )
        {
            $text = mb_eregi_replace($from, $to, $text);
        }
        if ( $toLowCase )
        { // Optionally convert to lower case.
            $text = mb_strtolower($text, self::$encoding);
        }
        return $text;
    }

    /**
     * Создать случайную строку заданной длины из указанных символов
     * 
     * @see http://stackoverflow.com/questions/4356289/php-random-string-generator
     *
     * @param int $length - длина строки
     * @param string $characters - набор символов
     * @return string
     *
     * @deprecated использовать Yii::app()->securityManager->generateRandomString($length);
     */
    public static function getRandomString($length = 10, $characters = null)
    {
        $msg = 'DEPRECATED: ' . __CLASS__ . '->' . __METHOD__ . ': [' . __FILE__ . ':' . __LINE__ . ']';
        Yii::log($msg, CLogger::LEVEL_INFO, 'deprecated');
        
        return Yii::app()->securityManager->generateRandomString($length);
    }

    /**
     * Исправляет первую букву строки на заглавную
     * (одноименная функция PHP, к сожалению не работает с русским языком)
     *
     * @param string $str - исходная строка (кодировка UTF-8)
     * @return string - исправленная строка
     */
    public static function ucfirst($str)
    {
        $fc = mb_strtoupper(mb_substr($str, 0, 1, self::$encoding), self::$encoding);
        return $fc . mb_substr($str, 1, null, self::$encoding);
    }
}