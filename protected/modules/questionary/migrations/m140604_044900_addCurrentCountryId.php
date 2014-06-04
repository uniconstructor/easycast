<?php

class m140604_044900_addCurrentCountryId extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = '{{questionaries}}';
        $this->dropIndex('idx_middlename', $table);
        
        $this->addColumn($table, 'currentcountryid', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_currentcountryid', $table, 'currentcountryid');
        
        $this->alterColumn($table, 'nativecountryid', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->alterColumn($table, 'ownerid', "int(11) UNSIGNED NOT NULL DEFAULT 1");
        
        $this->alterColumn($table, 'titsize', "varchar(20) DEFAULT NULL");
        
        /////////////////
        $table = '{{q_user_fields}}';
        
        $this->insert($table, array(
            'name'    => 'currentcountryid',
            'storage' => 'questionary',
        ));
    }
}