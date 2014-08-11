<?php

/**
 * Виджет выполнения процесса по шагам (Wizard)
 * @see http://exacttarget.github.io/fuelux/#wizard
 * 
 * Все JS-функции могут работать с переменными data.step (номер текущего шага) 
 * и data.direction (prev/next)
 * Для прерывания действия используйте e.preventDefault()
 * 
 * @todo языковые строки
 * @todo сделать подрузку скриптов не через CDN
 */
class FuelWizard extends CWidget
{
    /**
     * @var array - массив с шагами для действий
     *           [title]   - название шага
     *           [content] - содержимое вкладки (html)
     *           [active]  - true если текущий шаг активен
     */
    public $htmlOptions = array(
        'class' => 'wizard',
    );
    /**
     * @var array - 
     */
    public $steps = array();
    /**
     * @var string - текущий шаг: совпадает с индексом массива
     */
    public $activeStep;
    /**
     * @var bool - отображать ли кнопку назад?
     */
    public $displayButtons = true;
    /**
     * @var array - массив настроек для bootstrap-виджета TbButton
     */
    public $prevButtonOptions = array(
        'buttonType'  => 'button',
        'size'        => 'large',
        'encodeLabel' => false,
        'label'       => 'Назад',
        'htmlOptions' => array(
            'class' => 'btn-prev',
        ),
    );
    /**
     * @var array - массив настроек для bootstrap-виджета TbButton
     */
    public $nextButtonOptions = array(
        'buttonType'  => 'button',
        'type'        => 'primary',
        'size'        => 'large',
        'encodeLabel' => false,
        'label'       => 'Дальше',
        'htmlOptions' => array(
            'class' => 'btn-next pull-right',
        ),
    );
    /**
     * @var string - JS-код который выполняется перед изменением шага
     */
    public $onChange = "console.log('change');";
    /**
     * @var string - JS-код который выполняется перед изменением шага
     */
    public $onAfterChange = "console.log('changed')";
    /**
     * @var string - JS-код который выполняется при завершении всех шагов
     */
    public $onFinish = "console.log('finished')";
    /**
     * @var string - JS-код который выполняется при нажатии на кнопку "далее"
     */
    public $onPrev = "console.log('prev');";
    /**
     * @var string - JS-код который выполняется при нажатии на кнопку "назад"
     */
    public $onNext = "console.log('next');";
    /**
     * @var string - скрипты выполняющиеся при инициализации
     */
    public $onInit;
    
    /**
     * @var array
     */
    protected $stepNumbers = array();
    /**
     * @var array
     */
    protected $stepNames   = array();
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        $this->htmlOptions['id'] = $this->getId();
        $initId     = __CLASS__ . '#' . $this->getId();
        $baseUrl    = CHtml::asset(dirname(__FILE__) . '/assets');
        $initScript = '';
        
        $count = 1;
        foreach ( $this->steps as $name => $step )
        {
            $this->stepNumbers[$name] = $count;
            $this->stepNames[$count]  = $name;
            $count++;
        }
        if ( ! $this->activeStep )
        {// устанавливаем активным шагом первый если он не задан
            $this->activeStep = $this->stepNames[1];
        }
        
        // задаем изначальный шаг виджета
        $initScript = "$('#{$this->htmlOptions['id']}').wizard('selectedItem', {step:{$this->stepNumbers[$this->activeStep]}});";
        if ( $this->onPrev )
        {
            $initScript .= "$('.btn-prev').on('click', function(e) {
                {$this->onPrev}
                $('#{$this->htmlOptions['id']}').wizard('previous');
            });\n";
        }
        if ( $this->onNext )
        {
            $initScript .= "$('.btn-next').on('click', function(e) {
                {$this->onNext}
                $('#{$this->htmlOptions['id']}').wizard('next');
            });\n";
        }
        if ( $this->onChange )
        {
            $initScript .= "$('#{$this->htmlOptions['id']}').on('change', function(e, data) {
                {$this->onChange}
            });\n";
        }
        if ( $this->onAfterChange )
        {
            $initScript .= "$('#{$this->htmlOptions['id']}').on('changed', function(e, data) {
                {$this->onAfterChange}
            });\n";
        }
        if ( $this->onFinish )
        {
            $initScript .= "$('#{$this->htmlOptions['id']}').on('finished', function(e, data) {
                {$this->onFinish}
            });\n";
        }
        $initScript .= $this->onInit;
        
        // подключаем зависимости
        $clientScript = Yii::app()->clientScript;
        //$clientScript->registerCoreScript('jquery');
        // подключаем стили виджета
        //$clientScript->registerCssFile($baseUrl . '/css/fuelux.min.css');
        //$clientScript->registerCssFile($baseUrl . '/css/fuelux-responsive.min.css');
        
        // @todo подключаем скрипты виджета
       /* $clientScript->registerScriptFile($baseUrl . '/lib/almond.js');
        $clientScript->registerScriptFile($baseUrl . '/lib/require.js');
        $clientScript->registerScriptFile($baseUrl . '/lib/moment.js');
        //$clientScript->registerScriptFile($baseUrl . '/src/loader.js');
        $clientScript->registerScriptFile($baseUrl . '/src/wizard.js');*/
        
        $clientScript->registerScript($initId, $initScript, CClientScript::POS_READY);
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('wizard');
    }
}