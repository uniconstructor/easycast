<?php 
////////////////////////////////////////////////////////////////////////////////////////
// This simpleWorkflow definition file was generated automatically
// from a yEd Graph Editor file (.graphml).
//
// Workflow Name : swVideo
// Created       : 01/05/2014 17:32

class swVideo {
	const WORKFLOW_ID = 'swVideo';
	const PENDING = 'swVideo/pending';
	const APPROVED = 'swVideo/approved';
	const REJECTED = 'swVideo/rejected';

	public function getDefinition(){
		return array(
			'initial' => self::PENDING,
			'node'    => array(
				array(
					'id' => self::PENDING,
					'label' => '',//Yii::t('workflow', 'pending'),
					'constraint' => '',
					'transition' => array(
						self::PENDING,
						self::APPROVED,
						self::REJECTED,
					),
					'metadata' => array(
						'background-color' => '#FFCC00',
						'color' => '#000000',
					),
				),
				array(
					'id' => self::APPROVED,
					'label' => '',//Yii::t('workflow', 'approved'),
					'constraint' => '',
					'metadata' => array(
						'background-color' => '#FFCC00',
						'color' => '#000000',
					),
				),
				array(
					'id' => self::REJECTED,
					'label' => '',//Yii::t('workflow', 'rejected'),
					'constraint' => '',
					'metadata' => array(
						'background-color' => '#FFCC00',
						'color' => '#000000',
					),
				),
			)
		);
	}
}
