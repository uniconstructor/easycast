<?php

class m130223_051400_AddPremoderation extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
    
    public function safeUp()
    {
        $table = '{{questionaries}}';
        // комментарий администрации при заполнении анкеты
        $this->addColumn($table, 'admincomment', "varchar(255) DEFAULT NULL");
        
        // создаем таблицу "введенные анкеты", чтобы мы знали какой оператор сколько анкет ввел
        $table = '{{q_creation_history}}';
        $fields = array(
            "id" => "pk",
            "userid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "questionaryid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timecreated" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
        );
        $this->createTable($table, $fields, $this->MySqlOptions);
        
        $this->_createIndexes($table, $fields);
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