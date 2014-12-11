<?php

class m140613_124500_installTags extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci';
        
        $table = "{{tags}}";
        $columns = array(
            'id' => 'pk',
            'objecttype' => "VARCHAR(50) NOT NULL DEFAULT 'system'",
            'objectid' => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            'sourcetype' => "VARCHAR(50) NOT NULL DEFAULT 'user'",
            'sourceid' => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            'name' => 'varchar(255) NOT NULL',
            'rawname' => 'varchar(255) NOT NULL',
            'description' => 'varchar(255) DEFAULT NULL',
            'type' => "VARCHAR(50) NOT NULL DEFAULT 'questionary'",
            'official' => "tinyint(1) NOT NULL DEFAULT 0",
            'timecreated' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timemodified' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
        );
        $this->createTable($table, $columns, $tableOptions);
        $this->_createIndexes($table, $columns);
        unset($columns);
        
        
        $table = "{{tag_instances}}";
        $columns = array(
            'id' => 'pk',
            'tagid' => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            'objecttype' => "VARCHAR(50) NOT NULL DEFAULT 'system'",
            'objectid' => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            'sourcetype' => "VARCHAR(50) NOT NULL DEFAULT 'user'",
            'sourceid' => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            'sortorder' => "int(11) UNSIGNED DEFAULT NULL",
            'timecreated' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timemodified' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
        );
        $this->createTable($table, $columns, $tableOptions);
        $this->_createIndexes($table, $columns);
        unset($columns);
        
        
        $table = "{{categories}}";
        $columns = array(
            'id' => 'pk',
            'parentid' => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            'type' => "VARCHAR(50) NOT NULL", // section, field, acivity...
            'name' => 'varchar(255) NOT NULL',
            'description' => 'varchar(255) DEFAULT NULL',
        );
        $this->createTable($table, $columns, $tableOptions);
        $this->_createIndexes($table, $columns);
        unset($columns);
        
        
        $table = "{{category_instances}}";
        $columns = array(
            'id' => 'pk',
            'categoryid' => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            'objecttype' => "VARCHAR(50) NOT NULL", // vacancy, project, projectEvent, ...
            'objectid' => "int(11) UNSIGNED NOT NULL DEFAULT 0",
        );
        $this->createTable($table, $columns, $tableOptions);
        $this->_createIndexes($table, $columns);
        unset($columns);
        
        
        $table = '{{menu_items}}';
        $this->dropTable($table);
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