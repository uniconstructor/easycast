<?php

/**
 * Базовый класс для всех контроллеров приложения
 * Все контроллеры приложения должны быть наследованы от него
 * 
 * @todo убрать sideBar, pageHeader и subtitle если их использование будет 
 *       ограничиваться только темой оформления SmartAdmin 
 * @todo языковые строки
 * @todo переместить методы ведения системных логов модуль log когда станет 
 *       понятно как находясь там достать извне все необходимые данные
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
     * @see CController::beforeAction()
     */
    public function beforeAction($action)
    {
        try
        {// для аналитики: записываем каждое совершенное пользователем действие
            $data = array(
                'level'  => 'action',
            );
            self::logSystemData($data);
        }catch ( Exception $e )
        {// сбор аналитики не должен мешать нормальной работе системы
            $msg = "Exception: ".$e->getMessage().' ('.$e->getFile().':'.$e->getLine().")\n".$e->getTraceAsString()."\n";
            Yii::log($msg, CLogger::LEVEL_ERROR, 'application');
        }
        return parent::beforeAction($action);
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
     * Получить название выполняемого контроллером действия (для составления ленты системных событий)
     * 
     * @param  string $module
     * @param  string $controller
     * @param  string $action
     * @return string
     * 
     * @todo заготовка для будущего метода - прописать языковые строки с названиями всех действий
     *       контроллеров во всех модулях
     */
    public static function getActionTitle($action, $controller=null, $module=null)
    {
        return null;
    }
    
    ////// временные методы //////
    
    /**
     * 
     * @param  string $message
     * @param  string $category
     * @param  array $params
     * @return void
     */
    public function logTargetEvent($targetType, $targetId=0, array $params=array())
    {
        $params['targettype'] = $targetType;
        $params['targetid']   = $targetId;
        
        return $this->logSystemData($params);
    }
    
    /**
     * 
     * @param  array $params
     * @return void
     * 
     * @todo заменить path на referer чтобы можно было отслеживать путь
     */
    public function logSystemData(array $params=array())
    {
        $categoryComponents = array();
        $template = array(
            'level'      => 'info',
            'category'   => 'easycast',
            'targettype' => 'system',
            'targetid'   => 0,
            'sourcetype' => 'guest',
            'sourceid'   => 0,
            'logtime'    => time(),
        );
        if ( ! Yii::app()->user->isGuest AND Yii::app()->user->id )
        {
            $template['sourcetype'] = 'user';
            $template['sourceid']   = Yii::app()->user->id;
            $template['userid']     = Yii::app()->user->id;
        }
        if ( Yii::app()->request->userHostAddress )
        {
            $template['userip'] = Yii::app()->request->userHostAddress;
        }
        if ( $module = $this->getModule() AND is_object($module) )
        {
            $template['module']   = $module->getId();
            $categoryComponents[] = $template['module'];
        }
        if ( $this->getId() )
        {
            $template['controller'] = $this->getId();
            $categoryComponents[]   = $template['controller'];
        }
        if ( $action = $this->getAction() AND is_object($action) )
        {
            $template['action']   = $action->getId();
            $categoryComponents[] = $template['action'];
        }
        if ( Yii::app()->request->urlReferrer )
        {
            $template['referer'] = Yii::app()->request->urlReferrer;
        }
        if ( ! empty($categoryComponents) )
        {
            $template['category'] = implode('.', $categoryComponents);
        }
        if ( $this->pageTitle )
        {
            $template['title'] = $this->pageTitle;
        }
        $this->saveLog(CMap::mergeArray($template, $params));
    }
    
    /**
     * 
     * @param  array $log
     * @return void
     */
    protected function saveLog($log)
    {
        Yii::app()->db->createCommand()->insert('{{system_logs}}', $log);
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