<?php

/**
 * Модуль "проекты и мероприятия"
 */
class ProjectsModule extends CWebModule
{
    /**
     * @var ProjectsController
     */
    public $defaultController = 'projects';
    
	public function init()
	{
		// import the module-level models and components
		$this->setImport(array(
			'projects.models.*',
		    'projects.controllers.*',
		    
		    'ext.galleryManager.*',
		    'ext.galleryManager.models.*',
		));
	}
	
	/**
	 * Получить условия для выбора проектов в списке
	 * @todo прописать критерий через SearchScopes
	 * @param array|SearchScope $scopes - критерий поиска анкет или несколько таких критериев поиска
	 *         (например если мы ищем по своим критериям внутри раздела каталога)
	 * @return CDbCriteria
	 */
	public function getProjectsCriteria($scopes=array())
	{
	    $criteria  = new CDbCriteria();
	    // Показываем в базе пользователей только проверенные анкеты
	    $criteria->compare('status', 'active');
	     
	    return $criteria;
	}

	/**
	 * @param $str - id строки перевода
	 * @param $params - дополнительные параметры для строки
	 * @param $dic - используемый словарь
	 * @return string - переведенная строка
	 */
	public static function t($str='', $params=array(), $dic='projects')
	{
	    if ( Yii::t("ProjectsModule", $str) == $str )
	    {
	        return Yii::t("ProjectsModule.".$dic, $str, $params);
	    }else
	    {
	        return Yii::t("ProjectsModule", $str, $params);
	    }
	}
	
	// Функции для работы с уведомлениями, создаваемыми модулем "проекты"
	// @todo придумать как можно вывести здесь виджеты ( попробовать Yii::app()->createController() )
	// @todo возможно следует добавить к модулю составления красивых писем собственный контроллер 
	//       (чтобы выводить виджеты и ни от кого не зависеть)
	
	/**
	 * Отправить участнику письмо о том, что он приглашен на съемки
	 * Эта функция является обработчиком события 'onNewInvite'
	 * 
	 * @param CModelEvent $event - отправленное приглашением (EventInvite) событие
	 * @return bool
	 */
	public static function sendNewInviteNotification($event)
	{
	    $invite = $event->sender;
	    if ( ! $invite->questionary OR ! is_object($invite->questionary->user) )
	    {// проверка на случай нарушения целостности БД
	        // @todo записать ошибку в лог
	        return false;
	    }
	    
	    $email   = $invite->questionary->user->email;
	    $subject = self::createNewInviteNotificationSubject($invite);
	    $message = self::createNewInviteNotificationMessage($invite);
	     
	    // добавляем письмо с приглашениями в очередь
	    return Yii::app()->getComponent('ecawsapi')->pushMail($email, $subject, $message);
	}
	
	/**
	 * Отправить участнику письмо о том, что его заявка была принята
	 * 
	 * @param CModelEvent $event - отправленное заявкой (ProjectMember) событие
	 * @return bool
	 */
	public static function sendApproveMemberNotification($event)
	{
	    $memberData = $event->sender;
	    if ( ! $memberData->member OR ! is_object($memberData->member->user) )
	    {// проверка на случай нарушения целостности БД
    	    // @todo записать ошибку в лог
    	    return false;
	    }
	    
	    $email   = $memberData->member->user->email;
	    $subject = 'Ваша заявка принята. Теперь вы участник проекта "'.$memberData->vacancy->event->project->name.'"';
	    $message = self::createApproveMemberMessage($memberData);
	    
	    // добавляем письмо в очередь
	    return Yii::app()->getComponent('ecawsapi')->pushMail($email, $subject, $message);
	}
	
	/**
	 * Отправить участнику письмо о том, что его заявка отклонена
	 *
	 * @param CModelEvent $event - отправленное заявкой (ProjectMember) событие
	 * @return bool
	 */
	public static function sendRejectMemberNotification($event)
	{
	    $memberData = $event->sender;
	    if ( ! $memberData->member OR ! is_object($memberData->member->user) )
	    {// проверка на случай нарушения целостности БД
    	    // @todo записать ошибку в лог
    	    return false;
	    }
	     
	    $email = $memberData->member->user->email;
	    $subject = 'Ваша заявка на участие в проекте "'.$memberData->vacancy->event->project->name.'" отклонена.';
	    $message = self::createRejectMemberMessage($memberData);
	    
	    // добавляем письмо в очередь
	    return Yii::app()->getComponent('ecawsapi')->pushMail($email, $subject, $message);
	}
	
	/**
	 * Составить тему письма для приглашения на съемки
	 * 
	 * @param EventInvite $invite - приглашение на мероприятие
	 * @return string
	 * 
	 * @todo проверить Chtml::encode()
	 */
	protected static function createNewInviteNotificationSubject($invite)
	{
	    // название проекта и мероприятия
	    $projectName = $invite->event->project->name;
	    $eventName   = $invite->event->name;
	    
	    // дата и время начала мероприятия
	    $dateFormatter = Yii::app()->getDateFormatter();
	    $startDate = $invite->event->getFormattedTimeStart();
	    
	    // дата и время или название мероприятия (если время не указано)
	    $eventInfo = $eventName;
	    if ( $startDate )
	    {
	        $eventInfo = $startDate;
	    }
	    
	    $subject = 'Приглашение на съемки в проекте "'.$projectName.'" ('.$eventInfo.')';
	    
	    return $subject;
	}
	
	/**
	 * Получить тект письма с приглашением на съемки
	 * @param EventInvite $invite - приглашение на мероприятие
	 * @return string
	 * 
	 * @todo заявка в 1 клик
	 */
	protected static function createNewInviteNotificationMessage($invite)
	{
	    $message = '';
	    // получаем данные для письма
	    $userName    = $invite->questionary->firstname;
	    $projectName = $invite->event->project->name;
	    
	    // получаем url для всех ссылок
	    $invitesUrl = Yii::app()->createAbsoluteUrl(Yii::app()->getModule('questionary')->profileUrl,
	        array(
	            'id'        => $invite->questionary->id,
	            'activeTab' => 'invites',)
            );
	    $projectUrl = Yii::app()->createAbsoluteUrl('/projects/projects/view', 
	        array('id' => $invite->event->project->id));
	    /*$vacanciesUrl = Yii::app()->createAbsoluteUrl('/projects/projects/view',
	        array(
	            'eventid'   => $invite->event->id,
	            'activeTab' => 'vacancies',)
	    );*/
	    $subscribeUrl = Yii::app()->createAbsoluteUrl('/projects/invite/subscribe', 
	        array('id' => $invite->id, 'key' => $invite->subscribekey));
	    
	    // создаем ссылки на все объекты
	    $invitesLink = CHtml::link('Мои приглашения', $invitesUrl, array('target' => '_blank'));
	    $projectLink = CHtml::link($projectName, $projectUrl, array('target' => '_blank'));
	    //$vacanciesLink = CHtml::link($vacanciesUrl, $vacanciesUrl, array('target' => '_blank'));
	    $subscribeLink = CHtml::link($subscribeUrl, $subscribeUrl, array('target' => '_blank'));

	    // Создаем текст сообщения
	    if ( $userName )
	    {
	        $message .= $userName.", добрый день.<br>\n<br>\n";
	    }else
	    {
	        $message .= "Добрый день.<br>\n<br>\n";
	    }
	    
	    $message .= 'Приглашаем вас принять участие в проекте "'.$projectLink.'"'."<br>\n";
	    if ( trim($invite->event->project->description) )
	    {// описание проекта для участника
    	    $message .= '<h3>О проекте</h3>';
    	    $message .= '<p>'.$invite->event->project->description."</p><br>\n";
	    }
	    
	    // информация о мероприятии
	    if ( $invite->event->type == 'group' )
	    {// группа мероприятий - выведем информацию о каждом
	        $message .= self::createGroupEventDescription($invite->event);
	    }else
	    {// одно мероприятие
	        $message .= self::createSingleEventDescription($invite->event);
	    }
	    
	    // Ссылка на подачу заявки
	    $message .= 'Если хотите принять участие - то для этого достаточно подать заявку, просто ';
	    $message .= 'кликнув по этой ссылке: '.$subscribeLink."<br>\n<br>\n";
	    
	    $message .= 'Просмотреть, другие приглашения вы можете 
	        на своей странице, в разделе "'.$invitesLink.'".'."<br>\n";
	    
	    // стандартная подпись
	    $message .= self::createGoodbyeText();
	    $message .= self::createMailSignature();
	    
	    return $message;
	}
	
	protected static function createSingleEventDescription($event)
	{
	    $message = 'Вот информация о запланированном событии:'."<br>\n";
	    
	    $eventName = $event->name;
	    $startDate = $invite->event->getFormattedTimeStart();
	    $endDate   = Yii::app()->getDateFormatter()->format('HH:mm', $invite->event->timeend);
	    
	    $eventUrl  = Yii::app()->createAbsoluteUrl('/projects/projects/view', array('eventid' => $event->id));
	    $eventLink = CHtml::link($eventName, $eventUrl, array('target' => '_blank'));
	    
	    $message .= '<p>Что планируется: '.$eventLink.'</p>';
	    $message .= 'Когда: <b>'.$startDate." - ".$endDate."</b><br>\n<br>\n";
	    
	    if ( trim($invite->event->description) )
	    {// описание мероприятия
	        $message .= '<p>Подробности: '.$event->description."</p><br>\n";
	    }
	    return $message;
	}
	
	protected static function createGroupEventDescription($group)
	{
	    $message = 'Мероприятия будут проходить в следующие дни:'."<br>\n";
	    
	    foreach ( $group->events as $event )
	    {
	        $message .= self::createOneGroupEventDescription($event);
	    }
	    
	    return $message;
	}
	
	protected static function createOneGroupEventDescription($event)
	{
	    $message = '';
	    
	    $eventName = $event->name;
	    $startDate = $event->getFormattedTimeStart();
	    $endDate   = Yii::app()->getDateFormatter()->format('HH:mm', $event->timeend);
	    $eventUrl  = Yii::app()->createAbsoluteUrl('/projects/projects/view', array('eventid' => $event->id));
	    $eventLink = CHtml::link($eventName, $eventUrl, array('target' => '_blank'));
	    
	    $message .= '<h2>'.$startDate.' - '.$endDate.'</h2>';
	    $message .= '<h3>'.$eventName.'</h3>';
	    
	    $message .= '<p>Дополнительная информация: '.$event->description.'</p>';
	    
	    return $message;
	}
	
	/**
	 * Получить текст письма с уведомлением о том, что заявка участника принята
	 * @param ProjectMember $projectMember - данные заявки участника
	 * @return string
	 * 
	 * @todo добавить обработку поля "дополнительная информация для участников"
	 */
	public static function createApproveMemberMessage($projectMember)
	{
	    $message = '';
	    
	    $projectName = $projectMember->vacancy->event->project->name;
	    $event       = $projectMember->vacancy->event;
	    $userName    = $projectMember->member->firstname;
	     
	    if ( $userName )
	    {
	        $message .= $userName.", здравствуйте.<br>\n<br>\n";
	    }else
	    {
	        $message .= "Добрый день.<br>\n<br>\n";
	    }
	    
	    $message .= 'Ваша заявка рассмотрена и подтверждена.'."<br>\n";
	    $message .= 'Теперь вы участник проекта "'.$projectName.'".'."<br>\n";
	    
	    if ( $event->type == 'group' )
	    {
	        $message .= self::createGroupEventDescription($event);
	    }else
	    {
	        $message .= 'Напоминаем вам информацию о мероприятии:'."<br>\n";
	        $message .= self::createSingleEventDescription($event);
	    }
	    
	    $message .= 'Ждем вас.'."<br>\n<br>\n";
	    
	    //$message .= 'Если случится что-то непредвиденное и вы не сможете присутствовать, то пожалуйста сообщите нам об этом.';
	    
	    // стандартная подпись
	    $message .= self::createGoodbyeText();
	    $message .= self::createMailSignature();
	    
	    return $message;
	}
	
	/**
	 * Получить текст письма с уведомлением о том, что заявка участника отклонена
	 * @param ProjectMember $projectMember - данные заявки участника
	 * @return string
	 * 
	 * @todo указывать причины отказа
	 */
	public static function createRejectMemberMessage($projectMember)
	{
	    $message = '';
	    
	    $projectName = $projectMember->vacancy->event->project->name;
	    $eventName   = $projectMember->vacancy->event->name;
	    $userName    = $projectMember->member->firstname;
	    
	    if ( $userName )
	    {
	        $message .= $userName.", здравствуйте.<br>\n<br>\n";
	    }else
	    {
	        $message .= "Добрый день.<br>\n<br>\n";
	    }
	    
	    $message .= 'Ваша заявка на участие в проекте "'.$projectName.'" отклонена.'."<br>\n";
	    $message .= 'Необходимое количество человек для мероприятия "'.$eventName.'" уже набрано.'."<br>\n<br>\n";
	    
	    //$message .= 'Если у нас появятся другие предложения, то мы обязательно сообщим вам о них.';
	    
	    $message .= self::createGoodbyeText();
	    $message .= self::createMailSignature();
	    
	    return $message;
	}
	
	protected static function createGoodbyeText()
	{
	    return "<br>\n<br>\n"."С уважением, команда проекта EasyCast.";
	}
	
	protected static function createMailSignature()
	{
	    $passwordUrl = Yii::app()->createAbsoluteUrl('/user/recovery');
	    
	    $message = '<hr>';
	    $message .= '<small>Если у вас есть вопросы, то вы можете задать их нам, просто ответив на это письмо
	                или позвонив по телефону '.Yii::app()->params['adminPhone'].".<br>\n";
	    $message .= 'Если вы забыли пароль, или он не пришел вам при регистрации - его можно восстановить на этой странице: ';
	    $message .= CHtml::link($passwordUrl, $passwordUrl, array('target' => '_blank'));
	    $message .= '</small>';
	    return $message;
	}
}