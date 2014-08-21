<?php

/**
 * Виджет для редактирования смешанного списка полей формы 
 * (поля анкеты и поля заявки в одном списке)
 * 
 * @todo если не хватит стандартного JS для отчистки полей 
 *       перед вставкой новой записи - то дописать свой
 */
class EditFieldList extends EditableGrid
{
    /**
     * @var int - id списка, объекты из которого редактируются
     */
    public $easyListId;
    /**
     * @var string - сообщение перед удалением записи
     */
    public $deleteConfirmation = 'Удалить этот элемент?';
    /**
     * @var string - если для всех трех действий (create, update, delete) используется один контроллер
     *               то здесь можно указать относительный путь к нему: в этом случае не нужно
     *               отдельно указывать url для каждого действия
     *               Пример значения: '/questionary/qEmcee/'
     */
    public $gridControllerPath = '/admin/easyListItem/';
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass = 'EasyListItem';
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     *              (в этом виджете набор различается в зависимости от типа,
     *              поэтому сразу ничего не задаем)
     */
    public $fields;
    /**
     * @var string - заголовок всплывающего окна с формой добавления новой записи
     */
    public $modalHeader = 'Добавить поле';
    /**
     * @var string - надпись на кнопке добавления новой записи
     */
    public $addButtonLabel = 'Добавить';
    /**
     * @var array - список текстов-заглушек, которые отображаются в случае, когда поле не заполнено
     */
    public $emptyTextVariants = array(
        'fieldid' => '[не указано]',
        'filling' => '[не указано]',
    );
    /**
     * @var string - id модуля, который хранит клипы с modal-формами
     */
    public $clipModule = 'admin';
    /**
    * @var string - адрес по которому происходит изменение порядка сортировки элементов по AJAX
    */
    public $sortableAction = 'admin/easyListItem/sortable';
    /**
     * @var bool - разрешить ли изменять порядок строк перетаскиванием?
     */
    public $sortableRows = true;

    /**
     * @var CActiveRecord - объект к которому привязываются дополнительные поля
     */
    protected $targetObject;

    /**
     * Отобразить поля формы создания новой записи
     *
     * @param TbActiveForm $form
     * @return void
     */
    protected function renderFormFields($form)
    {
        $this->render('_fields', array('model' => $this->model, 'form' => $form));
    }

    /**
     * Создать пустую модель для формы добавления объекта
     * @return void
     */
    protected function initModel()
    {
        $this->model = new $this->modelClass;
        $this->model->easylistid = $this->easyListId;
    }

    /**
     * Получить настройки для колонок таблицы с данными
     * @return array
     */
    protected function getDataColumns()
    {
        $columns = array();
        $columns['form'] = array(
            // название поля
            // @todo возможность редактировать название
            array(
                'name'  => 'fieldid',
                'value' => '$data->name;',
            ),
            // обязательное поле (да/нет/задано автоматически)
            $this->getStaticSelect2ColumnOptions('filling', QFieldInstance::model()->getFillingModes(), 'fillingMode'),
            // автоматическое значение (если есть)
            $this->getTextColumnOptions('data'),
        );
        $columns['wizard'] = array(
            // шаг регистрации
            $this->getSelectColumnOptions('objectid', $this->getWizardStepOptions(), 'stepName'),
            // @todo возможность редактировать название
            // название поля
            array(
                'name'  => 'fieldid',
                'value' => '$data->name;',
            ),
            // обязательное поле (да/нет/задано автоматически)
            $this->getStaticSelect2ColumnOptions('filling', QFieldInstance::model()->getFillingModes(), 'fillingMode'),
            // автоматическое значение (если есть)
            $this->getTextColumnOptions('data'),
        );
        if ( $this->objectType === 'vacancy' )
        {
            return $columns[$this->targetObject->regtype];
        }
        return array(
            // название поля
            // @todo возможность редактировать название
            array(
                'name'  => 'fieldid',
                'value' => '$data->name;',
            ),
            // обязательное поле (да/нет/задано автоматически)
            $this->getStaticSelect2ColumnOptions('filling', QFieldInstance::model()->getFillingModes(), 'fillingMode'),
            // автоматическое значение (если есть)
            $this->getTextColumnOptions('data'),
        );
    }
    
    /**
     * @see EditableGrid::getGridCriteria()
     */
    protected function getGridCriteria()
    {
        return array(
             'scopes' => array(
                 'forList' => array($this->easyListId),
             ),
         );
    }
    
    /**
     * Получить полный список полей (обязательных и дополнительных), 
     * которые можно прикрепить к объекту. Одним списком.
     * 
     * @return array
     * 
     * @todo Исключить те поля, которые уже прикреплены
     */
    protected function getFieldIdOptions()
    {
        $fields  = QUserField::model()->findAll();
        $options = CHtml::listData($fields, 'id', 'label');
        
        /*foreach ( $this->targetObject->userFields as $field )
        {// ескли к этому объекту уже привязаны какие-либо поля, то не даем привязать их второй раз
        unset($options[$field->id]);
        }*/
        asort($options);
        
        return CMap::mergeArray(array('' => Yii::t('coreMessages', 'not_set')), $options);
    }
}