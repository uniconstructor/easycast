<?php

/**
 * Класс для совершения операций с вакансией
 * Этот контроллер обрабатывает только AJAX-запросы, информация по вакансиям обычно выводится виджетами
 * @todo настроить права доступа
 */
class VacancyController extends Controller
{
    /**
     * @var string - класс модели, по умолчанию используемый для метода $this->loadModel()
     */
    protected $defaultModelClass = 'EventVacancy';
    
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
    }
    
    /**
     * Подать заявку на вакансию по токену
     * 
     * @return null
     * 
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
        // получаем id приглашения и роли
        $inviteId  = Yii::app()->request->getParam('inviteId', null);
        $vacancyId = Yii::app()->request->getParam('vacancyId', null);
        $invite    = EventInvite::model()->findByPk($inviteId);
        $vacancy   = $this->loadModel($vacancyId);
        
        if ( ! $invite )
        {// для переданного токена нет приглашения
            throw new CHttpException(404, 'Приглашение не найдено');
        }
        if ( $key != $invite->subscribekey )
        {// переданный ключ одноразовой ссылки не подходит с хранимым в базе
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
    }
    
    /**
     * Отобразить страницу с динамической формой анкеты, которая 
     * одновременно регистрирует пользователя в системе и подает от его имени заявку на интересующую роль
     * 
     * @return void
     * 
     * @todo вынести в отдельный action-класс
     * @todo обработать специальными страницами случаи когда при регистрации возникла ошибка
     */
    public function actionRegistration()
    {
        // id роли на которую подается заявка
        $vid     = Yii::app()->request->getParam('vid', 0);
        $vacancy = $this->loadModel($vid);
        // если отбор на роль или мероприятие завершен - сообщим об этом
        if ( $vacancy->status === EventVacancy::STATUS_FINISHED OR $vacancy->event->isExpired() )
        {// отбор завершен - сообщим об этом
            if ( ! Yii::app()->user->checkAccess('Admin') )
            {
                $this->render('expired');
                return;
            }
        }
        // id анкеты: разрешаем только регистрацию новых пользователей или подачу заявки от своего имени
        $qid = Yii::app()->getModule('questionary')->getCurrentQuestionaryId();
        if ( Yii::app()->user->checkAccess('Admin') )
        {// для админов: они сами не могут подать заявку, но могут регистрировать новых
            // пользователей через эту форму
            $qid = 0;
        }
        if ( $questionary = Questionary::model()->findByPk($qid) )
        {// заявку подает существующий участник:
            $statuses = array(
                ProjectMember::STATUS_DRAFT,
                ProjectMember::STATUS_ACTIVE,
                ProjectMember::STATUS_FINISHED,
                ProjectMember::STATUS_SUCCEED,
                ProjectMember::STATUS_REJECTED,
                ProjectMember::STATUS_FINALIZED,
                ProjectMember::STATUS_INCOMPLETE,
            );
            
            if ( $vacancy->hasMember($qid, $statuses) )
            {// происходит дополение существующих данных
                $scenario = 'finalization';
            }else
            {// подача заявки от зарегистрированного участника
                $scenario = 'application';
            }
            $model = new QDynamicFormModel($scenario);
            // не показываем те поля анкеты которые уже заполнены участником
            // при регистрации или редактировании профиля, а также скрываем те дополнительные поля
            // которые уже были заполнены участником при подаче заявки на другие роли
            // @todo сделать настройку если обязательное или доп. поле уже заполнено участником:
            //       - предложить изменить последнее значение
            //       - использовать последнее заполненное значение молча, не давая его изменить
            //       - всегда требовать новый ответ
            $model->displayFilled = false;
            // id галереи
            $model->galleryid = $questionary->galleryid;
        }else
        {// участник регистрируется через подачу заявки
            $questionary = new Questionary;
            $model       = new QDynamicFormModel('registration');
            // id галереи
            $model->galleryid = $questionary->galleryid;
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
            }else
            {
                $model->galleryid = Yii::app()->session->get('galleryid');
            }
            // сохраняем в сессию тот id галереи, который смогли вытащить
            Yii::app()->session->add('galleryid', $model->galleryid);
            // проверка данных формы по AJAX
            $this->performAjaxValidation($model);
            
            if ( $model->validate() )
            {// все данные формы верны
                if ( $user = $model->save() )
                {/* @var $user User */
                    // сообщаем что заявка подана
                    $this->finishRegistration($user, $vacancy, 'registration');
                    return;
                }
            }
        }elseif ( $model->scenario === 'registration' )
        {// это регистрация и у пользователя нет аккаунта:
            if ( $galleryId = Yii::app()->session->get('galleryid') )
            {// берем id галереи из сессии если она уже была сохранена
                $gallery = Gallery::model()->findByPk($galleryId);
            }else
            {// cоздаем пустую галерею чтобы было куда загружать изображения
                $gallery = new Gallery();
                // Определяем в каких размерах создавать миниатюры изображений в галерее
                $gallery->versions    = Yii::app()->getModule('questionary')->gallerySettings['versions'];
                $gallery->limit       = 40;
                $gallery->name        = 1;
                $gallery->description = 1;
                
                if ( ! $gallery->save() )
                {// не удалось сохранить галерею
                    Yii::log('Не удалось сохранить галерею', CLogger::LEVEL_ERROR);
                }
            }
            if ( ! $gallery->subfolder )
            {// @todo beforeSave не может знать id для записи в subfolder до сохранения записи
                // поэтому загрузка изображений происходила в неправильные директории
                // этот код можно будет убрать после того как будет переписан класс gallery
                $gallery->subfolder = $gallery->id;
            }
            // привязываем заполненную галерею к анкете
            $model->galleryid = $gallery->id;
            // сохраняем id галереи в сессию
            Yii::app()->session->add('galleryid', $gallery->id);
        }elseif ( ! $formData AND $model->hasFullInfo() AND $model->scenario === 'application' AND 
                    Yii::app()->request->getPost('alreadyFilled') )
        {// если подается заявка от участника который уже заранее заполнил все необходимые данные:
            // в этом случае ничего нового из формы не приходит (и это логично)
            // поэтому просто регистрируем заявку и отпускаем пользователя с миром
            if ( $vacancy->addApplication($questionary->id) )
            {// сообщаем что заявка подана
                $this->finishRegistration($questionary->user, $vacancy, 'registration');
                return;
            }
        }
        // FIXME добавить возможность указывать любую разметку (landing) для любой роли
        if ( $vacancy->id == 749 )
        {
            $this->layout = '//landing/masterchief';
        }
        // отображаем форму с регистрацией на роль
        $this->render('registration', array(
            'model' => $model,
        ));
    }
    
    /**
     * AJAX-проверка одного шага анкеты
     * @return void
     */
    public function actionValidateStep()
    {
        /*if ( ! Yii::app()->request->isPostRequest OR ! Yii::app()->request->isAjaxRequest )
        {// проверка данных только через POST AJAX
            Yii::app()->end();
        }*/
        if ( ! $index = Yii::app()->request->getParam('_index', 1) )
        {// получаем текущий шаг регистрации
            $index = 1;
        }
        // собираем входные параметры
        $steps    = Yii::app()->request->getParam('steps');
        $formData = Yii::app()->request->getParam('QDynamicFormModel');
        $qid      = Yii::app()->request->getParam('qid');
        $vid      = Yii::app()->request->getParam('vid');
        // определяем на какую роль подается заявка
        $vacancy = $this->loadModel($vid);
        
        // определяем кто подает заявку и будет ли регистрация
        if ( ! $questionary = Yii::app()->getModule('questionary')->getCurrentQuestionary() )
        {// анкета еще не создана - происходит регистрация с одновременной подачей заявки
            $scenario    = 'registration';
            $questionary = new Questionary();
        }else
        {// зарегистрированный участник подает заявку: 
            $scenario = 'application';
            $formData['galleryid'] = $questionary->galleryid;
        }
        
        // на каждом шаге регистрации проверяется определенный набор полей (только часть а не вся форма)
        // определяем на каком этапе мы находимся и какие поля нужно проверять
        $fields = array();
        $stepId = $steps[$index];
        $step   = WizardStepInstance::model()->findByPk($stepId);
        // список полей из анкеты участника
        $userFields  = QUserField::model()->forObject('wizardstepinstance', $step->id)->findAll();
        foreach ( $userFields as $userField )
        {
            $fields[] = $userField->name;
        }
        // список полей заявки
        $extraFields = ExtraField::model()->forObject('wizardstepinstance', $step->id)->findAll();
        foreach ( $extraFields as $extraField )
        {
            $fields[] = 'ext_'.$extraField->name;
        }
        
        // создаем модель формы и загружаем туда данные для проверки
        $model = new QDynamicFormModel($scenario);
        $model->questionary = $questionary;
        $model->vacancy     = $vacancy;
        if ( isset($formData['galleryid']) AND $formData['galleryid'] )
        {// галерея передана при сохранении формы 
            $model->galleryid = $formData['galleryid'];
            Yii::app()->session->add('galleryid', $formData['galleryid']);
        }else
        {// галерея не передана - но мы помним ее в сессии
            $model->galleryid = Yii::app()->session->get('galleryid');
        }
        //CVarDumper::dump($formData['galleryid']);
        //CVarDumper::dump($model->galleryid);
        
        // используем страндартные проверки модели формы для проверки всех полей анкеты
        $result = CActiveForm::validate($model, $fields);
        
        //$result = CActiveForm::validate($model, $fields);
        /*$result = CJSON::decode($result);
        $result['QDynamicFormModel_email'] = array('545454');
        $result = CJSON::encode($result);*/
        
        // отправляем результат проверки как JSON
        echo $result;
        Yii::app()->end();
    }
    
    /**
     * Все действия, необходимые после завершения регистрации: сообщение о том что заявка принята,
     * отображение дополнительной информации и перенаправление участника на страницу с его заявками
     * 
     * @param User $user
     * @param EventVacancy $vacancy
     * @param string $scenario сценарий использования формы: регистрация или просто подача заявки
     * @return void
     */
    protected function finishRegistration($user, $vacancy, $scenario)
    {
        if ( $scenario === 'registration' AND ! Yii::app()->user->checkAccess('Admin') )
        {// Вместе с сохранением данных участника сразу же происходит его авторизация на сайте
            Yii::app()->getModule('user')->forceLogin($user);
            // добавляем flash-сообщение об успешной регистрации
            Yii::app()->user->setFlash('success', 'Регистрация завершена');
            
            // FIXME сделать загрузку видео в зависимости от настроек
            if ( $vacancy->id == 749 OR Yii::app()->user->checkAccess('Admin') )
            {
                $galleryId = $user->questionary->galleryid;
                // @todo проверить можно ли тут использовать просто find
                // @todo после подачи заявки прикреплять видео к заявке а не к анкете
                $videos = Video::model()->forObject('gallery', $galleryId)->findAll();
                if ( $galleryId AND $videos )
                {
                    $video = current($videos);
                    $video->objecttype = 'questionary';
                    $video->objectid   = $user->questionary->id;
                    $video->save();
                }
            }
        }elseif ( $scenario === 'finalization' )
        {// сообщаем что данные заявки дополнены
            Yii::app()->user->setFlash('success', 'Данные в вашей заявке дополнены');
        }else
        {// сообщаем что заявка подана
            Yii::app()->user->setFlash('success', 'Ваша заявка зарегистрирована<br>
                Обо всех изменениях мы будем сообщать вам по почте');
            // FIXME сделать загрузку видео в зависимости от настроек
            // @todo после подачи заявки прикреплять видео к заявке а не к анкете
            if ( $vacancy->id == 749 OR Yii::app()->user->checkAccess('Admin') )
            {
                
            }
        }
        // адрес по которому происходит переход после "thank you page"
        $finalRedirect = Yii::app()->createUrl('//questionary/questionary/view', array(
            'id'        => $user->questionary->id,
            'activeTab' => 'requests',
        ));
        // FIXME адрес редиректа настройкой
        if ( $vacancy->id == 749 )
        {
            Yii::app()->user->setFlash('success', 'Заявка принята. Вы можете перейти обратно на сайт проекта.');
            $finalRedirect = 'http://ctc.ru/rus/projects/show/76527/';
        }
        // отображаем страницу с итоговым результатом регистрации
        $this->render('finish', array(
            'redirectUrl' => $finalRedirect,
            'user'        => $user,
            'vacancy'     => $vacancy,
        ));
        // перенаправляем участника на страницу анкеты с открытой вкладкой заявок
        //$url = Yii::app()->createUrl('//projects/vacancy/finish');
        //$this->redirect($url);
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
        // создаем и сохраняем заявку
        return  (bool)$vacancy->addApplication($questionaryId);
    }
    
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     * @return EventVacancy
     */
    /*public function loadModel($id)
    {
        $model = EventVacancy::model()->findByPk($id);
        if ( $model === null )
        {
            throw new CHttpException(404, 'Роль не найдена id='.$id);
        }
        return $model;
    }*/
    
    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if ( isset($_POST['ajax']) AND $_POST['ajax'] === 'dynamic-registration-form' )
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}