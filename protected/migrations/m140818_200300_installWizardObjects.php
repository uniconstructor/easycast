<?php

class m140818_200300_installWizardObjects extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci';
        
        $table   = "{{wizards}}";
        $columns = array(
            'id'           => 'pk',
            'name'         => "VARCHAR(255) DEFAULT NULL",
            'description'  => "VARCHAR(4095) DEFAULT NULL",
            'filltype'     => "VARCHAR(20) NOT NULL DEFAULT 'form'",
            'timecreated'  => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timemodified' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'objecttype'   => "VARCHAR(50) DEFAULT NULL",
            'objectid'     => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
        );
        $this->createTable($table, $columns, $tableOptions);
        $this->_createIndexes($table, $columns);
        unset($table);
        unset($columns);
        
        $table = "{{wizard_steps}}";
        $this->addColumn($table, 'objecttype', "VARCHAR(50) DEFAULT NULL");
        $this->createIndex('idx_objecttype', $table, 'objecttype');
        $this->addColumn($table, 'objectid', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_objectid', $table, 'objectid');
        $this->addColumn($table, 'sortorder', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_sortorder', $table, 'sortorder');
        unset($table);
        
        // добавляем возможность задавать уникальность элементов в списках
        $table = "{{easy_lists}}";
        $this->addColumn($table, 'unique', "tinyint(1) NOT NULL DEFAULT 0");
        $this->createIndex('idx_unique', $table, 'unique');
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
    protected function _createIndexes($table, $fields, $excluded = array(), $idxPrefix = "idx_")
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