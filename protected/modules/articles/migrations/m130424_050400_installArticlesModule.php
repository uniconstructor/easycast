<?php

/**
 * Установка таблицы статей
 */
class m130424_050400_installArticlesModule extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';

    public function safeUp()
    {
        $table = "{{articles}}";
        $fields = array(
            "id" => "pk",
            "name" => "varchar(255) DEFAULT NULL",
            "description" => "varchar(4095) DEFAULT NULL",
            "content" => "TEXT DEFAULT NULL",
            "timecreated" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timemodified" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "authorid" => "int(11) UNSIGNED NOT NULL DEFAULT 1",
            "visible" => "tinyint(1) DEFAULT 0",
        );
        $this->createTable($table, $fields, $this->MySqlOptions);
        
        $this->_createIndexes($table, $fields, array('content'));
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
        if ( isset($indexedFields['id']) ) unset($indexedFields['id']);
        foreach ( $indexedFields as $field )
        {
            $this->createIndex($idxPrefix.$field, $table, $field);
        }
    }
}