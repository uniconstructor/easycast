<?php

// подключение родительского класса
Yii::import('questionary.extensions.widgets.QGridEditBase.QGridEditBase');

/**
 * Виджет для ввода музыкальных инструментов которыми владеет участник
 * Вводится название (из списка или свое) и уровень владения
 *
 * @package    easycast
 * @subpackage questionary
*/
class QEditInstruments extends QGridEditBase
{
    /**
     * @var string - сообщение перед удалением записи
     */
    public $deleteConfirmation = 'Убрать этот музыкальный инструмент из списка навыков?';
    /**
     * @var string - url по которому происходит удаление записей
     */
    public $deleteUrl   = "/questionary/qInstrument/delete";
    /**
     * @var string - url по которому происходит создание записей
     */
    public $createUrl   = "/questionary/qInstrument/create";
    /**
     * @var string - url по которому происходит обновление записей
     */
    public $updateUrl   = "/questionary/qInstrument/update";
    /**
     * @var string - префикс html-id для каждой строки таблицы (чтобы можно было удалять строки)
     */
    public $rowIdPrefix = 'instrument_row_';
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass  = 'QInstrument';
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     */
    public $fields = array('name', 'level');
    /**
     * @var string - html-id формы для ввода новой записи
     */
    public $formId = 'instrument-form';
    /**
     * @var string - html-id modal-окна для ввода новой записи
     */
    public $modalId = 'instrument-modal';
    /**
     * @var string - html-id кнопки для ввода новой записи
     */
    public $addButtonId    = 'add-instrument-button';
    /**
     * @var string - заголовок всплывающего окна с формой добавления новой записи
     */
    public $addButtonLabel = 'Добавить';
    /**
     * @var string - заголовок всплывающего окна с формой добавления новой записи
     */
    public $modalHeader    = 'Добавить музыкальный инструмент';
    /**
     * @var array - список текстов-заглушек, которые отображаются в случае, когда поле не заполнено
     */
    public $emptyTextVariants = array(
        'name'  => '[не указан]',
        'level' => '[не указан]',
    );
    
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
     * js для очистки полей формы после добавления новой записи
     * @return string
     */
    protected function createClearFormJs()
    {
        $js  = '';
        $js .= "\$('#{$this->modelClass}_name').select2('val', '');\n";
        $js .= "\$('#{$this->modelClass}_level').select2('val', '');\n";
        
        return $js;
    }
    
    /**
     * Получить настройки для колонок таблицы с данными
     * 
     * @return array
     * 
     * @todo для админов: разрешить править название в ячейках таблицы при помощи xeditable
     */
    protected function getDataColumns()
    {
        return array(
            // музыкальный инструмент
            array(
                'name'  => 'name',
                'value' => '$data->name;',
            ),
            // уровень навыка
            $this->getStaticSelect2ColumnOptions('level',
                $this->questionary->getFieldVariants('level', false),
                'getSkillLevel()'
            ),
        );
    }
}