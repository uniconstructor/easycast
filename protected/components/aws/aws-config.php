<?php
/**
 * AWS configuration file
 */

return array(
	'includes' => array('_aws'),
	'services' => array(
		'default_settings' => array(
			'params' => array(
				'key'    => Yii::app()->params['AWSAccessKey'],
	            'secret' => Yii::app()->params['AWSSecret'],
				'region' => 'us-east-1',
			)
		),
	)
);