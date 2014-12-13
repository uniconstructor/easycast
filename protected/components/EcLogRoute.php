<?php

/**
 * Класс для сбора данных о действиях системы и участников
 * Эта информация отделена от стандартных логов чтобы ее было проще аналиировать
 * 
 * Levels:
 * process - наиболее важные события: запуск, завершение или смена статуса какого-либо процесса
 *           (например запуск проекта)
 * action  - действие совершенное в системе пользователем или самой системой 
 *           (например переход на страницу, редактирование записи в базе и т. д.)
 * event   - другие события, вызываемые действиями уровнем выше 
 * info    - любая другая информация: все что не относится к первым трем пунктам
 * @todo прописать константы для всех уровней
 */
class EcLogRoute extends CDbLogRoute
{
    /**
     * @var string
     */
    public $logTableName = "{{system_logs}}";
    
    /**
     * @see CDbLogRoute::createLogTable()
     */
    protected function createLogTable($db, $tableName)
    {
        $fields = array(
            'id'         => 'pk',
            'level'      => "VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'info'",
            'category'   => "VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'easycast'",
            'title'      => "VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL",
            'logtime'    => 'BIGINT(21) NOT NULL DEFAULT 0',
            'message'    => "TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL",
            'userid'     => 'BIGINT(21) NOT NULL DEFAULT 0',
            'referer'    => "VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci",
            'module'     => "VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci",
            'controller' => "VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci",
            'action'     => "VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci",
            'userip'     => "varchar(16) CHARACTER SET utf8 COLLATE utf8_unicode_ci",
            'sourcetype' => "varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci",
            'sourceid'   => 'BIGINT(21) NOT NULL DEFAULT 0',
            'targettype' => "varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci",
            'targetid'   => 'BIGINT(21) NOT NULL DEFAULT 0',
        );
        $db->createCommand()->createTable($tableName, $fields, "ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci");
        
        unset($fields['message']);
        foreach ( $fields as $name => $type )
        {
            $db->createCommand()->createIndex('idx_'.$name, $this->logTableName, $name);
        }
    }
    
    /**
     * @see CDbLogRoute::processLogs()
     */
    protected function processLogs($logs)
    {
        $userip  = Yii::app()->request->userHostAddress;
        $path    = Yii::app()->urlManager->parseUrl($request);
        $command = $this->getDbConnection()->createCommand();
        
        foreach ( $logs as $log )
        {
            $command->insert($this->logTableName, array(
                'level'     => $log[1],
                'category'  => $log[2],
                'logtime'   => (int)$log[3],
                'message'   => $log[4],
                'userid'    => (int)$log[5],
                'path'      => $log[6],
                'action'    => $log[7],
                'userip'    => $userip,
            ));
        }
    }
}