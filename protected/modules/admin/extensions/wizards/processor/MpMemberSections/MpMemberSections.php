<?php

// родительский класс
Yii::import('ext.EditableGrid.EditableGrid');

/**
 * Виджет, выводящий таблицу (EditableGrid) со списком возможных разделов по каждому участнику
 * Столбцы таблицы:
 * - раздел
 * - маркер: кнопка с четырьмя положениями (не участвует/плохо/средне/хорошо)
 * - комментарий
 */
class MpMemberSections extends EditableGrid
{
    /**
     * @var ProjectMember
     */
    public $member;
    /**
     * @var CustomerInvite
     */
    public $customerInvite;
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass  = 'MemberInstance';
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     */
    public $fields = array('objectid', 'linktype', 'comment');
    /**
     * @var array - список текстов-заглушек, которые отображаются в случае, когда поле не заполнено
     */
    public $emptyTextVariants = array(
        'objectid' => '[не указано]',
        'linktype' => '[не указано]',
        'comment'  => '[не указано]',
    );
    /**
     * @var array - массив настроек виджета TbButton для кнопки "добавить"
     */
    public $addButtonOptions = array(
        // убираем кнопку "добавить" - она здесь не нужна, потому что
        // список разделов должен обновлять свои связи сам
        'htmlOptions' => array('style' => 'display:none;'), 
    );
    /**
     * @var string - id модуля, который хранит клипы с modal-формами
     */
    public $clipModule = 'admin';
    
    /**
     * @see EditableGrid::init()
     */
    public function init()
    {
        Yii::import('catalog.models.*');
        // установка всех основных компонентов
        parent::init();
        
        if ( $this->customerInvite instanceof CustomerInvite )
        {// если происходит отбор по одноразовой ссылке - добавим ключи без которых нельзя редактировать запись
            $this->updateUrl = Yii::app()->createUrl($this->updateUrl, array(
                'ciid' => $this->customerInvite->id,
                'k1'   => $this->customerInvite->key,
                'k2'   => $this->customerInvite->key2,
            ));
        }
    }
    
    /**
     * Создать пустую модель для формы добавления объекта
     * @return void
     */
    protected function initModel()
    {
        $this->model = new $this->modelClass;
        $this->model->objecttype = 'section_instance';
    }
    
    /**
     * Отобразить поля формы создания новой записи
     *
     * @param TbActiveForm $form
     * @return void
     */
    protected function renderFormFields($form)
    {
        // в таблице категорий нельзя добавлять новые строки
        return;
    }
    
    /**
     * @see EditableGrid::getActionsColumn()
     */
    protected function getTableColumns()
    {
        return $this->getDataColumns();
    }
    
    /**
     * Получить настройки для колонок таблицы с данными
     * @return array
     */
    protected function getDataColumns()
    {
        // настройки для типа связи
        $oldTypeOptions = $this->getSelectColumnOptions('linktype',  $this->model->getLinkTypeOptions());
        $newTypeOptions = array(
            'value'    => '$data->linkTypeOption;',
            'editable' => array(
                'mode'    => 'inline',
                'options' => array(
                    'onblur' => 'submit',
                ),
            ),
        );
        $typeOptions = CMap::mergeArray($oldTypeOptions, $newTypeOptions);
        
        // настраиваем поле комментария
        $oldCommentOptions = $this->getTextAreaColumnOptions('comment');
        $newCommentOptions = array(
            'editable' => array(
                'mode'      => 'inline',
                'placement' => 'left',
                'options' => array(
                    'showbuttons' => 'bottom',
                ),
            ),
        );
        $commentOptions = CMap::mergeArray($oldCommentOptions, $newCommentOptions);
        
        return array(
            // раздел для заявки
            array(
                'name'  => 'objectid',
                'value' => '$data->sectionInstance->section->name;',
            ),
            // тип
            $typeOptions,
            // описание
            $commentOptions,
        );
    }
    
    /**
     * @see EditableGrid::getGridCriteria()
     */
    protected function getGridCriteria()
    {
        return array(
            'condition' => "`objecttype` = 'section_instance' AND `memberid` = '{$this->member->id}'"
        );
    }
}