<?php

/**
 * Наш собственный класс для миграций
 * Добавляет в стандартную миграцию дополнительные методы, 
 * облегчающие работу с таблицами и перенос данных
 * 
 * Все создаваемые этой миграцией таблицы будут использовать движок InnoDB, 
 * так как такие таблицы в Amazon RDS можно восстановить из резервной копии
 * к любой точке во времени (restore to point in time)
 * 
 * Кодировка всех таблиц (да и вообще всех данных в системе) всегда должна быть utf8
 * Используемое сравнение (collation) для всех таблиц базы: utf8_inicode_ci 
 */
class EcMigration extends CDbMigration
{
    /**
     * @var string - Настройки для всех создаваемых этой миграцией таблиц
     */
    const EC_MYSQL_OPTIONS = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci';
        
    /**
     * Create indexes for all fields in the table
     * @param string $table     - table name
     * @param array  $fields    - table fields
     *                            Example: 
     *                            array("fieldname1" => "fieldtype1", "fieldname2" => "fieldtype2", ... )
     * @param array  $excluded  - not indexed fields
     *                            example: array("fieldname1", "fieldname2", "fieldname3", ...)
     * @param string $idxPrefix - index name prefix (default is "idx_")
     *
     * @return null
     */
    protected function ecCreateIndexes($table, $fields, $excluded = array(), $idxPrefix = "idx_")
    {
        // gather all field names
        $fieldNames = array_keys($fields);
        // exclude not needed fields from index
        // ("id" is already primary key, so we never need to create additional index for it)
        $noIndex       = CMap::mergeArray(array("id"), $excluded);
        $indexedFields = array_diff($fieldNames, $noIndex);
    
        foreach ( $indexedFields as $field )
        {
            $this->createIndex($idxPrefix.$field, $table, $field);
        }
    }
    
    /**
     * @see CDbMigration::createTable()
     */
    public function createTable($table, $columns, $options=self::EC_MYSQL_OPTIONS)
    {
        return parent::createTable($table, $columns, $options);
    }
}