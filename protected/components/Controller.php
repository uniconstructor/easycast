<?php

/**
 * Базовый класс для всех контроллеров приложения
 * Все контроллеры приложения должны быть наследованы от него
 * 
 * @todo убрать sideBar, pageHeader и subtitle если их использование будет 
 *       ограничиваться только темой оформления SmartAdmin 
 * @todo языковые строки
 */
class Controller extends RController
{
    /**
     * @var array - левая панель навигации в меню
     */
    public $sideBar = array();
    /**
     * @var string
     */
    public $pageHeader;
    /**
     * @var string
     */
    public $subTitle;
    /**
     * @var bool - включить/выключить инструменты и счетчики веб-аналитики на странице (Яндекс, Google)
     */
    public $analytics = true;
    
    /**
     * @var string - класс модели, по умолчанию используемый для метода $this->loadModel()
     *               должен быть переопределен при наследовании
     */
    protected $defaultModelClass;
     
    /**
     * @see CController::filters()
     * 
     * @todo совместить с полной заменой accessFilter на RBAC если будет возможность
     */
    public function filters()
    {
        return array(
            'ECReferalFilter' => array(
                // фильтр обработки ссылок с токенами
                'application.filters.ECReferalFilter',
            ),
            // @todo фильтр, который заставляет использовать только защищенное соединение 
            //array(
            //    'ext.sweekit.filters.SwProtocolFilter - parse',
            //    'mode' => 'https',
            //),
        );
    }
    
    /**
     * @see parent::behaviors()
     * 
     * @return array
     */
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        // Подключаем ко всем контроллерам проекта методы для вывода js-кода: 
        // redirectJs(), renderJs(), renderJson()
        $behaviors = array(
            'sweelixRendering' => array(
                'class' => 'ext.sweekit.behaviors.SwRenderBehavior',
            ),
        );
        return CMap::mergeArray($parentBehaviors, $behaviors);
    }
    
    /**
     * Найти модель по ее id: этот метод используется во всех действиях контроллеров перед  
     * совершением любых операций с моделями  
     * Если модель данных не найдена - метод выбросит http-исключение
     *
     * @param  int    $id         - id загружаемой модели
     * @param  string $modelClass - имя класса модели: должно указывать на AR-класс 
     *                              Необязательный параметр, если не указан - то будет 
     *                              использован класс, заданный в $this-> 
     * @return CActiveRecord - запись с указанным id
     * @throws CHttpException 
     */
    public function loadModel($id, $modelClass='')
    {
        if ( ! $modelClass )
        {
            if ( ! $modelClass = $this->defaultModelClass )
            {// не указан класс загружаемой модели
                throw new CHttpException(500, Yii::t('coreMessages', 'no_default_model_for_controller', array(
                    '{сlassName}' => get_class($this),
                )));
            }
        }
        if ( ! is_subclass_of($modelClass, 'CActiveRecord') )
        {// класс модели указан неправильно
            throw new CHttpException(500, Yii::t('coreMessages', 'incorrect_default_model_for_controller', array(
                '{сlassName}' => get_class($this),
                '{modelName}' => $modelClass,
            )));
        }
        // ищем в базе модель с таким id
        $model = $modelClass::model()->findByPk($id);
        if ( $model === null )
        {
            throw new CHttpException(404, 'Запрошенная модель не существует. (id='.$id.')');
        }
        return $model;
    }
    
    /**
     * Performs the AJAX validation.
     *
     * @param  CModel the model to be validated
     * @return void
     */
    protected function performAjaxValidation($model)
    {
        if ( isset($_POST['ajax']) AND mb_strstr($_POST['ajax'], '-form') )
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}