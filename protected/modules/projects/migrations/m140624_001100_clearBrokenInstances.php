<?php

class m140624_001100_clearBrokenInstances extends CDbMigration
{
    public function safeUp()
    {
        $table = '{{member_instances}}';
        $membersTable = '{{project_members}}';
        
        $instances = $this->dbConnection->createCommand()->select('id, memberid')->from($table)->queryAll();
        foreach ( $instances as $instance )
        {
            $memberExists = $this->dbConnection->createCommand()->select('COUNT(*)')->from($membersTable)->
                where('id='.$instance['memberid'])->query();
            if ( ! $memberExists )
            {
                $this->delete($table, 'id='.$instance['id']);
                echo "\nDeleting broken instance:".$instance['id'];
            }
        }
    }
}