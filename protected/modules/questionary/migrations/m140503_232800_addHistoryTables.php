<?php

class m140503_232800_addHistoryTables extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{q_edit_history}}";
        $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        
        $columns = array(
            'id' => 'pk',
            "questionaryid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "field" => "varchar(20) NOT NULL",
            "type" => "varchar(12) NOT NULL DEFAULT 'edit'",
            "oldvalue" => "text DEFAULT NULL",
            "newvalue" => "text DEFAULT NULL",
            "timecreated" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "editortype" => "varchar(20) NOT NULL DEFAULT 'user'",
            "editorid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
        );
        
        $this->createTable($table, $columns, $tableOptions);
        $this->_createIndexes($table, $columns, array('oldvalue', 'newvalue'));
        unset($columns);
        
        $table = "{{q_notification_settings}}";
        $columns = array(
            'id' => 'pk',
            "questionaryid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "source" => "varchar(20) NOT NULL",
            "type" => "varchar(20) DEFAULT NULL",
            "enabled" => "tinyint(1) DEFAULT NULL",
            "email" => "tinyint(1) DEFAULT 1",
            "sms" => "tinyint(1) DEFAULT NULL",
            "minsalary" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timecreated" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timemodified" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
        );
        $this->createTable($table, $columns, $tableOptions);
        $this->_createIndexes($table, $columns);
        unset($columns);
        
        $table = "{{q_activity_types}}";
        $this->addColumn($table, 'search', "tinyint(1) DEFAULT NULL");
        $this->addColumn($table, 'form', "tinyint(1) DEFAULT NULL");
        $this->createIndex('idx_search', $table, 'search');
        $this->createIndex('idx_form', $table, 'form');
        
        $table = "{{q_universities}}";
        $this->addColumn($table, 'search', "tinyint(1) DEFAULT NULL");
        $this->addColumn($table, 'form', "tinyint(1) DEFAULT NULL");
        $this->createIndex('idx_search', $table, 'search');
        $this->createIndex('idx_form', $table, 'form');
        
        $table = "{{q_theatres}}";
        $this->addColumn($table, 'search', "tinyint(1) DEFAULT NULL");
        $this->addColumn($table, 'form', "tinyint(1) DEFAULT NULL");
        $this->createIndex('idx_search', $table, 'search');
        $this->createIndex('idx_form', $table, 'form');
    }
    
    /**
     * Create indexes for all fields in the table
     * @param string $table - the table name
     * @param array $fields - table fields
     *                         example: array( "fieldname1" => "fieldtype1", "fieldname2" => "fieldtype2", ... )
     * @param array $excluded - not indexed fields
     *                           example: array("fieldname1", "fieldname2", "fieldname3", ...)
     * @param string $idxPrefix - index name prefix (default is "idx_")
     *
     * @return null
     */
    protected function _createIndexes($table, $fields, $excluded=array(), $idxPrefix="idx_")
    {
        // gather all field names
        $fieldNames = array_keys($fields);
    
        // exclude not needed fields from index
        // ("id" is already primary key, so we never need to create additional index for it)
        $noIndex = CMap::mergeArray(array("id"), $excluded);
    
        $indexedFields = array_diff($fieldNames, $noIndex);
    
        foreach ( $indexedFields as $field )
        {
            $this->createIndex($idxPrefix.$field, $table, $field);
        }
    }
}