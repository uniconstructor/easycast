<?php

class m140726_214800_setProjectStatuses extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = '{{projects}}';
        $projects = $this->dbConnection->createCommand()->select('id, status')->from($table)->queryAll();
        
        foreach ( $projects as $project )
        {
            switch ( $project['status'] )
            {
                case 'draft':    $newStatus = 'swProject/draft';    break;
                case 'active':   $newStatus = 'swProject/active';   break;
                case 'finished': $newStatus = 'swProject/finished'; break;
                default: throw new CException('Неизвестный статус проекта: id='.$project['id']);
            }
            $this->update($table, array('status' => $newStatus), 'id='.$project['id']);
        }
        
        $this->alterColumn($table, 'status', "VARCHAR(50) NOT NULL DEFAULT 'swProject/draft'");
    }
}