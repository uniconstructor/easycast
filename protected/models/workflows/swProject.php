<?php 

/**
 * Класс определяющий правила и пути смены статусов для проекта
 * 
 * @todo при возобновлении завершенного проекта помечать все его старые события как архивные
 *       для того чтобы они не перемешались с новыми
 * @todo переименовать методы смены статуса в setFinished вместо toFinished
 * @todo проверять каждый результат смены статуса, пропускать неудавшиеся события и запоминать их в лог
 */
class swProject
{
    /**
     * @var string - название рабочего процесса (как правило совпадает с названием класса)
     *               
     */
	const WORKFLOW_ID = 'swProject';
	/**
	 * @var string - статус проекта: черновик. Проект только что создан. 
	 *               Необходимая инофрмация еще либо не внесена либо вносится в данный момент.
	 *               Проект в этом статусе можно удалить.
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
	 * @var string - статус проекта: завершен. Активных мероприятий в проекте нет, съемки
	 *               завершены и неизвестно вернемся ли мы к нему когда-нибудь.
	 *               Возможен переход проекта в статус "черновик" в случае если проект будет
	 *               перезапущен (например мы уже и не надеялись на возобновление съемок сериала
	 *               и завершили проект - но заказчик через год решил снять продолжение)
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
					'label'      => Yii::t('ProjectsModule.projects', 'project_status_'.self::DRAFT),
					'constraint' => '',
					'transition' => array(
					    self::READY,
						self::ACTIVE => array($this, 'toActive'),
					    self::FINISHED,
					),
				    'metadata' => array(
				        'bgColor' => '#c79121',
					),
				),
				array(
					'id'         => self::READY,
					'label'      => Yii::t('ProjectsModule.projects', 'project_status_'.self::READY),
					'constraint' => '$this->isReady();',
					'transition' => array(
					    self::ACTIVE => array($this, 'toActive'),
					),
				    'metadata' => array(
				        'bgColor' => '#3276b1',
				    ),
				),
			    array(
			        'id'         => self::ACTIVE,
			        'label'      => Yii::t('ProjectsModule.projects', 'project_status_'.self::ACTIVE),
			        'constraint' => '$this->canActivate();',
			        'transition' => array(
			            self::SUSPENDED,
			            self::FINISHED => array($this, 'toFinished'),
			        ),
			        'metadata' => array(
			            'bgColor' => '#739e73',
			        ),
			    ),
			    array(
			        'id'         => self::SUSPENDED,
			        'label'      => Yii::t('ProjectsModule.projects', 'project_status_'.self::SUSPENDED),
			        'constraint' => '$this->canSuspend();',
			        'transition' => array(
			            self::READY,
			            self::ACTIVE,
			            self::FINISHED,
			        ),
			        'metadata' => array(
			            'bgColor' => '#57889c',
			        ),
			    ),
			    array(
			        'id'         => self::FINISHED,
			        'label'      => Yii::t('ProjectsModule.projects', 'project_status_'.self::FINISHED),
			        'constraint' => '$this->canFinish();',
			        'transition' => array(
			            self::DRAFT,
			        ),
			        'metadata' => array(
			            'bgColor' => '#999',
			        ),
			    ),
			)
		);
	}
	
	/**
	 * Действия, выполняемые при запуске проекта
	 * 
	 * @param  Project $model
	 * @param  string $srcStatus
	 * @param  string $destStatus
	 * @return bool
	 */
	public function toActive($model, $srcStatus, $destStatus)
	{
	    // активируем только те мероприятия на которые уже созданы роли
	    $filledEvents = ProjectEvent::model()->forProject($model->id)->
            withStatus(ProjectEvent::STATUS_DRAFT)->withVacancies()->findAll();
	    foreach ( $filledEvents as $event )
	    {
	       $event->setStatus('active');
	    }
	    return true;
	}
	
	/**
	 * Действия, выполняемые при завершении проекта
	 *
	 * @param  Project $model
	 * @param  string $srcStatus
	 * @param  string $destStatus
	 * @return bool
	 */
	public function toFinished($model, $srcStatus, $destStatus)
	{
	    // завершаем все отдельные мероприятия проекта
	    $activeEvents = ProjectEvent::model()->forProject($model->id)->
            withStatus(array(ProjectEvent::STATUS_ACTIVE))->exceptGroups()->findAll();
	    {
	        $event->setStatus('finished');
	    }
	    // удаляем все события которые так и не начались
	    $draftEvents = ProjectEvent::model()->forProject($model->id)->
            withStatus(ProjectEvent::STATUS_DRAFT)->exceptGroups()->findAll();
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