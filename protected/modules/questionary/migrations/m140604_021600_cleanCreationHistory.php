<?php

class m140604_021600_cleanCreationHistory extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table  = '{{q_creation_history}}';
        $fields = array('id', 'objecttype', 'objectid', 'questionaryid');
        
        // получаем все записи из истории создания анкет
        $records = $this->dbConnection->createCommand()->select($fields)->from($table)->queryAll();
        $cleaned = array();
        
        foreach ( $records as $record )
        {// ищем все дубликаты для каждой записи и удаляем их
            $conditions = array('and', 
                '`questionaryid` = :questionaryid',
                '`objecttype` = :objecttype',
                '`objectid` = :objectid',
                array('not in', 'id', array($record['id'])),
            );
            $params = array(
                ':questionaryid' => $record['questionaryid'],
                ':objecttype'    => $record['objecttype'],
                ':objectid'      => $record['objectid'],
            );
            
            $duplicates = $this->dbConnection->createCommand()->select($fields)->from($table)->
                where($conditions, $params)->queryAll();
            foreach ( $duplicates as $duplicate )
            {
                if ( in_array($duplicate['id'], $cleaned) )
                {
                    continue;
                }
                echo "Deleting history: (objectid={$duplicate['objectid']}|questionaryid={$duplicate['questionaryid']})\n";
                $this->dbConnection->createCommand()->delete($table, 'id=:id', array(':id' => $duplicate['id']));
                $cleaned[$duplicate['id']] = $record['id'];
            }
            unset($conditions, $params, $duplicates);
        }
    }
}