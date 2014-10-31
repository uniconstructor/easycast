<?php

/**
 * Контроллер для landing-page проекта "Топ модель по-русски"
 * 
 * @todo удалить после завершения проекта
 * @todo решить задачу в общем виде: сделать конструктор таких страниц для люого проекта и любой роли
 * 
 * @deprecated
 */
class TopModelController extends Controller
{
    /**
     * @see CController::init()
     */
    public function init()
    {
        $projectsModule = Yii::app()->getModule('projects');
        $userModule     = Yii::app()->getModule('user');
    }

    /**
     *
     * @return void
     */
    public function actionIndex()
    {
        // id роли
        // @todo добавить проверку статуса роли
        $vid = Yii::app()->request->getParam('vid', 600);
        $vacancy = $this->loadModel($vid);
        // id анкеты: разрешаем только регистрацию новых пользователей или подачу заявки от своего имени
        // подача заявки от имени другого участника доступна только в админке
        if ( Yii::app()->user->checkAccess('Admin') )
        {
            $qid = 0;
        }else
        {
            $qid = Yii::app()->getModule('questionary')->getCurrentQuestionaryId();
        }
        if ( $qid )
        {// заявку подает существующий участник
            $questionary = Questionary::model()->findByPk($qid);
            $model = new QDynamicFormModel('application');
            $model->displayFilled = false;
        }else
        {// участник регистрируется через подачу заявки (либо админ создает нового участника через эту форму)
            $questionary = new Questionary();
            $model = new QDynamicFormModel('registration');
        }
        // запоминаем роль и анкету, от имени которой будет подаваться заявка
        $model->vacancy     = $vacancy;
        $model->questionary = $questionary;
        
        if ( $formData = Yii::app()->request->getParam('QDynamicFormModel') )
        {// форма заполнена: пришли данные из формы регистрации пользователя
            $model->attributes = $formData;
            if ( isset($formData['galleryid']) AND $formData['galleryid'] )
            {
                $model->galleryid = $formData['galleryid'];
            }elseif ( $gallery = $questionary->getGallery() )
            {
                $model->galleryid = $gallery->id;
            }
            // Проверка данных формы по AJAX
            $this->performAjaxValidation($model);
            
            if ( $model->validate() )
            {// все данные формы верны
                if ( $user = $model->save() )
                {/* @var $user User */
                    if ( ! Yii::app()->user->checkAccess('Admin') )
                    {// Вместе с сохранением данных участника сразу же происходит его авторизация на сайте
                        Yii::app()->getModule('user')->forceLogin($user);
                    }
                    // и перенаправляем его на страницу завершения
                    $finishUrl = Yii::app()->createUrl('//topmodel/finish');
                    $this->redirect($finishUrl);
                }
            }
        }elseif ( $model->scenario === 'registration' OR $questionary->isNewRecord )
        {// это регистрация и у пользователя нет аккаунта:
            // cоздаем пустую галерею чтобы было куда загружать изображения
            $gallery = new Gallery();
            // Определяем в каких размерах создавать миниатюры изображений в галерее
            $gallery->versions    = Yii::app()->getModule('questionary')->gallerySettings['versions'];
            $gallery->limit       = 40;
            $gallery->name        = 1;
            $gallery->description = 1;
            $gallery->save();
            
            if ( ! $gallery->subfolder )
            {// @todo beforeSave не может знать id для записи в subfolder до сохранения записи
                // поэтому загрузка изображений происходила в неправильные директории
                // этот код можно будет убрать после того как будет переписан класс gallery
                $gallery->subfolder = $gallery->id;
                $gallery->save();
            }
            $model->galleryid = $gallery->id;
        }
        // Отображаем страницу формы с регистрацией на роль
        $this->render('index', array('model' => $model));
    }

    /**
     * @return void
     */
    public function actionFinish()
    {
        $this->render('finish');
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * 
     * @param integer the ID of the model to be loaded
     * @return EventVacancy
     */
    public function loadModel($id)
    {
        $model = EventVacancy::model()->findByPk($id);
        if ( $model === null )
        {
            throw new CHttpException(404, 'Роль не найдена id=' . $id);
        }
        return $model;
    }
    
    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if ( isset($_POST['ajax']) && $_POST['ajax'] === 'dynamic-registration-form' )
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}