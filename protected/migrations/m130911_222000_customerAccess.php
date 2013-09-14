<?php

class m130911_222000_customerAccess extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = '{{customer_invites}}';

        $fields = array(
            "id" => "pk",
            "objecttype" => "varchar(11) NOT NULL DEFAULT ''",
            "objectid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "key" => "varchar(32) NOT NULL DEFAULT ''",
            "key2" => "varchar(32) NOT NULL DEFAULT ''",
            "email" => "varchar(255) NOT NULL DEFAULT ''",
            "name" => "varchar(255) NOT NULL DEFAULT ''",
            "managerid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timecreated" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timeused" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
        );
        
        $this->createTable($table, $fields, $this->MySqlOptions);
        $this->_createIndexes($table, $fields);
    }
    
    /**
     * Create indexes for all fields in the table
     * @param string $table - the table name
     * @param array $fields - table fields
     * example: array( "fieldname1" => "fieldtype1", "fieldname2" => "fieldtype2", ... )
     * @param array $excluded - not indexed fields
     * example: array("fieldname1", "fieldname2", "fieldname3", ...)
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