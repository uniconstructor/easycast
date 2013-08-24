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
     * (non-PHPdoc)
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
	 */
	public function getProjectsCriteria($scopes=array())
	{
	    $criteria  = new CDbCriteria();
	    // Показываем в списке проектов активные и завершенные проекты. Самые новые всегда наверху.
	    $criteria->addInCondition('status', array(Project::STATUS_ACTIVE, Project::STATUS_FINISHED));
	    $criteria->order = '`timecreated` DESC';
	     
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
	    $subject = 'Ваша заявка принята. Теперь вы участник проекта "'.$memberData->vacancy->event->project->name.'"';
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
	    $message = $mailComposer->getMessage('rejectMember', array('projectMember' => $memberData));
	    
	    // добавляем письмо в очередь
	    return Yii::app()->getComponent('ecawsapi')->pushMail($email, $subject, $message);
	}
}