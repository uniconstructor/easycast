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
     * @var string - слиль разделителя после заголовка
     */
    public $headerRulerStyle  = 'border-bottom:2px dotted #a3a3a3;';
    /**
     * @var string - слиль разделителя после текста (в конце блока)
     */
    public $textRulerStyle    = 'border-bottom:1px dotted #a3a3a3;';
    /**
     * @var string - слиль разделителя по умолчанию
     */
    public $defaultRulerStyle = 'border-bottom:1px dotted #a3a3a3;';
    
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
            $this->headerRulerStyle = '';
        }
        if ( ! $this->addTextRuler )
        {
            $this->textRulerStyle = '';
        }
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
            // горизонтальный разделитель
            case 'subHeader': $this->render('hruler'); break;
        }
        if ( is_array($this->button) AND ! empty($this->button) )
        {// отображаем кнопку с действием под абзацем
            $this->widget('application.modules.mailComposer.extensions.widgets.EMailButton.EMailButton',
                $this->button
            );
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