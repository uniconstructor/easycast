<?php

// подключение родительского класса
Yii::import('questionary.extensions.widgets.QGridEditBase.QGridEditBase');

/**
 * Виджет для редактирования списка наград и достижений участника
 *
 * @package    easycast
 * @subpackage questionary
 */
class QEditAwards extends QGridEditBase
{
    /**
     * @var string - сообщение перед удалением записи
     */
    public $deleteConfirmation = 'Удалить эту запись?';
    /**
     * @var string - url по которому происходит удаление записей
     */
    public $deleteUrl = "/questionary/qAward/delete";
    /**
     * @var string - url по которому происходит создание записей
     */
    public $createUrl = "/questionary/qAward/create";
    /**
     * @var string - url по которому происходит обновление записей
     */
    public $updateUrl = "/questionary/qAward/update";
    /**
     * @var string - префикс html-id для каждой строки таблицы (чтобы можно было удалять строки)
     */
    public $rowIdPrefix = 'award_row_';
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass = 'QAward';
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     */
    public $fields = array('name', 'nomination', 'countryid', 'year');
    /**
     * @var string - html-id формы для ввода новой записи
    */
    public $formId = 'award-form';
    /**
     * @var string - html-id modal-окна для ввода новой записи
     */
    public $modalId = 'award-modal';
    /**
     * @var string - html-id кнопки для ввода новой записи
     */
    public $addButtonId = 'add-award-button';
    /**
     * @var string - заголовок всплывающего окна с формой добавления новой записи
     */
    public $addButtonLabel = 'Добавить';
    /**
     * @var string - заголовок всплывающего окна с формой добавления новой записи
     */
    public $modalHeader = 'Добавить информацию о достижении';
    /**
     * @var array - список текстов-заглушек, которые отображаются в случае, когда поле не заполнено
     */
    public $emptyTextVariants = array(
        'name'       => '[не указано]',
        'nomination' => '[не указана]',
        'countryid'  => '[не указана]',
        'year'       => '[не указан]',
    );
    /**
     * @var string - адрес по которому происходит получение значений для select2
     */
    //public $optionsListUrl = '/questionary/qAward/getCountryList';
    
    /**
     * @see QGridEditBase::init()
     */
    public function init()
    {
        // для списка стран в форме подключаем модель для работы со странами
        Yii::import('ext.CountryCitySelectorRu.models.*');
        // после чего с чистой совестью продолжаем инициализацию
        parent::init();
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
     * js для очистки полей формы после добавления новой записи
     * @return string
     */
    protected function createClearFormJs()
    {
        $js  = '';
        $js .= "\$('#{$this->modelClass}_name').val('');\n";
        $js .= "\$('#{$this->modelClass}_nomination').val('');\n";
        $js .= "\$('#{$this->modelClass}_countryid').select2('val', '');\n";
        $js .= "\$('#{$this->modelClass}_year').val('');\n";

        return $js;
    }

    /**
     * Получить настройки для колонок таблицы с данными
     *
     * @return array
     */
    protected function getDataColumns()
    {
        $countries = $this->createCountryList();
        return array(
            // название
            $this->getTextColumnOptions('name'),
            // номинация
            $this->getTextColumnOptions('nomination'),
            // страна
            $this->getStaticSelect2ColumnOptions('countryid', $countries, 'countryName'),
            // год
            $this->getYearColumnOptions('year'),
        );
    }
    
    /**
     * Получить список стран для поля "страна"
     * @return array
     */
    protected function createCountryList()
    {
        // извлекаем все страны в алфавитном порядке
        $criteria = new CDbCriteria();
        $criteria->index = 'id';
        $criteria->order = '`name`';
        $models = CSGeoCountry::model()->findAll($criteria);
        
        // перемещаем Россию в начало списка, чтобы не приходилось каждый раз искать
        $russia = $models[3159];
        unset($models[3159]);
        array_unshift($models, $russia);
        // создаем массив для выпадающего списка
        return CHtml::listData($models, 'id', 'name');
    }
}