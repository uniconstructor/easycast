<?php 

/**
 * Класс определяющий правила и пути смены статусов для проекта
 * @todo решить, должен ли быть finished конечным статусом
 */
class swProject
{
	const WORKFLOW_ID = 'swProject';
	/**
	 * @var string - статус проекта: черновик. Проект только что создан. Необходимая инофрмация еще либо не внесена
	 *               либо вносится в данный момент. Проект в этом статусе можно удалить.
	 */
	const DRAFT       = 'swProject/draft';
	/**
	 * @var string - статус проекта: готов к запуску. Есть логотип, есть описание проекта.
	 *               Все мероприятия и роли созданы, описаны ир настроены.
	 */
	const READY       = 'swProject/ready';
	/**
	 * @var string - статус проекта: активен. Проект опубликован, идет набор людей или съемки.
	 */
	const ACTIVE      = 'swProject/active';
	/**
	 * @var string - статус проекта: приостановлен. Активных мероприятий в проекте нет, но 
	 *               сам проект еще не завершен и может быть снова запущен в будущем
	 */
	const SUSPENDED   = 'swProject/suspended';
	/**
	 * @var string - статус проекта: завершен.
	 */
	const FINISHED    = 'swProject/finished';
    
	/**
	 * @return array
	 */
	public function getDefinition()
	{
		return array(
			'initial' => self::DRAFT,
			'node'    => array(
				array(
					'id'         => self::DRAFT,
					'label'      => 'Черновик',
					'constraint' => '',
					'transition' => array(
					    self::READY,
						self::ACTIVE => array($this, 'activationTask'),
					),
				),
				array(
					'id'         => self::READY,
					'label'      => 'Готов к запуску',
					'constraint' => '$this->isReady();',
					'transition' => array(
					    self::ACTIVE => array($this, 'activationTask'),
					),
				),
			    array(
			        'id'         => self::ACTIVE,
			        'label'      => 'Идет',
			        'constraint' => '$this->canActive();',
			        'transition' => array(
			            self::SUSPENDED,
			            self::FINISHED,
			        ),
			    ),
			    array(
			        'id'         => self::SUSPENDED,
			        'label'      => 'Приостановлен',
			        'constraint' => '$this->canSuspend();',
			        'transition' => array(
			            self::READY,
			            self::ACTIVE,
			            self::FINISHED,
			        ),
			    ),
			    array(
			        'id'         => self::FINISHED,
			        'label'      => 'Завершен',
			        'constraint' => '$this->canFinish();',
			        'transition' => array(),
			    ),
			)
		);
	}
	
	/**
	 * Действия, выполняемые при запуске проекта
	 * 
	 * @param Project $model
	 * @param string $srcStatus
	 * @param string $destStatus
	 * @return bool
	 */
	public function toActive($model, $srcStatus, $destStatus)
	{
	    // сначала активируем все группы событий
	    $groups = ProjectEvent::model()->forProject($model->id)->
            withStatus(ProjectEvent::STATUS_DRAFT)->groupsOnly()->findAll();
	    foreach ( $groups as $group )
	    {
    	    $group->setStatus('active');
	    }
	    
	    // активируем только те мероприятия на которые уже созданы роли
	    $filledEvents = ProjectEvent::model()->forProject($model->id)->
            withStatus(ProjectEvent::STATUS_DRAFT)->withVacancies()->findAll();
	    foreach ( $filledEvents as $event )
	    {// или те которые находятся в группе
	       $event->setStatus('active');
	    }
	    
	    // затем все отдельные мероприятия проекта
	    $groupEvents = ProjectEvent::model()->forProject($model->id)->
            withStatus(ProjectEvent::STATUS_DRAFT)->hasGroup()->findAll();
	    foreach ( $groupEvents as $event )
	    {// или те которые находятся в группе
	        $event->setStatus('active');
	    }
	    
	    return true;
	}
	
	/**
	 * Действия, выполняемые при завершении проекта
	 *
	 * @param Project $model
	 * @param string $srcStatus
	 * @param string $destStatus
	 * @return bool
	 */
	public function toFinished($model, $srcStatus, $destStatus)
	{
	    // сначала завершаем все активные группы событий
	    $activeGroups = ProjectEvent::model()->forProject($model->id)->
            withStatus(array(ProjectEvent::STATUS_ACTIVE))->groupsOnly()->findAll();
	    foreach ( $activeGroups as $group )
	    {
	        $group->setStatus('finished');
	    }
	    
	    // не начатые группы событий удаляем
	    $draftGroups = ProjectEvent::model()->forProject($model->id)->
            withStatus(array(ProjectEvent::STATUS_DRAFT))->groupsOnly()->findAll();
	    foreach ( $draftGroups as $group )
	    {
    	    $group->delete();
	    }
	    
	    // завершаем все отдельные мероприятия проекта
	    $activeEvents = ProjectEvent::model()->forProject($model->id)->
            withStatus(array(ProjectEvent::STATUS_ACTIVE))->exceptGroups()->findAll();
	    foreach ( $activeEvents as $event )
	    {
	        $event->setStatus('finished');
	    }
	    
	    // удаляем все события которые так и не начались
	    $draftEvents = ProjectEvent::model()->forProject($model->id)->
            withStatus(array(ProjectEvent::STATUS_DRAFT))->exceptGroups()->findAll();
	    foreach ( $draftEvents as $event )
	    {
	        $event->delete();
	    }
	    
	    return true;
	}
	
	/**
	 * Можно ли отметить проект как готовый к запуску?
	 * @return boolean
	 */
	protected function isReady()
	{
	    return true;
    }
    
	/**
	 * Можно ли запустить проект?
	 * @return boolean
	 * 
	 * @todo до запуска проверять что для всех событий проекта есть описание
	 * @todo до запуска проверять что для всех ролей установлены критерии поиска и описание
	 */
	protected function canActivate()
	{
	    return true;
	}
	
	/**
	 * Можно ли приостановить проект?
	 * @return boolean
	 * 
	 * @todo проверять наличие хотя бы одного активного мероприятия
	 */
	protected function canSuspend()
	{
	    return true;
	}
	
	/**
	 * Можно ли сейчас завершить проект?
	 * @return boolean
	 */
	protected function canFinish()
	{
	    return true;
	}
}