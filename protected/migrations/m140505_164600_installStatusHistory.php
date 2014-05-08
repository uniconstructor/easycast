<?php

class m140505_164600_installStatusHistory extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{status_history}}";
        $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        
        $columns = array(
            'id' => 'pk',
            'objecttype' => 'varchar(50) NOT NULL',
            'objectid' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'oldstatus' => 'varchar(50) NOT NULL',
            'newstatus' => 'varchar(50) NOT NULL',
            'timecreated' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'userid' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
        );
        $this->createTable($table, $columns, $tableOptions);
        $this->_createIndexes($table, $columns);
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