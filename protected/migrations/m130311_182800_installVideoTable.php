<?php

/**
 * Эта миграция устанавливает таблицу для хранения видео
 */
class m130311_182800_installVideoTable extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
    
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = '{{video}}';
        $fields = array(
            "id" => "pk",
            "objecttype" => "varchar(20) DEFAULT NULL",
            "objectid" => "int(11) UNSIGNED DEFAULT 0",
            "name" => "varchar(255) DEFAULT NULL",
            "type" => "varchar(20) DEFAULT NULL",
            "description" => "varchar(255) DEFAULT NULL",
            "link" => "varchar(255) DEFAULT NULL",
            "timecreated" => "int(11) UNSIGNED DEFAULT 0",
            "uploaderid" => "int(11) UNSIGNED DEFAULT 0",
            "md5" => "varchar(128) DEFAULT NULL",
            "size" => "int(11) UNSIGNED DEFAULT 0",
            "status" => "varchar(20) DEFAULT NULL",
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