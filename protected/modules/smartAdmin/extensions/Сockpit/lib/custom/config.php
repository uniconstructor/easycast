<?php
/**
 * Cockpit configuration
 */
return array(
    // сессия для хранения данных cockpit
    'session.name' => 'Yii.easycast.cockpit.session',
    
    'i18n'  => 'ru',
    
    'paths' => [
        'data'    => Yii::app()->getRuntimePath().DIRECTORY_SEPARATOR.'cockpit'.DIRECTORY_SEPARATOR.'data',
        'backups' => Yii::app()->getRuntimePath().DIRECTORY_SEPARATOR.'cockpit'.DIRECTORY_SEPARATOR.'backups',
        'cache'   => Yii::app()->getRuntimePath().DIRECTORY_SEPARATOR.'cockpit'.DIRECTORY_SEPARATOR.'cache',
    ]
);