<?php

/**
 * Виджет для редактирования фильмографии
 */
class QEditFilms extends CWidget
{
    /**
     * @var Questionary
     */
    public $questionary;
    /**
     * @var string - сообщение перед удалением записи
     */
    public $deleteConfirmation;
    /**
     * @var string - всплывающая подсказка над иконкой удаления записи
     */
    public $deleteButtonLabel;
    /**
     * @var string - url по которому происходит удаление записей
     */
    public $deleteUrl = "/questionary/qFilmInstance/delete";
    /**
     * @var string - url по которому происходит создание записей
     */
    public $createUrl = "/questionary/qFilmInstance/create";
    /**
     * @var string - url по которому происходит обновление записей
     */
    public $updateUrl = "/questionary/qFilmInstance/update";
    /**
     * @var string - префикс html-id для каждой строки таблицы (чтобы можно было удалять строки)
     */
    public $rowIdPrefix = 'film_row_';
    /**
     * @var string - префикс для свойства name у editable полей
     */
    public $rowEditPrefix;
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass = 'QFilmInstance';
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     */
    public $fields = array('name', 'role', 'year', 'director');
    /**
     * @var string - html-id формы для ввода новой записи
     */
    public $formId = 'film-instance-form';
    /**
     * @var string - html-id modal-окна для ввода новой записи
     */
    public $modalId = 'film-instance-modal';
    /**
     * @var string - html-id кнопки для ввода новой записи
     */
    public $addButtonId = 'add-film-instance-button';
    /**
     * @var string - заголовок всплывающего окна с формой добавления новой записи
     */
    public $modalHeader = 'Добавить фильмографию';
    
    /**
     * @var CActiveRecord
     */
    protected $model;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        parent::init();
        
        if ( ! ( $this->questionary instanceof Questionary ) )
        {
            throw new CException('В виджет '.get_class($this).' не передана анкета');
        }
        // создаем пустую модель для формы
        $this->initModel();
        if ( ! $this->rowEditPrefix )
        {
            $this->rowEditPrefix = $this->modelClass;
        }
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
     * @see CWidget::run()
     */
    public function run()
    {
        // рисуем таблицу со списком добавленных элементов и кнопкой "добавить"
        $this->render('films');
        // отображаем скрытую форму добавления новой записи (она будет возникать в modal-окне)
        $this->render('_form', array('model' => $this->model));
    }
    
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
            //$(rowSelector).remove();
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
        $newRow = $this->createNewTableRowJs();
        // js для добавления новой строки в таблицу
        $js .= "\$('.{$this->rowIdPrefix}table table > tbody:last').append('{$newRow}');";
        // js для очистки полей формы после добавления новой записи
        $js .= $this->createClearFormJs();
        
        return $js;
    }
    
    /**
     * 
     * @return string
     */
    protected function createNewTableRowJs()
    {
        //$row = "<tr id=\'{$this->rowIdPrefix}\'>";
        $row = "<tr>";
        foreach ( $this->fields as $field )
        {
            $row .= "<td>' + data.{$field} + '</td>";
        }
        $row .= "<td>&nbsp</td>";
        $row .= "</tr>";
        
        return $row;
    }
    
    /**
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
        $dataColumns = $this->getDataColumns();
        // колонка с иконками действий
        $dataColumns[] = $this->getActionsColumn();
        
        return $dataColumns;
    }
    
    /**
     * 
     * @return void
     */
    protected function getDataColumns()
    {
        return array(
            // название фильма
            array(
                'name'  => 'name',
                'class' => 'bootstrap.widgets.TbEditableColumn',
                'editable' => array(
                    'type'      => 'text',
                    'title'     => $this->model->getAttributeLabel('name'),
                    'url'       => $this->updateUrl,
                    'emptytext' => '[не указано]',
                    'params' => array(
                        Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                    ),
                    /*'onSave' => 'js:function(e, params) {
                        var alertBlock = "<div style=\"text-align:center;\" class=\"alert in alert-block fade alert-success\">";
                        alertBlock    += "<a href=\"#\" class=\"close\" data-dismiss=\"alert\">&times;</a>Сохранено</div>";
                        $("#'.$this->rowIdPrefix.'table").append(alertBlock);
                    }',*/
                ),
            ),
            // роль
            array(
                'name'  => 'role',
                'class' => 'bootstrap.widgets.TbEditableColumn',
                'editable' => array(
                    'type'      => 'text',
                    'title'     => $this->model->getAttributeLabel('role'),
                    'url'       => $this->updateUrl,
                    'emptytext' => '[не указана]',
                    'params' => array(
                        Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                    ),
                ),
            ),
            // год
            array(
                'name'  => 'year',
                'value' => '$data->year;',
                'class' => 'bootstrap.widgets.TbEditableColumn',
                'editable' => array(
                    'type'      => 'date',
                    'title'     => $this->model->getAttributeLabel('date'),
                    'url'       => $this->updateUrl,
                    'emptytext' => '[не указан]',
                    'format'    => 'yyyy',
                    'params' => array(
                        Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                    ),
                    'options' => array(
                        'datepicker' => array(
                            'minViewMode' => 'years',
                            'language'    => 'en',
                            'autoclose'   => true,
                            'forceParse'  => false,
                        ),
                    ),
                ),
            ),
            // режиссер
            array(
                'name'  => 'director',
                'class' => 'bootstrap.widgets.TbEditableColumn',
                'editable' => array(
                    'type'      => 'text',
                    'title'     => $this->model->getAttributeLabel('director'),
                    'url'       => $this->updateUrl,
                    'emptytext' => '[не указан]',
                    'params' => array(
                        Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                    ),
                ),
            ),
        );
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
}