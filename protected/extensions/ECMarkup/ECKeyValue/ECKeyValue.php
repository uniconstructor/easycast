<?php

/**
 * Класс для представления короткого фрагмента информации (название-значение)
 * Отображает небольшой блок, состоящий из двух частей: стрелка с названием параметра и блок со значением
 */
class ECKeyValue extends CWidget
{
    /**
     * @var string - пояснение для значения
     */
    public $key;
    /**
     * @var string - само значение
     */
    public $value;
    /**
     * @var string - тип виджета (влияет на цвет)
     *              info
     *              primary
     *              default
     *              warning
     *              danger
     *              inverse
     */
    public $type = 'info';
    /**
     * @var int - высота треугольника в пикселях
     */
    public $spacerHeight = 15;
    /**
     * @var bool - "выключает" виджет: делает его серым
     *              (равнозначно type='default', параметр сделан для удобства)
     */
    public $disabled = false;
    /**
     * @var array - параметры контейнера виджета
     */
    public $htmlOptions = array();
    /**
     * @var string - цвет фона под надписью
     */
    public $captionBackground;
    /**
     * @var string - цвет фона под значением
     */
    public $valueBackground;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        parent::init();
        
        $defaults = array(
            'class' => 'ec-key-value-container ec-key-value-container-'.$this->type,
        );
        $this->htmlOptions = CMap::mergeArray($defaults, $this->htmlOptions);
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('view');
    }
}