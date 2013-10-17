<?php

class m131014_013200_installReports extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        // устанавливаем таблицу отчетов
        $table = '{{reports}}';
        $fields = array(
            "id"       => "pk",
            "authorid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "name" => "varchar(255) NOT NULL DEFAULT ''",
            "type" => "varchar(20) NOT NULL DEFAULT ''",
            "timecreated" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timemodified" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "plantime" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "data" => "text",
            "status" => "varchar(50) NOT NULL DEFAULT 'draft'",
        );
        $this->createTable($table, $fields, 'ENGINE=InnoDB CHARSET=utf8');
        $this->_createIndexes($table, $fields, array('data'));
        
        // устанавливаем таблицу связей отчетов
        $table = '{{report_links}}';
        $fields = array(
            "id"       => "pk",
            "reportid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "linktype" => "varchar(20) NOT NULL DEFAULT 'default'",
            "linkid"   => "int(11) UNSIGNED NOT NULL DEFAULT 0",
        );
        $this->createTable($table, $fields, 'ENGINE=InnoDB CHARSET=utf8');
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