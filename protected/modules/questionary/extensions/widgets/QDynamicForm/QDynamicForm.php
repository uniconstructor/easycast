<?php

/**
 * Виджет динамической формы анкеты участника, составляемый по частям, 
 * в зависимости от того на какую роль подается заявка
 * 
 * 
 * В форме могут быть простые значения и списки
 * Простое поле - это одно скалярное значение, которое хранится в таблице questionary
 *                для него чаще всего нужно вывести один элемент: например текстовое поле или галочку, и т. д.
 * Список       - это, как правило, набор связанных с анкетой объектов, которых может быть
 *                произвольное количество: например список фильмов, в которых снимался актер
 *                учебные заведения, которые он окончил, и т. д.
 *                Для таких полей выводится или виджет select2 или виджет ввода сложных значений,
 *                Все виджеты ввода сложных значений наследуются от класса QGridEditBase
 * @todo добавить возможность создавать форму одновременно для нескольких ролей 
 * @todo предусмотреть 2 варианта отображения для списков: регистрация и обычный пользователь
 *       при регистрации добавляется максимум 1 значение или указывается только 1 поле из возможных
 */
class QDynamicForm extends CWidget
{
    /**
     * @var QDynamicFormModel - создаваемая или редактируемая анкета
     */
    public $model;
    
    /**
     * @var Questionary - создаваемая или редактируемая анкета
     */
    protected $questionary;
    /**
     * @var EventVacancy - роль, к которой прикрепляется форма
     *                     из нее берется набор полей, которые необходимы для регистрации
     */
    protected $vacancy;
    /**
     * @var array - полный список всех полей формы, которые нужно указать перед подачей заявки
     */
    protected $userFields = array();
    /**
     * @var array - список незаполненных полей формы, которые нужно указать перед подачей заявки
     *              (только для зарегистрированных пользователей)
     */
    protected $emptyUserFields = array();
    /**
     * @var array - список дополнительных полей роли, которые нужно указать перед подачей заявки
     *              (это список всех обязательных полей роли минус уже заполненные поля анкеты)
     */
    //protected $emptyExtraFields = array();
    /**
     * @var array - список полей формы для которых есть заранее определенная разметка
     */
    protected $defaultLayouts = array(
        'email',
        'firstname',
        'lastname',
        'gender',
        'birthdate',
        'mobilephone',
        'homephone',
        'cityid',
        //'countryid',
        //'nativecountryid',
        'height',
        'weight',
        'chestsize',
        'waistsize',
        'hipsize',
        'shoessize',
        //'passportexpires',
        //'fbprofile',
        //'vkprofile',
        'photo',
        'policyagreed',
    );
    /**
     * @var array - список типов полей формы для которых есть заранее определенная разметка
     */
    protected $defaultTemplates = array(
        'country',
        'date',
        'phone',
        'text',
        'textarea',
        'url',
    );
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        parent::init();
        
        if ( ! $this->model )
        {
            throw new CException('Не передан обязательный параметр');
        }
        
        //Yii::import('ext.CountryCitySelectorRu.models.*');
        
        $this->questionary = $this->model->questionary;
        $this->vacancy     = $this->model->vacancy;
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('form', array(
            'model' => $this->model,
        ));
    }
    
    /**
     * Получить разметку для одного поля анкеты
     * 
     * @param TbActiveForm $form
     * @param CActiveRecord $model
     * @param QUserField $field
     * @return string
     */
    protected function getUserFieldLayout($form, $model, $field)
    {
        $htmlOptions = $this->getUserFieldHtmlOptions($field);
        $rowOptions  = $this->getUserFieldRowOptions($field);
        
        if ( in_array($field->name, $this->defaultLayouts ) )
        {// сначала смотрим есть ли унас разметка под конкретное поле со всеми его особенностями
            return $this->render('fields/'.$field->name, array(
                'form'  => $form,
                'model' => $model,
            ), true);
        }elseif ( in_array($field->type, $this->defaultTemplates ) )
        {// для всех остальных полей используем более общий шаблон
            return $this->render('templates/'.$field->type, array(
                'form'        => $form,
                'model'       => $model,
                'attribute'   => $field->name,
                'htmlOptions' => $htmlOptions,
                'rowOptions'  => $rowOptions,
            ), true);
        }else
        {
            throw new CException('Неизвестный тип поля: "'.$field->type.'"');
        }
    }
    
    /**
     * Получить разметку для одного поля анкеты
     * 
     * @param TbActiveForm  $form - форма анкеты
     * @param CActiveRecord $model - модель для динамической формы
     * @param QUserField    $field - поле, привязанное к роли
     * @return string
     */
    /*protected function getPlainUserFieldLayout($form, $model, $field)
    {
        $htmlOptions = $this->getUserFieldHtmlOptions($field);
        $rowOptions  = $this->getUserFieldRowOptions($field);
        
        switch ( $field->type )
        {
            case 'text':     return $form->textFieldRow($form, $field->name, $htmlOptions, $rowOptions);
            case 'textarea': return $form->textAreaRow($form, $field->name, $htmlOptions, $rowOptions);
            //case 'select':   return array('null', 'emptystring');
            //case 'slider':   return array('null', 'zero', 'emptystring');
            //case 'phone':    return array('null', 'zero', 'emptystring');
            //case 'url':      return array('null', 'zero', 'emptystring');
            //case 'badge':    return array('null');
            //case 'city':     return array('zero');
            //case 'checkbox': return array('null');
            //case 'toggle':   return array('null');
            //case 'date':     return array('null', 'zero', 'emptystring');
            //case 'country':  return array('null', 'zero');
            default: throw new CException('Неизвестный поля анкеты');
        }
    }*/
    
    /**
     * Получить разметку для поля анкеты, которое содержит список значений
     *
     * @param TbActiveForm  $form - форма анкеты
     * @param CActiveRecord $model - модель для динамической формы
     * @param QUserField    $field - поле, привязанное к роли
     * @return string
     */
    protected function getComplexUserFieldLayout($form, $model, $field)
    {
        
    }
    
    /**
     * Получить разметку для дополнительного поля, привязанного к роли
     *
     * @param TbActiveForm      $form - форма анкеты
     * @param QDynamicFormModel $model - модель для динамической формы
     * @param ExtraField        $field - поле, привязанное к роли
     * @return string
     */
    protected function getExtraFieldLayout($form, $model, $field)
    {
        $htmlOptions = $this->getExtraFieldHtmlOptions($field);
        $rowOptions  = $this->getExtraFieldRowOptions($field);
        
        $fieldName = $model->extraFieldPrefix.$field->name;
        switch ( $field->type )
        {
            case 'text':     return $form->textFieldRow($model, $fieldName, $htmlOptions, $rowOptions);
            case 'textarea': return $form->textAreaRow($model, $fieldName, $htmlOptions, $rowOptions);
            default: throw new CException('Неизвестный тип дополнительного поля');
        }
    }
    
    /**
     * 
     * @param QUserField $field
     * @return array
     */
    protected function getUserFieldHtmlOptions($field)
    {
        $options = array();
        switch ( $field->type )
        {
            case 'phone':
                $options = array(
                    'size'        => 60,
                    'maxlength'   => 20,
                    'placeholder' => '(987)654-32-10',
                );
            break;
            case 'country':
                $options = array(
                    'asDropDownList' => true,
                    'data'           => $this->createCountryList(),
                );
            break;
            case 'url':
                $options = array(
                    'size'      => 60, 
                    'maxlength' => 255,
                );
            break;
            case 'date':
                $options = array(
                    'options' => array(
                        'language'  => 'ru',
                        'format'    => 'dd.mm.yyyy',
                        'startView' => 'decade',
                        'weekStart' => 1,
                        'autoclose' => true,
                    ),
                );
            break;
        }
        
        return $options;
    }
    
    /**
     * 
     * @param QUserField $field
     * @return array
     */
    protected function getUserFieldRowOptions($field)
    {
        $options = array();
        
        switch ( $field->type )
        {
            case 'phone':
                $options['hint'] = '<i class="icon-eye-slash" data-toggle="tooltip" data-title="Эта информация не будет опубликована"></i>';
            break;
            case 'date':
                $options['prepend'] = '<i class="icon-calendar"></i>';
            break;
        }
        
        return $options;
    }
    
    /**
     *
     * @param ExtraField $field
     * @return array
     */
    protected function getExtraFieldHtmlOptions($field)
    {
        $options = array();
        
        switch ( $field->type )
        {
            case 'text':
                $options['style'] = 'width:100%;';
            break;
            case 'textarea':
                $options['style'] = 'width:100%;';
            break;
        }
        
        return $options;
    }
    
    /**
     *
     * @param ExtraField $field
     * @return array
     */
    protected function getExtraFieldRowOptions($field)
    {
        $options = array();
        
        $options['hint'] = '';
        if ( $field->description )
        {
            $options['hint'] = $field->description.$options['hint'];
        } 
        
        return $options;
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
    
    /**
     * 
     * @return void
     */
    public function getCity()
    {
        if ( $this->questionary->cityobj )
        {
            return $this->questionary->cityobj->name;
        }
        return '';
    }
}