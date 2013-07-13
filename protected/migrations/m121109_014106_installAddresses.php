<?php
/** Установка таблицы адресов. Используется заказчиками и пользователями при заполнении акнеты,
 * а также в мероприятиях, в указании как добраться
 * 
 * @author frost
 *
 */
class m121109_014106_installAddresses extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
    private $_tableName = "{{addresses}}";
    
    public function safeUp()
    {
        $fields = array(
            "id" => "pk",
            "objecttype" => "varchar(12) NOT NULL DEFAULT ''",
            "objectid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "type" => "int(11) UNSIGNED DEFAULT NULL",
            "postalcode" => "varchar(10) DEFAULT NULL",
            "country" => "varchar(255) DEFAULT NULL",
            "region" => "varchar(6) DEFAULT NULL",
            "city" => "varchar(255) DEFAULT NULL",
            "streettype" => "varchar(16) DEFAULT NULL",
            "streetname" => "varchar(255) DEFAULT NULL",
            "number" => "varchar(16) DEFAULT NULL",
            "housing" => "varchar(16) DEFAULT NULL",
            "gate" => "varchar(8) DEFAULT NULL",
            "floor" => "int(3) DEFAULT NULL",
            "apartment" => "varchar(16) DEFAULT NULL",
            "timecreated" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timemodified" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "latitude" => "double(10,10) UNSIGNED DEFAULT NULL",
            "longitude" => "double(10,10) UNSIGNED DEFAULT NULL",
            "description" => "varchar(255) DEFAULT NULL",
            "status" => "enum('active', 'deleted') NOT NULL DEFAULT 'active'",
            "encrypted" => "tinyint(1) NOT NULL DEFAULT 0",
            );
        
        $this->createTable($this->_tableName, $fields, $this->MySqlOptions);
        
        // получаем все имена полей чтобы потом создать индексы по каждому из них
        $fieldNames = array_keys($fields);
        
        // перечисляем поля, которые мы не будем индексировать
        // (по ним не планируется производить сортировку и поиск)
        $noIndex = array('postalcode', 'housing', "streettype", 'gate', 'latitude', 'longitude');
        
        // оставляем только те поля, которые будем индексировать
        $indexedFields = array_diff($fieldNames, $noIndex);
        
        
        foreach ( $indexedFields as $field )
        {
            $this->createIndex('idx_'.$field, $this->_tableName, $field);
        }
    }
}