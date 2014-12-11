<?php

class m140803_072400_installConfigTables extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci';

        $table   = "{{config}}";
        $columns = array(
            'id'           => 'pk',
            'name'         => "VARCHAR(255) NOT NULL",
            'title'        => "VARCHAR(255) NOT NULL",
            'description'  => "VARCHAR(4095) DEFAULT NULL",
            'type'         => "VARCHAR(20) NOT NULL DEFAULT 'text'",
            'minvalues'    => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'maxvalues'    => 'int(11) UNSIGNED NOT NULL DEFAULT 1',
            'timecreated'  => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timemodified' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
        );
        $this->createTable($table, $columns, $tableOptions);
        $this->_createIndexes($table, $columns);
        unset($table);
        unset($columns);

        $table   = "{{config_instances}}";
        $columns = array(
            'id'          => 'pk',
            'configid'    => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            'objecttype'  => "VARCHAR(50) NOT NULL DEFAULT 'system'",
            'objectid'    => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timecreated' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
        );
        $this->createTable($table, $columns, $tableOptions);
        $this->_createIndexes($table, $columns);
        unset($table);
        unset($columns);

        $table   = "{{config_values}}";
        $columns = array(
            'id'           => 'pk',
            'objecttype'   => "VARCHAR(50) NOT NULL DEFAULT 'config'",
            'objectid'     => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'type'         => "VARCHAR(20) NOT NULL DEFAULT 'string'",
            'value'        => "VARCHAR(4095) DEFAULT NULL",
            'default'      => "tinyint(1) NOT NULL DEFAULT 0",
            'timecreated'  => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timemodified' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
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
