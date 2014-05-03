<?php

Yii::import('ext.asmselect.JAMSelect');

/**
 * Базовый класс для виджетов, которые добавляют по одной простой характеристике к анкете
 * (например тембр голоса, вид спорта или образ двойника)
 * @author frost
 * 
 * @deprecated вместо этого элемента теперь используется select2
 */
class QEditSimpleActivity extends JAMSelect
{
    /**
     * Варианты вручную добавленные пользователем
     * @var array
     */
    protected $_customData = array();
    
    /**
     * id кнопки, добавляющей новую опцию в список
     * @var string
     */
    protected $buttonId;
    /**
     * id текстового поля, в котором пользователь может вписать собственное значение
     * @var string
     */
    protected $textFieldId;
    /**
     * Пояснение к полю "добавить новую опцию"
     * @var string
     */
    public $textFieldLabel;
    /**
     * Показывать ли select-список
     * @var bool
     */
    public $hideSelect = false;
    /**
     * Редактируемое сложное поле модели
     * @var string
     */
    public $fieldName;
    /**
     * Название класса модели
     * @var string
     */
    public $modelName;
    
    /**
     * (non-PHPdoc)
     * @see JAMSelect::init()
     */
    public function init()
    {
        if ( $this->fieldName AND $this->modelName )
        {
            $this->htmlOptions['name'] = $this->modelName.'['.$this->fieldName.']';
        }
        $this->htmlOptions['title'] = Yii::t('coreMessages', 'choose');
        $this->removeLabel          = Yii::t('coreMessages', 'delete');
        $this->removeClass          = 'btn btn-danger asmListItemRemove btn-mini';
        $this->animate              = true;
        
        parent::init();
    
        Yii::app()->clientScript->registerScriptFile(
        Yii::app()->assetManager->publish(
            Yii::getPathOfAlias('application.modules.questionary.extensions.QEditSimpleActivity').'/assets/qeditsimpleactivity.js'
            ),
            CClientScript::POS_END
        );
    
        $this->buttonId = $this->id.'_addbutton';
        $this->textFieldId = $this->id.'_addtext';
        
        if ( ! $this->textFieldLabel )
        {
            $this->textFieldLabel = QuestionaryModule::t('other_(write)');
        }
    }
    
    /**
     * Установить ранее выбранные пользователем значения
     * @param array $selected - массив записей из таблицы q_activities
     */
    public function setSelectedValues($selected)
    {
        if( ! is_array($selected) )
        {
            throw new CException(Yii::t(get_class($this),
            'Invalid type. Property "SelectedValues" must be an array.'));
        }
    
        foreach ( $selected as $option )
        {// перебираем все записи и определяем тип каждой из них
            if ( $option->value == 'custom' )
            {// значение добавлено пользователем - отображаем введенное значение и вместо ключа используем id
                $this->_selected[] = $option->id;
                // добавляем пользовательское значение в список select - иначе оно не отобразится
                $this->_data[$option->id] = strip_tags($option->uservalue);
            }else
            {// Значение стандартное - выведем его и вместо клбча используем тип
                $this->_selected[] = $option->value;
            }
        }
    }
    
    protected function renderMarkup()
    {
        parent::renderMarkup();
        
        // Добавляем форму для добавления собственных элементов
        $this->renderAddMenu();
        // Добавляем JS для работы кнопки добавления новой опции
        $js = "q_add_custom_activity('".$this->id."', '".$this->textFieldId."', '".$this->buttonId."');";
        $this->cs->registerScript(__CLASS__.'#'.$this->id.'_addmenu', $js, CClientScript::POS_READY);
        
        if ( $this->hideSelect )
        {// нужно скрыть select и оставить только ручной ввод значений
            $js = "q_hide_jamselect('".$this->hideSelect."');";
            $this->cs->registerScript(__CLASS__.'#'.$this->id.'_hideselect', $js, CClientScript::POS_READY);
        }
    }
    
    /**
     * @todo вывести html-элементы через класс CHtml
     * @todo сделать настраиваемые надписи
     */
    protected function renderAddMenu()
    {
        echo '<label for="'.$this->textFieldId.'">'.$this->textFieldLabel.'</label>
        <input type="text" id="'.$this->textFieldId.'" value="" />
        <button type="button" class="btn btn-success" style="vertical-align:top;" id="'.$this->buttonId.'" href="#">'.Yii::t('coreMessages', 'add').'</button>';
    }
}