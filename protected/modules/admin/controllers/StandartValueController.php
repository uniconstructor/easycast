<?php

/**
 * Контроллер редактора стандартных значений
 * @todo прописать права доступа к действиям контроллера через rules()
 */
class StandartValueController extends Controller
{
    /**
     * @var string
     */
    public $layout='//layouts/column2';
    /**
     * @var string - класс модели, по умолчанию используемый для метода $this->loadModel()
     */
    protected $defaultModelClass = 'QActivityType';
    
    /**
     * @see CController::init()
     */
    public function init()
    {
        Yii::import('application.modules.questionary.models.*');
        parent::init();
    }
    
    /**
     * Отображение главного меню редактора стандартных значений
     */
    public function actionIndex()
    {
        // класс отображаемых значения - ВУЗ, театр или просто свойство
        $class = Yii::app()->request->getParam('class');
        // тип отображаемых значений - музыкальные ВУЗы или виды спорта
        $type = Yii::app()->request->getParam('type');
        // Заголовок
        $header = $this->getHeaderByClassAndType($class, $type);
        
        $this->render('index', array(
                'class'   => $class,
                'type'    => $type,
                'header'  => $header,
            ) 
        );
    }
    
    /**
     * Просмотреть одно стандартное значение для вида деятельности
     */
    public function actionViewActivityType()
    {
        $id    = Yii::app()->request->getParam('id');
        // класс отображаемых значения - ВУЗ, театр или просто свойство
        $class = Yii::app()->request->getParam('class');
        // тип отображаемых значений - музыкальные ВУЗы или виды спорта
        $type  = Yii::app()->request->getParam('type');
        
        $this->render('viewActivityType',array(
            'model' => $this->loadActivityModel($id),
            'class' => $class,
            'type'  => $type,
        ));
    }
    
    /**
     * Редактировать одно стандартное значение для вида деятельности
     */
    public function actionUpdateActivityType()
    {
        $id    = Yii::app()->request->getParam('id');
        // класс отображаемых значения - ВУЗ, театр или просто свойство
        $class = Yii::app()->request->getParam('class');
        // тип отображаемых значений - музыкальные ВУЗы или виды спорта
        $type  = Yii::app()->request->getParam('type');
        
        $model = $this->loadActivityModel($id);
        
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        
        if(isset($_POST['QActivityType']))
        {
            $model->attributes=$_POST['QActivityType'];
            if($model->save())
            {
                $this->redirect(array('index','class' => $class,'type' => $type));
            }
        }
        
        $this->render('updateActivityType',array(
            'model' => $model,
            'class' => $class,
            'type'  => $type,
        ));
    }
    
    /**
     * Создать новое стандартное значение для вида деятельности
     */
    public function actionCreateActivityType()
    {
        $model = new QActivityType;
        
        // класс отображаемых значения - ВУЗ, театр или просто свойство
        $class = Yii::app()->request->getParam('class');
        // тип отображаемых значений - музыкальные ВУЗы или виды спорта
        $type  = Yii::app()->request->getParam('type');
        
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        
        if(isset($_POST['QActivityType']))
        {
            $model->attributes=$_POST['QActivityType'];
            if( $model->save() )
            {
                $this->redirect(array('index','class' => $class,'type' => $type));
            }
        }
        
        $this->render('createActivityType',array(
            'model' => $model,
            'class' => $class,
            'type'  => $type,
        ));
    }
    
    /**
     * Заменить одно стандартное значение другим, обновив все связанные записи
     */
    public function actionReplaceActivityType()
    {
        $id    = Yii::app()->request->getParam('id');
        // класс отображаемых значения - ВУЗ, театр или просто свойство
        $class = Yii::app()->request->getParam('class');
        // тип отображаемых значений - музыкальные ВУЗы или виды спорта
        $type  = Yii::app()->request->getParam('type');
        
        $model = $this->loadActivityModel($id);
        if ( $newValue = Yii::app()->request->getParam('newvalue') )
        {
            $params = array(':name' => $type, ':value' => $newValue);
            $condition = "`name` = :name AND `value` = :value";
            if ( ! $new = QActivityType::model()->find($condition, $params) )
            {
                throw new CHttpException(404,'Не найдено такое стандартное значение. Замена не произведена');
            }
            
            if ( $model->updateRelatedActivities($model->value, $new->value) )
            {
                $model->delete();
                $this->redirect(array('index','class' => $class,'type' => $type));
            }
        }
        
        $this->render('replaceActivityType',array(
            'model' => $model,
            'class' => $class,
            'type'  => $type,
        ));
    }
    
    /**
     * Просмотреть ВУЗ
     */
    public function actionViewUniversity()
    {
        $id    = Yii::app()->request->getParam('id');
        // класс отображаемых значений - ВУЗ, театр или просто свойство
        $class = Yii::app()->request->getParam('class');
        // тип отображаемых значений - музыкальные ВУЗы или виды спорта
        $type  = Yii::app()->request->getParam('type');
        
        $this->render('viewUniversity',array(
            'model' => $this->loadUniversityModel($id),
            'class' => $class,
            'type'  => $type,
        ));
    }
    
    /**
     * Редактировать ВУЗ
     */
    public function actionUpdateUniversity($id)
    {
        $id    = Yii::app()->request->getParam('id');
        // класс отображаемых значения - ВУЗ, театр или просто свойство
        $class = Yii::app()->request->getParam('class');
        // тип отображаемых значений - музыкальные ВУЗы или виды спорта
        $type  = Yii::app()->request->getParam('type');
        
        $model = $this->loadUniversityModel($id);
        
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        
        if(isset($_POST['QUniversity']))
        {
            $model->attributes=$_POST['QUniversity'];
            if($model->save())
            {
                $this->redirect(array('index','class' => $class,'type' => $type));
            }
        }
        
        $this->render('updateUniversity',array(
            'model' => $model,
            'class' => $class,
            'type'  => $type,
        ));
    }
    
    /**
     * Добавить ВУЗ
     */
    public function actionCreateUniversity()
    {
        $model = new QUniversity;
        
        // класс отображаемых значения - ВУЗ, театр или просто свойство
        $class = Yii::app()->request->getParam('class');
        // тип отображаемых значений - музыкальные ВУЗы или виды спорта
        $type  = Yii::app()->request->getParam('type');
        
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        
        if(isset($_POST['QUniversity']))
        {
            $model->attributes=$_POST['QUniversity'];
            if( $model->save() )
            {
                $this->redirect(array('index','class' => $class,'type' => $type));
            }
        }
        
        $this->render('createUniversity',array(
            'model' => $model,
            'class' => $class,
            'type'  => $type,
        ));
    }
    
    /**
     * Заменить ВУЗ другим, обновив все связанные записи
     */
    public function actionReplaceUniversity()
    {
        $id    = Yii::app()->request->getParam('id');
        // класс отображаемых значения - ВУЗ, театр или просто свойство
        $class = Yii::app()->request->getParam('class');
        // тип отображаемых значений - музыкальные ВУЗы или виды спорта
        $type  = Yii::app()->request->getParam('type');
        
        $model = $this->loadUniversityModel($id);
        if ( $newId = Yii::app()->request->getParam('newid') )
        {
            if ( $model->updateRelatedInstances($model->id, $newId) )
            {
                $model->delete();
                $this->redirect(array('index','class' => $class,'type' => $type));
            }
        }
        
        $this->render('replaceUniversity',array(
            'model' => $model,
            'class' => $class,
            'type'  => $type,
        ));
    }
    
    /**
     * Одобрить введенный пользователем ВУЗ к использованию в списке поиска
     * (AJAX-запрос)
     * 
     * @return null
     */
    public function actionEnableUniversity()
    {
        $this->changeUniversityStatus(Yii::app()->request->getParam('id'), 1);
    }
    
    /**
     * Исключить ВУЗ из списка поиска
     * (AJAX-запрос)
     *
     * @return null
     */
    public function actionDisableUniversity()
    {
        $this->changeUniversityStatus(Yii::app()->request->getParam('id'), 0);
    }
    
    /**
     * Загрузить стандартное значение для редактирования
     * @param int $id
     * @throws CHttpException
     * @return QActivityType
     */
    public function loadActivityModel($id)
    {
        $model = QActivityType::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'Не найдено такое стандартное значение');
        return $model;
    }
    
    /**
     * Загрузить ВУЗ для редактирования
     * @param int $id
     * @throws CHttpException
     * @return QUniversity
     */
    public function loadUniversityModel($id)
    {
        $model = QUniversity::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'ВУЗ не найден');
        return $model;
    }
    
    /**
     * Получить название отображаемых стандартных значений
     * @param string $class - класс отображаемых значения - ВУЗ, театр или просто свойство
     * @param string $type - тип отображаемых значений - музыкальные ВУЗы или виды спорта
     * @return string
     */
    protected function getHeaderByClassAndType($class, $type)
    {
        if ( ! $class )
        {// пока не знаем что показывать
            return '';
        }
        if ( $class == 'activity' )
        {
            switch ( $type )
            {
                case 'looktype': return 'Типы внешности'; break;
                case 'haircolor': return 'Цвет волос'; break;
                case 'eyecolor': return 'Цвет глаз'; break;
                case 'physiquetype': return 'Телосложение'; break;
                case 'addchar': return 'Дополнительные характеристики'; break;
                case 'dancetype': return 'Виды танца'; break;
                case 'vocaltype': return 'Типы вокала'; break;
                case 'voicetimbre': return 'Тембры голоса'; break;
                case 'singlevel': return 'Уровень вокала'; break;
                case 'instrument': return 'Музыкальные инструменты'; break;
                case 'sporttype': return 'Виды спорта'; break;
                case 'extremaltype': return 'Экстремальные виды спорта'; break;
                case 'skill': return 'Дополнительные умения и навыки'; break;
                case 'language': return 'Иностранные языки'; break;
                case 'languagelevel': return 'Уровень владения иностранным языком'; break;
                case 'wearsize': return 'Размер одежды'; break;
                case 'shoessize': return 'Размер обуви'; break;
                case 'titsize': return 'Размер груди'; break;
                case 'rating': return 'Рейтинг'; break;
                
                default: return '!ERROR!NO_DATA!'; break;
            }
        }
        if ( $class == 'university' )
        {
            if ( $type == 'music' )
            {
                return 'Музыкальные ВУЗы';
            }
            if ( $type == 'theatre' )
            {
                return 'Театральные ВУЗы';
            }
            return '';
        }
        if ( $class == 'theatre' )
        {
            return 'Театры';
        }
    }
    
    /**
     * Изменить статус ВУЗа - то есть разрешить или запретить отображать его в списке 
     * стандартных значений при поиске 
     * 
     * @param int $id - id учебного заведения в таблице q_universities
     * @param int $system (1/0) - новое значение поля system для модели Quniversity
     * 
     * @return null
     */
    protected function changeUniversityStatus($id, $system)
    {
        if ( ! Yii::app()->user->checkAccess('Admin') )
        {// эта операция только для администраторов
            Yii::app()->end();
        }
        try
       {
            $university = $this->loadUniversityModel($id);
            $university->system = $system;
            $university->save();
            echo 'OK';
        }catch ( CHttpException $e )
        {
            throw new CException('University not found (id='.$universityId.')', 500);
        }
    }
}