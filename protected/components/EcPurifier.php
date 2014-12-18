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
     * @param string
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
     * Format Newlines
     *
     * Converts newline characters into either <p> tags or <br />
     *
     * @access	public
     * @param	string
     * @return	string
     */
    function formatNewLines($str)
    {
        if ($str == '')
        {
            return $str;
        }
        if (strpos($str, "\n") === FALSE  && ! in_array($this->last_block_element, $this->inner_block_required))
        {
            return $str;
        }
    
        // Convert two consecutive newlines to paragraphs
        $str = str_replace("\n\n", "</p>\n\n<p>", $str);
    
        // Convert single spaces to <br /> tags
        $str = preg_replace("/([^\n])(\n)([^\n])/", "\\1<br />\\2\\3", $str);
    
        // Wrap the whole enchilada in enclosing paragraphs
        if ($str != "\n")
        {
            $str =  '<p>'.$str.'</p>';
        }
    
        // Remove empty paragraphs if they are on the first line, as this
        // is a potential unintended consequence of the previous code
        $str = preg_replace("/<p><\/p>(.*)/", "\\1", $str, 1);
    
        return $str;
    }

    /**
     * Normalize Characters
     *
     * This function mainly converts double and single quotes
     * to curly entities, but it also converts em-dashes,
     * double spaces, and ampersands and then decodes entities back to specials
     * Main goal - transform quotes
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
     * @see http://www.campaignmonitor.com/resources/will-it-work/image-blocking/ 
     * (тут можно посмотреть таблицу о том в каких клиентах и сервисах как отображаются картинки из писем)
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
     * Auto Typography
     * @see https://yii-trackstar-sample.googlecode.com/svn-history/r11/trunk/CodeIgniter_1.7.3/system/libraries/Typography.php
     *
     * This function converts text, making it typographically correct:
     * - Converts double spaces into paragraphs.
     * - Converts single line breaks into <br /> tags
     * - Converts single and double quotes into correctly facing curly quote entities.
     * - Converts three dots into ellipsis.
     * - Converts double dashes into em-dashes.
     * - Converts two spaces into entities
     *
     * @access public
     * @param string
     * @param bool whether to reduce more then two consecutive newlines to two
     * @return string
     * 
     * @todo заготовка
     */
    public static function autoTypography($str, $reduce_linebreaks=FALSE)
    {
        // Block level elements that should not be wrapped inside <p> tags
        $block_elements  = 'address|blockquote|div|dl|fieldset|form|h\d|hr|noscript|object|ol|p|pre|script|table|ul';
        // Elements that should not have <p> and <br /> tags within them.
        $skip_elements   = 'p|pre|ol|ul|dl|object|table|h\d';
        // Tags we want the parser to completely ignore when splitting the string.
        $inline_elements = 'a|abbr|acronym|b|bdo|big|br|button|cite|code|del|dfn|em|i|img|ins|input|label|map|kbd|q|samp|select|small|span|strong|sub|sup|textarea|tt|var';
        // array of block level elements that require inner content to be within another block level element
        $inner_block_required  = array('blockquote');
        // the last block element parsed
        $last_block_element    = '';
        // whether or not to protect quotes within { curly braces }
        $protect_braced_quotes = FALSE;
        
        if ( $str == '' )
        {
            return '';
        }
        // Standardize Newlines to make matching easier
        if ( strpos($str, "\r") !== FALSE )
        {
            $str = str_replace(array(
                "\r\n", 
                "\r"), "\n", $str);
        }
        // Reduce line breaks. If there are more than two consecutive linebreaks
        // we'll compress them down to a maximum of two since there's no benefit to more.
        if ( $reduce_linebreaks === TRUE )
        {
            $str = preg_replace("/\n\n+/", "\n\n", $str);
        }
        // HTML comment tags don't conform to patterns of normal tags, so pull them out separately, only if needed
        $html_comments = array();
        if ( strpos($str, '<!--') !== FALSE )
        {
            if ( preg_match_all("#(<!\-\-.*?\-\->)#s", $str, $matches) )
            {
                for ($i = 0, $total = count($matches[0]); $i < $total; $i++)
                {
                    $html_comments[] = $matches[0][$i];
                    $str = str_replace($matches[0][$i], '{@HC' . $i . '}', $str);
                }
            }
        }
        
        // match and yank <pre> tags if they exist. It's cheaper to do this separately
        // since most content will
        // not contain <pre> tags, and it keeps the PCRE patterns below simpler and faster
        if ( strpos($str, '<pre') !== FALSE )
        {
            $str = preg_replace_callback("#<pre.*?>.*?</pre>#si", array(__CLASS__, '_protectCharacters'), $str);
        }
        
        // Convert quotes within tags to temporary markers.
        $str = preg_replace_callback("#<.+?>#si", array(__CLASS__, '_protectCharacters'), $str);
        $str = preg_replace_callback("#\{.+?\}#si", array(__CLASS__, '_protectCharacters'), $str);
        
        // Convert "ignore" tags to temporary marker. The parser splits out the string at every tag
        // it encounters. Certain inline tags, like image tags, links, span tags, etc. will be
        // adversely affected if they are split out so we'll convert the opening bracket < temporarily to: {@TAG}
        $str = preg_replace("#<(/*)(" . $inline_elements . ")([ >])#i", "{@TAG}\\1\\2\\3", $str);
        
        // Split the string at every tag. This expression creates an array with this prototype:
        //
        // [array]
        // {
        // [0] = <opening tag>
        // [1] = Content...
        // [2] = <closing tag>
        // Etc...
        // }
        $chunks = preg_split('/(<(?:[^<>]+(?:"[^"]*"|\'[^\']*\')?)+>)/', $str, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        
        // Build our finalized string. We cycle through the array, skipping tags, and processing the contained text
        $str = '';
        $process = TRUE;
        $paragraph = FALSE;
        $current_chunk = 0;
        $total_chunks = count($chunks);
        
        foreach ( $chunks as $chunk )
        {
            $current_chunk++;
            // Are we dealing with a tag? If so, we'll skip the processing for this cycle.
            // Well also set the "process" flag which allows us to skip <pre> tags and a few other things.
            if ( preg_match("#<(/*)(" . $block_elements . ").*?>#", $chunk, $match) )
            {
                if ( preg_match("#" . $skip_elements . "#", $match[2]) )
                {
                    $process = ($match[1] == '/') ? TRUE : FALSE;
                }
                if ( $match[1] == '' )
                {
                    $last_block_element = $match[2];
                }
                $str .= $chunk;
                continue;
            }
            if ( $process == FALSE )
            {
                $str .= $chunk;
                continue;
            }
            // Force a newline to make sure end tags get processed by _format_newlines()
            if ( $current_chunk == $total_chunks )
            {
                $chunk .= "\n";
            }
            // Convert Newlines into <p> and <br /> tags
            $str .= self::formatNewLines($chunk);
        }
        
        // No opening block level tag? Add it if needed.
        if ( !preg_match("/^\s*<(?:" . $block_elements . ")/i", $str) )
        {
            $str = preg_replace("/^(.*?)<(" . $block_elements . ")/i", '<p>$1</p><$2', $str);
        }
        
        // Convert quotes, elipsis, em-dashes, non-breaking spaces, and ampersands
        $str = self::formatCharacters($str);
        
        // restore HTML comments
        for ($i = 0, $total = count($html_comments); $i < $total; $i++)
        {
            // remove surrounding paragraph tags, but only if there's an opening paragraph tag
            // otherwise HTML comments at the ends of paragraphs will have the closing tag removed
            // if '<p>{@HC1}' then replace <p>{@HC1}</p> with the comment, else replace only {@HC1} with the comment
            $str = preg_replace('#(?(?=<p>\{@HC' . $i . '\})<p>\{@HC' . $i . '\}(\s*</p>)|\{@HC' . $i . '\})#s', $html_comments[$i], $str);
        }
        
        // Final clean up
        $table = array(
            // If the user submitted their own paragraph tags within the text
            // we will retain them instead of using our tags.
            // <?php BBEdit syntax coloring bug fix
            '/(<p[^>*?]>)<p>/' => '$1', 
            // Reduce multiple instances of opening/closing paragraph tags to a single one
            '#(</p>)+#' => '</p>', 
            '/(<p>\W*<p>)+/' => '<p>', 
            // Clean up stray paragraph tags that appear before block level elements
            '#<p></p><(' . $block_elements . ')#' => '<$1', 
            // Clean up stray non-breaking spaces preceeding block elements
            '#(&nbsp;\s*)+<(' . $block_elements . ')#' => '  <$2', 
            // Replace the temporary markers we added earlier
            '/\{@TAG\}/' => '<', 
            '/\{@DQ\}/' => '"', 
            '/\{@SQ\}/' => "'", 
            '/\{@DD\}/' => '--', 
            '/\{@NBS\}/' => '  ',
        );
        // Do we need to reduce empty lines?
        if ( $reduce_linebreaks === TRUE )
        {
            $table['#<p>\n*</p>#'] = '';
        }else
        {
            // If we have empty paragraph tags we add a non-breaking space
            // otherwise most browsers won't treat them as true paragraphs
            $table['#<p></p>#'] = '<p>&nbsp;</p>';
        }
        return preg_replace(array_keys($table), $table, $str);
    }
    
    /**
     * Protect Characters
     *
     * Protects special characters from being formatted later
     * We don't want quotes converted within tags so we'll temporarily convert them to {@DQ} and {@SQ}
     * and we don't want double dashes converted to emdash entities, so they are marked with {@DD}
     * likewise double spaces are converted to {@NBS} to prevent entity conversion
     *
     * @access	public
     * @param	array
     * @return	string
     */
    public static function _protectCharacters($match)
    {
        return str_replace(array("'",'"','--','  '), array('{@SQ}', '{@DQ}', '{@DD}', '{@NBS}'), $match[0]);
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