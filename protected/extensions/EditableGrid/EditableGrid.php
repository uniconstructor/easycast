<?php

/**
 * Базовый класс для всех виджетов редактирования таблиц, имеет возможность добавления новых объектов
 * Заменяет multimodelform
 * 
 * @todo прописать проверки обязательных полей в init
 * @todo сделать настройку помещать/не помещать формы в клип
 * @todo наследовать от этого класса виджет QGridEditBase, убрать лишние методы после наследования
 */
class EditableGrid extends CWidget
{
    /**
     * @var string - сообщение перед удалением записи
     */
    public $deleteConfirmation = 'Удалить эту запись?';
    /**
     * @var string - всплывающая подсказка над иконкой удаления записи
     */
    public $deleteButtonLabel  = 'Удалить';
    /**
     * @var string - url по которому происходит удаление записей
     */
    public $deleteUrl;
    /**
     * @var string - url по которому происходит создание записей
     */
    public $createUrl;
    /**
     * @var string - url по которому происходит обновление записей
     */
    public $updateUrl;
    /**
     * @var string - префикс html-id для каждой строки таблицы (чтобы можно было удалять строки)
     */
    public $rowIdPrefix;
    /**
     * @var string - префикс для свойства name у editable полей
     */
    public $rowEditPrefix;
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass;
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     */
    public $fields = array();
    /**
     * @var string - html-id формы для ввода новой записи
    */
    public $formId;
    /**
     * @var string - html-id modal-окна для ввода новой записи
     */
    public $modalId;
    /**
     * @var string - html-id кнопки для ввода новой записи
     */
    public $addButtonId;
    /**
     * @var string - надпись на кнопке добавления новой записи
     */
    public $addButtonLabel = 'Добавить';
    /**
     * @var string - заголовок всплывающего окна с формой добавления новой записи
     */
    public $modalHeader    = 'Добавить запись';
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
     * @var CActiveRecord
    */
    protected $model;
    /**
     * @var string
     */
    protected $viewsPrefix = 'ext.EditableGrid.views.';
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        parent::init();
        
        // создаем пустую модель для формы
        $this->initModel();
        
        if ( ! $this->rowEditPrefix )
        {
            $this->rowEditPrefix = $this->modelClass;
        }
        // регистрируем клип с формой в модуле анкет для того чтобы позже вывести его в конце формы
        $this->registerFormClip();
    }
    
    /**
     * Создать пустую модель для формы добавления объекта
     * @return void
     */
    protected function initModel()
    {
        $className = $this->modelClass;
        $this->model = new $className;
    }
    
    /**
     * Зарегистрировать форму добавления сложного значения анкеты в модуле "анкеты" (QuestionaryModule)
     * @return void
     */
    protected function registerFormClip()
    {
        $this->clipId = $this->formId.'-clip';
        Yii::app()->getModule($this->clipModule)->formClips[] = $this->clipId;
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        // рисуем таблицу со списком добавленных элементов и кнопкой "добавить"
        $this->render($this->viewsPrefix.'grid');
        
        // отображаем скрытую форму добавления новой записи (она будет возникать в modal-окне)
        // записываем ее в clip и выводим позже, в самом низу страницы иначе она конфликтует с формой
        // внутри которой находится текущий виджет
        $this->owner->beginClip($this->clipId);
        $this->render($this->viewsPrefix.'_form', array('model' => $this->model));
        $this->owner->endClip();
    }
    
    /**
     * Отобразить поля формы создания новой записи
     *
     * @param TbActiveForm $form
     * @return void
     */
    protected function renderFormFields($form)
    {
        throw new CException('Эта функция должна быть переопределена');
    }
    
    /**
     * Получить JS-код, выполняющийся после удаления строки
     * @return string
     */
    protected function createAfterDeleteJs()
    {
        return 'function(link, success, data)
        {
            if ( ! success )
            {
                alert("При удалении возникла ошибка. Попробуйте еще раз.");
                return;
            }
            var rowSelector = "#'.$this->rowIdPrefix.'" + data;
            $(rowSelector).fadeOut(400);
        }';
    }
    
    /**
     * Получить JS-код, выполняющийся после добавления новой записи
     * @return string
     *
     * @todo создать нормальный ряд таблицы с возможностью редактирования и удаления
     */
    protected function createAfterAddJs()
    {
        $js = '';
        // js для добавления новой строки в таблицу
        $js .= "\$.fn.yiiGridView.update('{$this->rowIdPrefix}table');";
        // js для очистки полей формы после добавления новой записи
        $js .= $this->createClearFormJs();
    
        return $js;
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
     *
     * @return array - массив колонок таблицы TbExtendedGridView с настройками виджетов
     */
    protected function getTableColumns()
    {
        $dataColumns = $this->getDataColumns();
        // колонка с иконками действий
        $dataColumns[] = $this->getActionsColumn();
    
        return $dataColumns;
    }
    
    /**
     * Получить настройки для создания редактируемых колонок таблицы
     * @return array
     */
    protected function getDataColumns()
    {
        throw new CException('Эта функция должна быть переопределена');
    }
    
    /**
     * Получить колонку действий с записями
     * @return array
     */
    protected function getActionsColumn()
    {
        return array(
            'header'      => 'Действия',
            'htmlOptions' => array('nowrap' => 'nowrap', 'style' => 'text-align:center;'),
            'class'       => 'bootstrap.widgets.TbButtonColumn',
            'template'    => '{delete}',
            'deleteConfirmation' => $this->deleteConfirmation,
            'afterDelete' => $this->createAfterDeleteJs(),
            'buttons' => array(
                'delete' => array(
                    'label' => $this->deleteButtonLabel,
                    'url'   => 'Yii::app()->createUrl("'.$this->deleteUrl.'", array("id" => $data->id))',
                ),
            ),
        );
    }
    
    /**
     * Получить стандартные настройки для виджета выбора даты
     * @return array
     * 
     * @todo для базового класса слишком специализированная функция: переделать в метод для выбора даты (не только год)
     */
    protected function getYearPickerOptions()
    {
        return array(
            'minViewMode' => 'years',
            // @todo для русского языка виджет не работает - обновиться и исправить
            'language'    => 'en',
            'format'      => 'yyyy',
            'autoclose'   => true,
            'forceParse'  => false,
        );
    }
    
    /**
     * Получить параметры для создания editable-колонки в таблице (текстовое поле)
     *
     * @param string $field - поле модели для которого создается редактируемая колонка таблицы
     * @return void
     */
    protected function getTextColumnOptions($field, $value=null)
    {
        $options = array(
            'name'  => $field,
            'class' => 'bootstrap.widgets.TbEditableColumn',
            'editable' => array(
                'type'      => 'text',
                'title'     => $this->model->getAttributeLabel($field),
                'url'       => $this->updateUrl,
                'emptytext' => $this->getFieldEmptyText($field),
                'params' => array(
                    Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                ),
            ),
        );
        
        if ( $value )
        {
            $options['value'] = $value;
        }
        
        return $options;
    }
    
    /**
     * Получить параметры для создания editable-колонки в таблице (select2 без подгрузки элементов по AJAX)
     *
     * @param string $field - поле модели для которого создается редактируемая колонка таблицы
     * @param array $variants - список вариантов для выбора
     * @return void
     */
    protected function getStaticSelect2ColumnOptions($field, $variants, $valueField='level', $allowCustom=false)
    {
        $options = array(
            'name'  => $field,
            'class' => 'bootstrap.widgets.TbEditableColumn',
            'value' => '$data->'.$valueField.';',
            'editable' => array(
                'type'      => 'select2',
                'title'     => $this->model->getAttributeLabel($field),
                'url'       => $this->updateUrl,
                'emptytext' => $this->getFieldEmptyText($field),
                'params' => array(
                    Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                ),
                'select2' => $this->getSelect2Options($variants, 'static', $allowCustom),
                'source'  => $variants,
            ),
        );
        
        return $options;
    }
    
    /**
     * 
     * @param array $variants
     * @param string $type
     * @param string $allowCustom
     * @return array
     */
    protected function getSelect2Options($variants, $type='static', $allowCustom=false)
    {
        $options = array(
            'maximumSelectionSize' => 0,
            'placeholder'       => 'Выбрать...',
            'placeholderOption' => '',
            'multiple'          => false,
        );
        $variants = ECPurifier::getSelect2Options($variants);
        
        if ( $allowCustom )
        {
            $options['tags'] = $variants;
            $options['maximumSelectionSize'] = 1;
        }
        
        return $options;
    }
    
    /**
     * Получить параметры для создания editable-колонки в таблице (выбор года)
     *
     * @param string $field - поле модели для которого создается редактируемая колонка таблицы
     * @return array
     * 
     * @todo для базового класса слишком специализированная функция: переделать в метод для колонки выбора даты
     */
    protected function getYearColumnOptions($field)
    {
        return array(
            'name'  => $field,
            'value' => '$data->'.$field.';',
            'class' => 'bootstrap.widgets.TbEditableColumn',
            'editable' => array(
                'type'      => 'date',
                'title'     => $this->model->getAttributeLabel($field),
                'url'       => $this->updateUrl,
                'emptytext' => $this->getFieldEmptyText($field),
                'format'    => 'yyyy',
                'params'    => array(
                    Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                ),
                'options'   => array(
                    'datepicker' => $this->getYearPickerOptions(),
                ),
            ),
        );
    }
    
    /**
     * Получить текст, который отображается в случае когда поле таблицы не заполнено
     * @param string $field - поле модели для которого получается текс-заглушка
     * @return string
     */
    protected function getFieldEmptyText($field)
    {
        if ( isset($this->emptyTextVariants[$field]) )
        {
            return $this->emptyTextVariants[$field];
        }
        return '[не заполнено]';
    }
    
    /**
     * Создать источник данных, который будет подставлен в виджет TbExtendedGridView для отображения
     * таблицы с данными, которые уже до этого были добавлены
     * 
     * @return CDataProvider
     */
    protected function createGridDataProvider()
    {
        return new CActiveDataProvider($this->modelClass, array(
            'criteria'   => $this->getGridCriteria(),
            'pagination' => false,
        ));
    } 
    
    /**
     * Получить критерий выборки записей для списка редактирования
     * @return array
     */
    protected function getGridCriteria()
    {
        return array();
    }
}