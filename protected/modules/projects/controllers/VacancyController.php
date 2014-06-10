<?php

/**
 * Класс для совершения операций с вакансией
 * Этот контроллер обрабатывает только AJAX-запросы, информация по вакансиям обычно выводится виджетами
 * @todo настроить права доступа
 */
class VacancyController extends Controller
{
    /**
     * Подать заявку на участие в мероприятии (указав определенную вакансию)
     * Подача заявки происхочить через AJAX-запрос, методом POST
     *
     * @todo языковые строки
     * @todo более подробная проверка прав
     */
    public function actionAddApplication()
    {
        if ( ! Yii::app()->request->isAjaxRequest OR ! Yii::app()->request->isPostRequest )
        {// разрешаем только POST AJAX-запросы
            throw new CHttpException(400, 'Only AJAX request allowed');
        }
        if ( Yii::app()->user->isGuest )
        {// проверяем права
            throw new CHttpException(400, 'Operation not permitted');
        }
        // получаем вакансию
        $vacancyId = Yii::app()->request->getParam('vacancyId', 0);
        $vacancy   = $this->loadModel($vacancyId);
        
        // для админов (подача заявки от имени участника) - получаем id анкеты участника, 
        // от имени которого подается заявка
        $questionaryId = Yii::app()->request->getParam('questionaryId', 0);
        
    
        // Создаем и сохраняем новый запрос на участие
        if ( $this->createApplication($vacancy, $questionaryId) )
        {
            echo 'OK';
        }else
        {
            echo 'ERROR';
        }
    
        Yii::app()->end();
    }
    
    /**
     * Подать заявку на вакансию по токену
     *
     * @return null
     * @todo сделать проверку статуса вакансии и статуса заявки. Если они устарели - выводить сообщение
     * @todo выводить сообщение, если участник не подходит по критериям вакансии
     */
    public function actionAddApplicationByToken()
    {
        if ( ! Yii::app()->request->isAjaxRequest OR ! Yii::app()->request->isPostRequest )
        {// разрешаем только POST AJAX-запросы
            throw new CHttpException(400, 'Only AJAX request allowed');
        }
        if ( ! $key = Yii::app()->request->getPost('key', null) )
        {// не передан токен
            throw new CHttpException(500, 'Token not found');
        }
        $vacancyId = Yii::app()->request->getPost('vacancyId', null);
        $vacancy   = $this->loadModel($vacancyId);
        
        $inviteId = Yii::app()->request->getParam('inviteId', null);
        if ( ! $invite = EventInvite::model()->findByPk($inviteId) )
        {
            throw new CHttpException(404, 'Приглашение не найдено');
        }
        
        if ( $key != $invite->subscribekey )
        {// ключ одноразовой ссылки не подходит - что-то тут не так...
            throw new CHttpException(404, 'Страница не найдена');
        }
        
        // Создаем и сохраняем новый запрос на участие
        if ( $this->createApplication($vacancy, $invite->questionaryid ) )
        {
            echo 'OR';
        }else
        {
            echo 'ERROR';
        }
        Yii::app()->end();
    }
    
    /**
     * Отобразить страницу с динамической формой анкеты, которая 
     * одновременно регистрирует пользователя в системе и подает от его имени заявку на интересующую роль
     * 
     * @return void
     * @todo вынести в отдельный action-класс
     */
    public function actionRegistration()
    {
        // id роли
        // @todo добавить проверку статуса роли
        $vid     = Yii::app()->request->getParam('vid', 0);
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
        
        // id анкеты: разрешаем только регистрацию новых пользователей или подачу заявки от своего имени
        // подача заявки от имени другого участника доступна только в админке
        if ( $qid )
        {// заявку подает существующий участник 
            $questionary = Questionary::model()->findByPk($qid);
            $model = new QDynamicFormModel('application');
            $model->displayFilled = false;
        }else
        {// участник регистрируется через подачу заявки
            $questionary = new Questionary;
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
                    if ( $model->scenario === 'registration' AND ! Yii::app()->user->checkAccess('Admin') )
                    {// Вместе с сохранением данных участника сразу же происходит его авторизация на сайте
                        Yii::app()->getModule('user')->forceLogin($user);
                        // добавляем flash-сообщение об успешной регистрации
                        Yii::app()->user->setFlash('success', 'Регистрация завершена');
                    }
                    // и перенаправляем его на страницу
                    $url = Yii::app()->createUrl('//questionary/questionary/view'); 
                    $this->redirect($url);
                }
            }
        }elseif ( $model->scenario === 'registration' )
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
            }
            $model->galleryid = $gallery->id;
        }
        
        // Отображаем страницу формы с регистрацией на роль
        $this->render('registration', array(
            'model' => $model,
        ));
    }
    
    /**
     * Создать заявку на участии в мероприятии (выполняется после всех проверок)
     *
     * @param EventVacancy $vacancy
     * @return bool
     *
     * @todo сделать проверку - не является ли текущий участник гостем, если questionaryid не указан
     */
    protected function createApplication($vacancy, $questionaryId=null)
    {
        if ( ! $questionaryId OR ! Yii::app()->user->checkAccess('Admin') )
        {// подавать заявку от имени другого участника могут только админы
            $questionaryId = Yii::app()->getModule('user')->user()->questionary->id;
        }
        if ( ! $vacancy->isAvailableForUser($questionaryId) )
        {// участник не подходит по критериям вакансии
            return false;
        }
        
        $request = new MemberRequest();
        $request->vacancyid = $vacancy->id;
        $request->memberid  = $questionaryId;
        return $request->save();
    }
    
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     * @return EventVacancy
     */
    public function loadModel($id)
    {
        $model = EventVacancy::model()->findByPk($id);
        if ( $model === null )
        {
            throw new CHttpException(404, 'Роль не найдена id='.$id);
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