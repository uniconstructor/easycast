<?php

class m140612_110100_addLabelOverride extends CDbMigration
{
    public function safeUp()
    {
        $table = "{{q_field_instances}}";
        $this->addColumn($table, 'newlabel', 'varchar(255) DEFAULT NULL');
        $this->createIndex('idx_newlabel', $table, 'newlabel');
        $this->addColumn($table, 'newdescription', 'varchar(2047) DEFAULT NULL');
        $this->createIndex('idx_newdescription', $table, 'newdescription');
        
        $table = '{{questionaries}}';
        $this->addColumn($table, 'visible', 'tinyint(1) DEFAULT 1');
        $this->createIndex('idx_visible', $table, 'visible');
        
        $table  = "{{q_user_fields}}";
        $this->insert($table, array(
            'name'     => 'visible',
            'storage'  => 'questionary',
            'external' => 0,
            'multiple' => 0,
            'type'     => 'checkbox',
        ));
    }
}