<?php

class m130731_080000_addMeetingPlace extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = '{{project_events}}';
        
        $this->addColumn($table, 'meetingplace', "VARCHAR(2048) DEFAULT NULL");
        $this->createIndex('idx_meetingplace', $table, 'meetingplace');
    }
}