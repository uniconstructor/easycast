<?php

/**
 * Модуль "проекты и мероприятия"
 * 
 * @todo убрать отсюда функции отправки уведомлений и переместить их в отдельный модуль "уведомления"
 */
class ProjectsModule extends CWebModule
{
    /**
     * @var ProjectsController
     */
    public $defaultController = 'projects';
    
    /**
     * @see CModule::init()
     */
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
	 * 
	 * @deprecated заменить использование этой функции использованием именованных групп условий поиска
	 */
	public function getProjectsCriteria($scopes=array())
	{
	    $criteria  = new CDbCriteria();
	    // Показываем в списке проектов активные и завершенные проекты
	    $criteria->addInCondition('status', array(Project::STATUS_ACTIVE, Project::STATUS_FINISHED));
	    
	    if ( Yii::app()->getModule('user')->getViewMode() === 'customer' OR 
	         Yii::app()->user->checkAccess('Admin') )
	    {// Просмотр для заказчиков: cамые лучшие по рейтингу - всегда наверху
	        $criteria->order = '`rating` DESC, `timecreated` DESC';
	    }else
	    {// Просмотр для участников: самые новые проекты - всегда наверху
	        $criteria->order = '`timecreated` DESC';
	    }
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
	
	/**
	 * Отправить участнику письмо о том, что он приглашен на съемки
	 * Эта функция является обработчиком события 'onNewInvite'
	 * 
	 * @param  CModelEvent $event - отправленное приглашением (EventInvite) событие
	 * @return bool
	 */
	public static function sendNewInviteNotification($event)
	{
	    /* @var $invite EventInvite */
	    $invite = $event->sender;
	    if ( ! $invite->questionary OR ! is_object($invite->questionary->user) )
	    {// проверка на случай нарушения целостности БД
	        // @todo записать ошибку в лог
	        return false;
	    }
	    if ( $invite->event->timestart <= time() AND ! $invite->event->nodates )
	    {// если событие уже прошло и мероприятие имеет конкретную дату - то не отсылаем на него приглашение
	        // чтобы не было драмы и вопросов в стиле "почему вы приглашаете меня на уже прошедшее мероприятие?"
	        return true;
	    }
	    // составляем текст письма с приглашением
	    $mailComposer = Yii::app()->getModule('mailComposer');
	    $email   = $invite->questionary->user->email;
	    $subject = $mailComposer->getSubject('newInvite', array('invite' => $invite));
	    $message = $mailComposer->getMessage('newInvite', array('invite' => $invite));
	     
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
    	    return false;
	    }
	    $mailComposer = Yii::app()->getModule('mailComposer');
	    
	    $email   = $memberData->member->user->email;
	    $subject = 'Ваша заявка одобрена. Теперь вы участник проекта "'.$memberData->vacancy->event->project->name.'"';
	    $message = $mailComposer->getMessage('approveMember', array('projectMember' => $memberData));
	    
	    // добавляем письмо в очередь
	    return Yii::app()->getComponent('ecawsapi')->pushMail($email, $subject, $message);
	}
	
	/**
	 * Отправить участнику письмо о том, что его заявка отклонена
	 *
	 * @param CModelEvent $event - отправленное заявкой (ProjectMember) событие
	 * @return bool
	 * 
	 * @todo не отправлять письмо, если у участника уже есть подтвержденные заявки на это мероприятие
	 * @todo временно отключено
	 */
	public static function sendRejectMemberNotification($event)
	{
	    $memberData = $event->sender;
	    if ( ! $memberData->member OR ! is_object($memberData->member->user) )
	    {// проверка на случай нарушения целостности БД
    	    // @todo записать ошибку в лог
    	    return false;
	    }
	    $mailComposer = Yii::app()->getModule('mailComposer');
	     
	    $email   = $memberData->member->user->email;
	    $subject = 'Ваша заявка на участие в проекте "'.$memberData->vacancy->event->project->name.'" отклонена.';
	    $message = $mailComposer->getMessage('rejectMember', array('projectMember' => $memberData));
	    
	    // добавляем письмо в очередь
	    return Yii::app()->getComponent('ecawsapi')->pushMail($email, $subject, $message);
	}
	
	/**
	 * Отправить письмо участнику о том, что его заявка предварительно одобрена
	 * @param CModelEvent $event - отправленное заявкой (ProjectMember) событие
	 * @return bool
	 */
	public static function sendPendingMemberNotification($event)
	{
	    $memberData = $event->sender;
	    if ( ! $memberData->member OR ! is_object($memberData->member->user) )
	    {// проверка на случай нарушения целостности БД
    	    // @todo записать ошибку в лог
    	    return false;
	    }
	    $mailComposer = Yii::app()->getModule('mailComposer');
	    
	    $email   = $memberData->member->user->email;
	    //$subject = 'Ваша заявка в проекте "'.$memberData->vacancy->event->project->name.'" предварительно одобрена.';
	    $subject = 'Ваша заявка предварительно одобрена';
	    $message = $mailComposer->getMessage('pendingMember', array('projectMember' => $memberData));
	     
	    // добавляем письмо в очередь
	    return Yii::app()->getComponent('ecawsapi')->pushMail($email, $subject, $message);
	}
}