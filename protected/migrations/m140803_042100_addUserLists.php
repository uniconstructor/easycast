<?php

class m140803_042100_addUserLists extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci';

        $table   = "{{easy_lists}}";
        $columns = array(
            'id'           => 'pk',
            'name'         => "VARCHAR(255) NOT NULL",
            'description'  => "VARCHAR(4095) DEFAULT NULL",
            'allowupdate'  => "tinyint(1) NOT NULL DEFAULT 0",
            'updatemethod' => "VARCHAR(10) DEFAULT NULL", // null/auto/manual/any
            'timecreated'  => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timemodified' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timeupdated'  => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'updateperiod' => 'int(11) UNSIGNED NOT NULL DEFAULT 3600',
        );
        $this->createTable($table, $columns, $tableOptions);
        $this->_createIndexes($table, $columns);
        unset($table);
        unset($columns);

        $table   = "{{easy_list_instances}}";
        $columns = array(
            'id'          => 'pk',
            'easylistid'  => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            'objecttype'  => "VARCHAR(50) DEFAULT NULL",
            'objectid'    => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timecreated' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
        );
        $this->createTable($table, $columns, $tableOptions);
        $this->_createIndexes($table, $columns);
        unset($table);
        unset($columns);

        $table   = "{{easy_list_items}}";
        $columns = array(
            'id'            => 'pk',
            'easylistid'    => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            'objecttype'    => "VARCHAR(50) NOT NULL DEFAULT 'item'",
            'objectid'      => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'name'          => "VARCHAR(255) DEFAULT NULL",
            'timecreated'   => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timemodified'  => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'sortorder'     => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'status'        => "VARCHAR(50) NOT NULL",
        );
        $this->createTable($table, $columns, $tableOptions);
        $this->_createIndexes($table, $columns);
        unset($table);
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
