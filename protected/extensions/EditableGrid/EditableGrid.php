<?php

/**
 * Базовый класс для всех виджетов редактирования таблиц, имеет возможность добавления новых объектов
 * Заменяет multimodelform
 * 
 * @todo прописать проверки обязательных полей в init
 * @todo сделать настройку помещать/не помещать формы в клип
 * @todo переписать дочерние классы: использовать controllerPath вместо отдельного указания трех URL
 * @todo переписать дочерние классы: убрать генерацию ненужных id
 * @todo Добавить возможность задавать POST-параметры запроса для всез editable-полей
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
    public $deleteButtonLabel = 'Удалить';
    /**
     * @var string - всплывающая подсказка над иконкой просмотра записи
     */
    public $viewButtonLabel   = 'Просмотр';
    /**
     * @var string - если для всех трех действий (create, update, delete) используется один контроллер
     *               то здесь можно указать относительный путь к нему: в этом случае не нужно 
     *               отдельно указывать url для каждого действия
     *               Пример значения: '/questionary/qEmcee/'
     */
    public $gridControllerPath;
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
     * @var string - url просмотра записей
     */
    public $viewUrl;
    /**
     * @var string - главный префикс для всех id использующихся в виджете
     *               Обеспечивает уникальность id всех элементов виджета
     *               Если не задан - то используется название модели
     */
    public $mainIdPrefix;
    /**
     * @var string - префикс html-id для каждой строки таблицы (чтобы можно было удалять строки)
     */
    public $rowIdPrefix;
    /**
     * @var string - префикс для свойства name у editable полей
     * @deprecated похоже что этот параметр не используется: убедиться в этом и удалить при рефакторинге
     */
    public $rowEditPrefix;
    /**
     * @var string - html-id modal-окна для ввода новой записи
     */
    public $modalId;
    /**
     * @var string - html-id кнопки для ввода новой записи
     */
    public $addButtonId;
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
     * @var array - массив настроек виджета TbButton для кнопки "добавить"
     */
    public $addButtonOptions = array();
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
     * @var string - текст, отображаемый когда в таблице нет ни одного значения
     */
    public $emptyText;
    /**
     * @var bool - выводить ли форму добавления отдельно от виджета таблицы?
     *             Используется в тех случаях когда этот виджет нужно вставить в середину 
     *             другого виджета формы
     *             Дело в том, что стандарт HTML не позволяет создавать одну форму внутри другой,
     *             поэтому если этот виджет вставляется в середину формы редактирования, то
     *             вывод происходит следующим образом: 
     *             <форма редактирования>
     *             <EditableGrid>
     *             <окончание формы редактирования>
     *             <форма ввода нового элемента в editable-таблицу>
     *             true  - использовать раздельное отображение формы добавления нового элемента
     *             false - выводитьт форму ввода нового элемента сразу после таблицы
     */
    public $useClip = true;
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
     * @var bool - разрешить ли изменять порядок строк перетаскиванием?
     */
    public $sortableRows      = false;
    /**
     * @var string - поле, по которому производится сортировка
     */
    public $sortableAttribute = 'sortorder';
    /**
     * @var bool - сохранять ли новый порядок строк через AJAX?
     */
    public $sortableAjaxSave  = true;
    /**
     * @var string - путь к обработчику сортировки строк
     */
    public $sortableAction;
    /**
     * @var CDbCriteria|array - условия выборки записей для таблицы значений
     */
    public $criteria;
    
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
        // вычисляем url для создания/редактирования/удаления записей если они не заданы
        if ( ! $this->createUrl )
        {
            $this->createUrl = $this->gridControllerPath.'create';
        }
        if ( ! $this->updateUrl )
        {
            $this->updateUrl = $this->gridControllerPath.'update';
        }
        if ( ! $this->deleteUrl )
        {
            $this->deleteUrl = $this->gridControllerPath.'delete';
        }
        if ( ! $this->viewUrl )
        {
            $this->viewUrl   = $this->gridControllerPath.'view';
        }
        // url для изменения порядка строк в таблице
        if ( ! $this->sortableAction )
        {
            $this->sortableAction = $this->gridControllerPath.'sortable';
        }
        // вычисляем все id для html-элементов если они не заданы
        if ( ! $this->mainIdPrefix )
        {
            $this->mainIdPrefix = $this->getId().'_'.$this->modelClass;
        }
        if ( ! $this->rowEditPrefix )
        {
            $this->rowEditPrefix = 'e_'.$this->mainIdPrefix.'_row_';
        }
        if ( ! $this->rowIdPrefix )
        {
            $this->rowIdPrefix = $this->mainIdPrefix.'_row_';
        }
        if ( ! $this->addButtonId )
        {
            $this->addButtonId = 'add-'.$this->mainIdPrefix.'-button';
        }
        if ( ! $this->formId )
        {
            $this->formId  = $this->mainIdPrefix.'-grid-edit-form';
        }
        if ( ! $this->modalId )
        {
            $this->modalId = 'add-'.$this->mainIdPrefix.'-modal';
        }
        if ( ! $this->clipId )
        {
            $this->clipId = $this->formId.'-clip';
        }
        // создаем пустую модель для формы
        $this->initModel();
        // настраиваем кнопку добавления новой записи
        $this->initAddButtonOptions();
        
        // регистрируем клип с формой в модуле анкет для того чтобы позже вывести его в конце формы
        $this->registerFormClip();
    }
    
    /**
     * Создать пустую модель для формы добавления объекта
     * @return void
     */
    protected function initModel()
    {
        $this->model = new $this->modelClass;
    }
    
    /**
     * Зарегистрировать форму добавления сложного значения анкеты в модуле "анкеты" (QuestionaryModule)
     * @return void
     */
    protected function registerFormClip()
    {
        if ( $this->useClip )
        {
            Yii::app()->getModule($this->clipModule)->formClips[] = $this->clipId;
        }
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        // рисуем таблицу со списком добавленных элементов и кнопкой "добавить"
        $this->render($this->viewsPrefix.'grid');
        // отображаем скрытую форму добавления новой записи (она будет возникать в modal-окне)
        if ( $this->useClip )
        {//записываем ее в clip и выводим позже, в самом низу страницы если она конфликтует с формой
            // внутри которой находится текущий виджет
            $this->owner->beginClip($this->clipId);
            $this->render($this->viewsPrefix.'_form', array('model' => $this->model));
            $this->owner->endClip();
        }else
        {// выводим форму сразу как есть если она не конфликтует с другими элементами
            $this->render($this->viewsPrefix.'_form', array('model' => $this->model));
        }
    }
    
    /**
     * Установить параметры для кнопки "добавить" (в конце таблицы)
     * @return array
     */
    protected function initAddButtonOptions()
    {
        $defaults = array(
            'buttonType'  => 'link',
            'type'        => 'success',
            'size'        => 'large',
            'label'       => $this->addButtonLabel,
            'icon'        => 'plus white',
            'url'         => '#'.$this->modalId,
            'htmlOptions' => array(
                'id'          => $this->addButtonId,
                'class'       => 'pull-right',
                'data-toggle' => 'modal',
                'data-target' => '#'.$this->modalId,
            ),
        );
        $this->addButtonOptions = CMap::mergeArray($defaults, $this->addButtonOptions);
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
     * 
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
     * 
     * @return string
     *
     * @todo создать нормальный ряд таблицы с возможностью редактирования и удаления
     */
    protected function createAfterAddJs()
    {
        $js = "\n";
        // js для добавления новой строки в таблицу
        $js .= "$.fn.yiiGridView.update('{$this->rowIdPrefix}table');";
        // js для очистки полей формы после добавления новой записи
        $js .= $this->createClearFormJs();
    
        return $js;
    }
    
    /**
     * Получить JS для обновления содержимого таблицы по AJAX
     * 
     * @return string
     */
    protected function createGridRefreshJs()
    {
        return "$.fn.yiiGridView.update('{$this->rowIdPrefix}table');";
    }
    
    /**
     * JS для очистки полей формы после добавления новой записи
     * 
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
        $dataColumns   = $this->getDataColumns();
        // колонка с иконками действий
        $dataColumns[] = $this->getActionsColumn();
    
        return $dataColumns;
    }
    
    /**
     * Получить настройки для создания редактируемых колонок таблицы
     * 
     * @return array
     */
    protected function getDataColumns()
    {
        throw new CException('Эта функция должна быть переопределена');
    }
    
    /**
     * Получить колонку действий с записями
     * 
     * @return array
     */
    protected function getActionsColumn()
    {
        return array(
            'header'      => '<i class="icon icon-list"></i>&nbsp;',
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
     * 
     * @return array
     */
    protected function getYearPickerOptions()
    {
        return array(
            'minViewMode' => 'years',
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
            'name'     => $field,
            'class'    => 'bootstrap.widgets.TbEditableColumn',
            'editable' => array(
                'type'      => 'text',
                'title'     => $this->model->getAttributeLabel($field),
                'url'       => $this->updateUrl,
                'emptytext' => $this->getFieldEmptyText($field),
                'params' => array(
                    Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                ),
                'options' => array(
                    'mode' => 'inline',
                ),
            ),
        );
        if ( $value )
        {// подставляем значение по умолчанию (если есть)
            $options['value'] = $value;
        }
        return $options;
    }
    
    /**
     * Получить параметры для создания editable-колонки в таблице (многосторочное текстовое поле)
     *
     * @param string $field - поле модели для которого создается редактируемая колонка таблицы
     * @return array
     */
    protected function getTextAreaColumnOptions($field, $value=null)
    {
        $options = array(
            'name'     => $field,
            'class'    => 'bootstrap.widgets.TbEditableColumn',
            'editable' => array(
                'type'      => 'textarea',
                'title'     => $this->model->getAttributeLabel($field),
                'url'       => $this->updateUrl,
                'emptytext' => $this->getFieldEmptyText($field),
                'params' => array(
                    Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                ),
                'options' => array(
                    'mode' => 'inline',
                ),
            ),
        );
        if ( $value )
        {// подставляем значение по умолчанию (если есть)
            $options['value'] = $value;
        }
        return $options;
    }
    
    /**
     * Получить параметры для создания editable-колонки в таблице (обычный select без подгрузки элементов по AJAX)
     *
     * @param string $field 	 - поле модели для которого создается редактируемая колонка таблицы
     * @param array  $variants   - список вариантов для выбора
     * @param string $valueField - поле из которого берется отображаемое значание
     * @return array
     */
    protected function getSelectColumnOptions($field, $variants, $valueField='')
    {
        if ( ! $valueField )
        {
            $valueField = $field;
        }
        $options = array(
            'name'     => $field,
            'class'    => 'bootstrap.widgets.TbEditableColumn',
            'value'    => '$data->'.$valueField.';',
            'editable' => array(
                'type'      => 'select',
                'title'     => $this->model->getAttributeLabel($field),
                'url'       => $this->updateUrl,
                'emptytext' => $this->getFieldEmptyText($field),
                'source'    => $variants,
                'params' => array(
                    Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                ),
                'options' => array(
                    'mode' => 'inline',
                ),
            ),
        );
        return $options;
    }
    
    /**
     * Получить параметры для создания editable-колонки в таблице (select2 без подгрузки элементов по AJAX)
     *
     * @param string $field - поле модели для которого создается редактируемая колонка таблицы
     * @param array  $variants - список вариантов для выбора
     * @param string $valueField - поле из которого берется отображаемое значание
     * @return array
     * 
     * @deprecated используем обычный select в этом случае
     */
    protected function getStaticSelect2ColumnOptions($field, $variants, $valueField='level', $allowCustom=false)
    {
        $options = array(
            'name'     => $field,
            'class'    => 'bootstrap.widgets.TbEditableColumn',
            'value'    => '$data->'.$valueField.';',
            'editable' => array(
                'type'      => 'select2',
                'title'     => $this->model->getAttributeLabel($field),
                'url'       => $this->updateUrl,
                'emptytext' => $this->getFieldEmptyText($field),
                'select2'   => $this->getSelect2Options($variants, 'static', $allowCustom),
                'source'    => $variants,
                'params' => array(
                    Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                ),
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
        $variants = EcPurifier::getSelect2Options($variants);
        
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
     * Получить параметры для создания editable-колонки "вкл/выкл" в таблице
     *
     * @param string $field - поле модели для которого создается редактируемая колонка таблицы
     * @return array
     */
    protected function getToggleColumnOptions($field, $action)
    {
        return array(
            'toggleAction'  => $action,
            'name'          => $field,
            'value'         => '$data->'.$field.';',
            'class'         => 'bootstrap.widgets.TbToggleColumn',
            'checkedIcon'   => false,
            'uncheckedIcon' => false,
            'emptyIcon'     => false,
            'displayText'   => true,
            'sortable'      => false,
            'checkedButtonLabel'   => Yii::t('coreMessages', 'yes'),
            'uncheckedButtonLabel' => Yii::t('coreMessages', 'no'),
            'emptyButtonLabel'     => Yii::t('coreMessages', 'not_set'),
        );
    }
    
    /**
     * Получить текст, который отображается в случае когда поле таблицы не заполнено
     * @param string $field - поле модели для которого получается текст-заглушка
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
     * 
     * @return array
     */
    protected function getGridCriteria()
    {
        if ( $this->criteria AND (is_array($this->criteria) OR ( $this->criteria instanceof CDbCriteria )) )
        {
            return $this->criteria;
        }
        throw new CException('Не заданы условия выборки для записей в таблице');
    }
}