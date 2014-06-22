<?php

class m140619_174300_addMemberMarkers extends CDbMigration
{
    public function safeUp()
    {
        $table = "{{member_instances}}";
        $this->addColumn($table, 'linktype', "varchar(50) DEFAULT 'nolink'");
        $this->createIndex('idx_linktype', $table, 'linktype');
    }
}