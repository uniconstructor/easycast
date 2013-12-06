<?php

/**
 * Один фрагмент основной части письма
 * Возможные варианты:
 *     - Только текст
 *     - Изображение на всю ширину
 *     - Картинка слева
 *     - Картинка справа
 *     - Две колонки
 *     - Три колонки
 *     
 * @todo проверить правильность типа отрисовки (только значения из списка)
 * @todo дописать отображение остальных вариантов верстки
 */
class EMailSegment extends CWidget
{
    /**
     * @var string - тип отображения (название должно совпадать с одним из вариантов view)
     */
    public $type = 'textOnly';
    /**
     * @var int - отступ содержимого от края блока
     */
    public $padding = 0;
    /**
     * @var int - 
     */
    public $fullPadding = 0;
    /**
     * @var string - подзаголовок абзаца текста
     */
    public $header;
    /**
     * @var string - дополнительная информация справа от заголовка
     */
    public $headerInfo;
    /**
     * @var string - абзац текста (разрешено html-форматирование)
     */
    public $text;
    /**
     * @var string - ссылка на изображение (если есть)
     */
    public $imageLink;
    /**
     * @var string - ссылка на изображение (если есть)
     */
    public $imageAlt;
    /**
     * @var string - ссылка c изображения на веб-страницу (если есть)
     *               Если не указана - изображение отобразится просто как картинка
     */
    public $imageTarget;
    /**
     * @var стиль отображаемого изображения
     */
    public $imageStyle = 'border:0px;';
    /**
     * @var array - массив колонок для отображения (если нужно отобразить верстку в несколько колонок)
     */
    public $columns = array();
    /**
     * @var array - кнопка с действием (если нужна)
     *              Массив, содержащий настройки для создания виджета EMailButton
     */
    public $button = array();
    /**
     * @var string - цвет текста в блоке
     */
    public $textColor = '#6d6d6d';
    /**
     * @var string - цвет текста для заголовков
     */
    public $headerColor = '#286B84';
    /**
     * @var string - расположение текста в блоке
     */
    public $textAlign = 'left';
    /**
     * @var string - расположение заголовка в блоке
     */
    public $headerAlign = 'left';
    /**
     * @var bool - добавить ли разделитель после заголовка
     */
    public $addHeaderRuler = false;
    /**
     * @var bool - добавить ли разделитель в конце блока
     */
    public $addTextRuler = false;
    /**
     * @var bool - добавить ли красивый "разорванный" разделитель в конце блока
     */
    public $addCutRuler = false;
    /**
     * @var string - слиль разделителя после заголовка
     */
    public $headerRulerStyle  = 'border-bottom:3px dotted #a3a3a3;';
    /**
     * @var string - слиль разделителя после текста (в конце блока)
     */
    public $textRulerStyle    = 'border-bottom:1px dotted #a3a3a3;';
    /**
     * @var string - слиль разделителя по умолчанию
     */
    public $defaultRulerStyle = 'border-bottom:1px dotted #a3a3a3;';
    
    /**
     * @var int - ширина блока по умолчанию
     */
    protected $blockWidth;
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        if ( ! $this->type )
        {// устанавливаем тип отображения по умолчанию, если он не задан
            $this->type = 'textOnly';
        }
        if ( ! $this->addHeaderRuler )
        {
            $this->headerRulerStyle  = '';
        }
        if ( ! $this->addTextRuler )
        {
            $this->textRulerStyle    = '';
        }
        if ( ! $this->addTextRuler AND ! $this->addHeaderRuler )
        {
            $this->defaultRulerStyle = '';
        }
        // определяем отступ от краев письма в зависимости от типа отображения: с отступом или без
        $this->fullPadding = $this->padding + 20;
        // определяем полную ширину блока по умолчанию
        $this->blockWidth = 640 - (2 * $this->padding);
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        switch ( $this->type )
        {// определяем, в каком виде отобразить блок письма
            // абзац текста
            case 'textOnly':  $this->displayTextOnly(); break;
            // одно изобрадение слева
            case 'imageLeft': $this->displayImageLeft(); break;
            // подзаголовок с дополнительной информацией
            case 'subHeader': $this->render('subHeader/header'); break;
            // изображение во всю ширину письма
            case 'image640':  $this->render('image640/image'); break;
            // текст или произвольная верстка во всю ширину письма
            case 'text640':   $this->render('text640/text'); break;
            // горизонтальный разделитель
            case 'hruler':    $this->render('hruler'); break;
            // разделитель с "разрезанными" краями
            case 'cutRuler':  $this->render('cutRuler'); break;
        }
        if ( is_array($this->button) AND ! empty($this->button) )
        {// отображаем кнопку с действием под абзацем
            $this->widget('application.modules.mailComposer.extensions.widgets.EMailButton.EMailButton',
                $this->button
            );
        }
        if ( $this->addCutRuler )
        {// отображаем большой широкий разделитель в виде разрезанной бумаги 
            $this->render('cutRuler');
        }
    }
    
    /**
     * Отобразить фрагмент (блок) письма в формате "только текст"
     * (содержимое фрагмента берется из полей виджета)
     * @return null
     */
    public function displayTextOnly()
    {
        $this->render('textOnly/text');
    }
    
    /**
     * Отобразить фрагмент (блок) письма в формате "картинка слева"
     * @return null
     */
    public function displayImageLeft()
    {
        $this->render('imageLeft/left');
    }
}