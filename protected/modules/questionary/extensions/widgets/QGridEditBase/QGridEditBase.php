<?php

/**
 * Базовый класс для всех виджетов редактирования сложных полей анкеты
 * Позволяет создать редактируемую дополняемую по AJAX таблицу с произвольным набором полей.
 * Для добавления значения используется modal-окно с произвольной формой. 
 * Поля и верстка формы добавления нового значения настраиваются в файле views/_fields.php, 
 * который должен быть в каждом виджете-наследнике этого класса
 * Заменяет устеревший плагин multimodelform (убран из системы)
 * Начало и окончание формы формируется родительским классом: оно везде общее
 * 
 * Добавленые через форму значения сразу же подгружаются в таблицу по AJAX и сразу же
 * могут стать редактируемыми. 
 * 
 * Да-да, сразу же в добавленой строке таблицы можно править значения в отдельных ячейках
 * Да это тоже происходит через AJAX: при клике на ячейку таблицы
 * будет появлятся всплывающая форма редактирования значения (XEditable). 
 * И да, ее тоже можно настраивать, поддерживаются все виджеты ввода, 
 * которые есть в bootstrap-плагине XEditable
 * 
 * Это самый полезный виджет ввода во всей системе
 * 
 * @todo прописать проверки обязательных полей в init
 * @todo сделать настройку помещать/не помещать modal-формы в клип (сейчас всегда помещаем)
 *       (имеются в виду всплывающие формы, которые появляются при добавлении нового значения в таблицу)
 *       Тут важный момент: Yii не поддерживает вложенные виджеты ActiveForm, поэтому все modal-окна
 *       нельзя вывести скрытыми сразу же после таблицы. Их приходится тайно прятать в clip
 *       а потом, после закрытия основной формы выводить все скопом.
 * @todo сделать наследником более общего класса EditableGrid, чтобы можно было делать редактируемые таблицы
 *       не только для анкеты а для любых целей
 * @todo добавить asset со скриптом: общей JS-функцией для очистки полей modal-формы после добавления
 *       новой строки в таблицу. Получает массив, в котором ключи - названия полей формы и значения
 *       типы полей (синтаксис очистки обычных селектов и select2 различается)
 *       Вызывать эту функцию каждый рас после успешного добавления, убрать из дочерних плагинов
 *       использование createClearFormJs(). Она станет нужна только для тех случаем когда требуется нестандартная
 *       очистка формы (например если захотим там использовать какие-то экзотические виджеты ввода)
 * @todo при удалении последней записи (напримеру ВУЗа), проверять нужно ли теперь 
 *       сбросить обратно галочку (проф. актер), которая ставится только при наличии актерского образования 
 */
class QGridEditBase extends CWidget
{
    /**
     * @var Questionary
     */
    public $questionary;
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
     * @var CActiveRecord
    */
    protected $model;
    /**
     * @var string
     */
    protected $viewsPrefix = 'questionary.extensions.widgets.QGridEditBase.views.';
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        // вызов родительского init()
        // @todo понадобится когда растянем цепочку наследования и сделаем этот виджет не только для анкеты
        parent::init();
        // подключаем все модели, которые будем редактировать
        Yii::import('questionary.models.*');
        Yii::import('questionary.models.complexValues.*');
        
        if ( ! ( $this->questionary instanceof Questionary ) )
        {// модель анкеты необходима для работы всех виджетов
            throw new CException('В виджет '.get_class($this).' не передана анкета');
        }
        // создаем пустую модель для modal-формы добавления новой строки в таблицу
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
        $this->model->questionaryid = $this->questionary->id;
    }
    
    /**
     * Зарегистрировать форму добавления сложного значения анкеты в модуле "анкеты" (QuestionaryModule)
     * @return void
     */
    protected function registerFormClip()
    {
        $this->clipId = $this->formId.'-clip';
        Yii::app()->getModule('questionary')->formClips[] = $this->clipId;
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        // рисуем таблицу со списком добавленных элементов и кнопкой "добавить"
        $this->render($this->viewsPrefix.'grid');
        
        // отображаем скрытую форму добавления новой записи (она будет возникать в modal-окне)
        // записываем ее в clip и выводим позже, в самом низу страницы иначе она конфликтует с формой анкеты
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
            'header'      => '&nbsp;',
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
     * @return array
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
            ),
        );
        if ( $value )
        {// подставляем значение по умолчанию (если есть)
            $options['value'] = $value;
        }
        
        return $options;
    }
    
    /**
     * Получить параметры для создания editable-колонки в таблице (select2 без подгрузки элементов по AJAX)
     *
     * @param string $field - поле модели для которого создается редактируемая колонка таблицы
     * @param array $variants - список вариантов для выбора
     * @return array
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
                'params'    => array(
                    Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                ),
                'select2' => $this->getSelect2Options($variants, 'static', $allowCustom),
                'source'  => $variants,
            ),
        );
        
        return $options;
    }
    
    /**
     * Получить общие параметры для создания select2-виджета с выбором одного значения из списка стандартных.
     * 
     * @param array $variants - значения для выпадающего списка
     * @param string $type - способ подгрузки вариантов значений в список
     *                       static - все варианты задаются изначально и не меняются (для небольших списков)
     * @param string $allowCustom - разрешить ли вводить и сохранять свое значение в этом поле?
     *                              true: можно ввести и сохранить в поле свое значение 
     *                              если в выпадающем списке нет ничего подходящего
     *                              false: можно выбрать и сохранить только разрешенный вариант из списка
     * @return array
     * 
     * @todo доделать вариант с динамической подгрузкой значений по AJAX, 
     *       но похоже что параметры этой функции этого не позволят, так что будет проще написать отдельный метод
     *       Понять, можно ли не меняя набор параметров, 
     *       пользуясь только полями класса получить всё необходимое для настройки
     *       select2 с подгрузкой элементов по AJAX
     * @todo выяснить зачем планировался параметр $type и стоит ли удалять его
     */
    protected function getSelect2Options($variants, $type='static', $allowCustom=false)
    {
        $options = array(
            'maximumSelectionSize' => 0,
            'placeholder'          => 'Выбрать...',
            'placeholderOption'    => '',
            'multiple'             => false,
        );
        //$variants = ECPurifier::getSelect2Options($variants);
        
        if ( $allowCustom )
        {// разрешить ли вводить и сохранять свое значение в этом поле?
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
     */
    protected function getYearColumnOptions($field)
    {
        return array(
            'name'  => $field,
            'value' => 'trim($data->'.$field.');',
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
     * Получить критерий выборки записей для списка редактирования
     * @return array
     */
    protected function getGridCriteria()
    {
        return array(
            'condition' => "`questionaryid` = '{$this->questionary->id}'",
        );
    }
    
    /**
     * Получить список вариантов выбора для выпадающего списка
     * @param string $type
     * @param bool $excludeSelected - оставить в списке только те значения, которые еще не 
     *                                добавлены к анкете 
     *                                (чтобы нельзя было 2 раза добавить один вид спорта или язык)
     * @return string
     */
    protected function getActivityOptions($type, $excludeSelected=true)
    {
        $selected = array();
        if ( $excludeSelected )
        {
            $criteria = new CDbCriteria();
            $criteria->index = 'value';
            
            $values   = QActivity::model()->forQuestionary($this->questionary->id)->withType($type)->
                except(array('custom'))->findAll($criteria);
            if ( $values )
            {
                $selected = CHtml::listData($values, 'value', 'value');
            }
        }
        
        $options = QActivityType::model()->forActivity($type)->except($selected)->findAll();
        $options = CHtml::listData($options, 'value', 'translation');
        
        return ECPurifier::getSelect2Options($options);
    }
}