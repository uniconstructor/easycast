<?php

class m140613_124900_installSectionInstances extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci';
        
        $table = "{{section_instances}}";
        $columns = array(
            'id' => 'pk',
            'sectionid' => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            'objecttype' => "VARCHAR(50) NOT NULL DEFAULT 'vacancy'",
            'objectid' => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            'visible' => "tinyint(1) NOT NULL DEFAULT 1",
            'newname' => 'varchar(255) NOT NULL',
            'newdescription' => 'varchar(255) DEFAULT NULL',
            'timecreated' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timemodified' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
        );
        $this->createTable($table, $columns, $tableOptions);
        $this->_createIndexes($table, $columns);
        unset($columns);
        
        
        $table = "{{catalog_sections}}";
        $this->alterColumn($table, 'shortname', "varchar(128) DEFAULT NULL");
        $this->alterColumn($table, 'content', "varchar(50) DEFAULT 'users'");
        $this->dropColumn($table, 'lang');
        $this->dropColumn($table, 'count');
        $this->addColumn($table, 'categoryid', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
        $this->createIndex('idx_categoryid', $table, 'categoryid');
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