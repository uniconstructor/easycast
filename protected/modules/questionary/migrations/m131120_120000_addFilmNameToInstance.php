<?php

class m131120_120000_addFilmNameToInstance extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{q_film_instances}}";
        $this->addColumn($table, 'name', "varchar(255) NOT NULL DEFAULT ''");
        $this->createIndex('idx_name', $table, 'name');
        
        $this->addColumn($table, 'director', "varchar(255) NOT NULL DEFAULT ''");
        $this->createIndex('idx_director', $table, 'director');
        
        $this->addColumn($table, 'date', "int(11) NOT NULL DEFAULT 0");
        $this->createIndex('idx_date', $table, 'date');
        
        $this->addColumn($table, 'timemodified', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_timemodified', $table, 'timemodified');
        
        $this->alterColumn($table, 'role', "varchar(255) NOT NULL DEFAULT ''");
        $this->alterColumn($table, 'timecreated', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        
        
        $table = "{{q_films}}";
        $this->addColumn($table, 'timecreated', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_timecreated', $table, 'timecreated');
        
        $this->addColumn($table, 'timemodified', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_timemodified', $table, 'timemodified');
        
        $this->dropColumn($table, 'pictureid');
        $this->alterColumn($table, 'name', "varchar(255) NOT NULL DEFAULT ''");
        $this->alterColumn($table, 'director', "varchar(255) NOT NULL DEFAULT ''");
        $this->alterColumn($table, 'date', "int(11) NOT NULL DEFAULT 0");
    }
}