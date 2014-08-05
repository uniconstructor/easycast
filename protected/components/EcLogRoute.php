<?php

/**
 * Класс для сбора данных о действиях системы и участников
 * Эта информация отделена от стандартных логов чтобы ее было проще аналиировать
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
            'id'       => 'pk',
            'level'    => 'varchar(128)',
            'category' => 'varchar(128)',
            'logtime'  => 'integer',
            'message'  => 'text',
            'userid'   => 'integer',
            'path'     => 'varchar(255)',
            'action'   => 'varchar(255)',
        );
        $db->createCommand()->createTable($tableName, $fields);
        
        unset($fields['message']);
        foreach ( $fields as $name => $type )
        {
            $db->createCommand()->createIndex('idx_'.$name, $table, $name);
        }
    }
    
    /**
     * @see CDbLogRoute::processLogs()
     */
    protected function processLogs($logs)
    {
        $command = $this->getDbConnection()->createCommand();
        foreach ( $logs as $log )
        {
            $command->insert($this->logTableName,array(
                'level'    => $log[1],
                'category' => $log[2],
                'logtime'  => (int)$log[3],
                'message'  => $log[4],
                'userid'   => (int)$log[5],
                'path'     => $log[6],
                'action'   => $log[7],
            ));
        }
    }
}