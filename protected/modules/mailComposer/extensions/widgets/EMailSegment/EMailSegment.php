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
     * @var string - абзац текста (разрешено html-форматирование)
     */
    public $text;
    /**
     * @var string - ссылка на изображение (если есть)
     */
    public $imageLink;
    /**
     * @var array - массив колонок для отображения (если нужно отобразить верстку в несколько колонок)
     */
    public $columns = array();
    /**
     * @var array - кнопка с действием (если нужна). Массив, содержащий настройки для создания виджета EMailButton
     */
    public $button = array();
    
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
            case 'imageLeft': $this->displayImageLeft(); break;
        }
        if ( ! empty($this->button) )
        {// отображаем кнопку с действием под абзацем
            $this->widget('application.modules.mailComposer.extensions.widgets.EMailButton.EMailButton',
                array(
                    'link'    => $this->button['link'],
                    'caption' => $this->button['caption'],
                )
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