<?php

// подключаем родительский класс, его отсюда не видно
Yii::import('ext.EditableGrid.EditableGrid');

/**
 * Виджет для ручного добавления дополнительных участников в фотовызывной
 * 
 * @property ExternalMemberForm $model
 * @deprecated задача отменена. Удалить при рефакторинге
 */
class CallListExternalMembers extends EditableGrid
{
    /**
     * @var RCallList - фотовызывной в который добавляются участники
     */
    public $report;
    /**
     * @var EventVacancy - роль, к которой прикрепляются добавленные вручную участники
     */
    public $vacancy;
    
    /**
     * @var string - сообщение перед удалением записи
     */
    public $deleteConfirmation = 'Удалить участника?';
    /**
     * @var string - всплывающая подсказка над иконкой удаления записи
     */
    public $deleteButtonLabel  = 'Удалить';
    /**
     * @var string - url по которому происходит удаление записей
     */
    public $deleteUrl = '/admin/projectEvent/deleteExternalMember';
    /**
     * @var string - url по которому происходит создание записей
     */
    public $createUrl = '/admin/projectEvent/createExternalMember';
    /**
     * @var string - url по которому происходит обновление записей
     */
    public $updateUrl = '/admin/projectEvent/updateExternalMember';
    /**
     * @var string - префикс html-id для каждой строки таблицы (чтобы можно было удалять строки)
     */
    public $rowIdPrefix = 'external_member_row_';
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass = 'ExternalMemberForm';
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     */
    public $fields = array('galleryid', 'lastname', 'firstname', 'age', 'phone', 'bages', 'comment');
    /**
     * @var string - html-id формы для ввода новой записи
     */
    public $formId = 'external-member-form';
    /**
     * @var string - html-id modal-окна для ввода новой записи
     */
    public $modalId = 'external-member-form';
    /**
     * @var string - html-id кнопки для ввода новой записи
     */
    public $addButtonId    = 'add-external-member-button';
    /**
     * @var string - надпись на кнопке добавления новой записи
     */
    public $addButtonLabel = 'Добавить участника';
    /**
     * @var string - заголовок всплывающего окна с формой добавления новой записи
     */
    public $modalHeader    = 'Добавить участника вручную';
    /**
     * @var array - список текстов-заглушек, которые отображаются в случае, когда поле не заполнено
     */
    public $emptyTextVariants = array();
    /**
     * @var string - id клипа (фрагмента html-кода который генерируется в одном месте а выводится в другом)
     *               Здесь используется для modal-окон с формами (их нельзя размещать внутри других форм)
     *               Если этот параметр задан - то в модуль questionary будет записан id фрагмента кода с формой
     */
    public $clipId;
    /**
     * @var string - id модуля, который хранит клипы с modal-формами
     */
    public $clipModule;
    
    /**
     * @see EditableGrid::init()
     */
    public function init()
    {
        // подключаем стили и скрипты galleryManager здесь, потому что сам плагин приедет к нам позже,
        // и через AJAX, а значит сам подключить свои стили не сможет
        $assets = Yii::app()->getAssetManager()->publish('ext.galleryManager.assets');
        if ( defined('YII_DEBUG') AND YII_DEBUG )
        {
            $cs->registerScriptFile($this->assets . '/jquery.iframe-transport.js');
            $cs->registerScriptFile($this->assets . '/jquery.galleryManager.js');
        }else
        {
            $cs->registerScriptFile($this->assets . '/jquery.iframe-transport.min.js');
            $cs->registerScriptFile($this->assets . '/jquery.galleryManager.min.js');
        }
        
        if ( ! ( $this->report instanceof Report ) )
        {
            throw new CException('Не указан фотовызывной для виджета ручного добавления участников');
        }
        if ( ! ( $this->vacancy instanceof EventVacancy ) )
        {
            throw new CException('Не указана роль для виджета ручного добавления участников');
        }
        
        // добавляем id мероприятия и роли к каждому id элемента чтобы не возникло пересечений
        $this->formId      .= '_'.$this->report->id.'_'.$this->vacancy->id;
        $this->modalId     .= '_'.$this->report->id.'_'.$this->vacancy->id;
        $this->addButtonId .= '_'.$this->report->id.'_'.$this->vacancy->id;
        
        parent::init();
    }
    
    /**
     * Отобразить поля формы создания новой записи
     * @param TbActiveForm $form
     * @return void
     */
    protected function renderFormFields($form)
    {
        $this->render('_fields', array('model' => $this->model, 'form' => $form));
    }
    
    /**
     * js для очистки полей формы после добавления новой записи
     * @return string
     */
    protected function createClearFormJs()
    {
        $js = '';
        foreach ( $this->fields as $field )
        {
            $js .= "\$('#{$this->modelClass}_{$field}').val('');\n";
        }
        return $js;
    }
    
    /**
     * Получить настройки для колонок таблицы с данными
     * @return array
     */
    protected function getDataColumns()
    {
        $columns = array();
        foreach ( $this->fields as $field )
        {
            $columns[] = array(
                'name'  => $field,
                'value' => '$data->'.$field.';',
            );
        }
        return $columns;
    }
}