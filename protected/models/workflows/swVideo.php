<?php 

/**
 * Класс определяющий правила и пути смены статусов для видео
 * @todo добавить статус "обработка" (для оцифровки видео)
 */
class swVideo
{
	const WORKFLOW_ID = 'swVideo';
	const PENDING     = 'swVideo/pending';
	const APPROVED    = 'swVideo/approved';
	const REJECTED    = 'swVideo/rejected';
    
	/**
	 * @return array
	 */
	public function getDefinition()
	{
		return array(
			'initial' => self::PENDING,
			'node'    => array(
				array(
					'id'         => self::PENDING,
					'label'      => 'Ждет проверки',
					'constraint' => '',
					'transition' => array(
						self::APPROVED,
						self::REJECTED,
					),
				),
				array(
					'id'         => self::APPROVED,
					'label'      => 'Проверено',
					'constraint' => '',
				    'transition' => array(
				        self::REJECTED,
				    ),
				),
				array(
					'id'         => self::REJECTED,
					'label'      => 'Отклонено',
					'constraint' => '',
				),
			)
		);
	}
}