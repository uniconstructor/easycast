<?php

class m140614_160000_addMemberInstances extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci';

        $table = "{{member_instances}}";
        $columns = array(
            'id' => 'pk',
            'objecttype' => "varchar(50) NOT NULL",
            'objectid' => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            'memberid' => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            'comment' => 'varchar(255) DEFAULT NULL',
            'sourcetype' => "VARCHAR(50) NOT NULL DEFAULT 'user'",
            'sourceid' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timecreated' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timemodified' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'status' => "varchar(50) DEFAULT 'draft'",
        );
        $this->createTable($table, $columns, $tableOptions);
        $this->_createIndexes($table, $columns);
        unset($columns);
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