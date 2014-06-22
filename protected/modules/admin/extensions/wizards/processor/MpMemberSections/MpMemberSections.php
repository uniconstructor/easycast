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
     * @var string - если для всех трех действий (create, update, delete) используется один контроллер
     *               то здесь можно указать относительный путь к нему: в этом случае не нужно
     *               отдельно указывать url для каждого действия
     *               Пример значения: '/questionary/qEmcee/'
     */
    //public $gridControllerPath = '/projects/MemberInstanceGrid/';
    /**
     * @var string - url по которому происходит обновление записей
     */
    //public $updateUrl = '/projects/invite/editMemberInstance';
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
        //$this->render('_fields', array('model' => $this->model, 'form' => $form));
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
        return array(
            // раздел для заявки
            array(
                'name'  => 'objectid',
                'value' => '$data->sectionInstance->section->name;',
            ),
            // тип
            $this->getStaticSelect2ColumnOptions('linktype', $this->model->getLinkTypeOptions(), 'linkTypeOption'),
            // описание
            $this->getTextAreaColumnOptions('comment'),
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