<?php

/**
 * Эта миграция устанавливает новые поля в анкету (без индексов)
 * Удаляет addressid (он не нужен в анкете)
 * Добавляет таблицы для хранения работы в театрах
 * Добавляет поле "специальность" для музыкальных и театральных ВУЗов
 */
class m130311_100800_TheatresAndMoreNewFields extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
    
    public function safeUp()
    {
        Yii::import('application.modules.questionary.models.*');
        
        // Обновляем анкету
        $table = '{{questionaries}}';
        // удаляем ссылку на адрес
        $this->dropIndex('idx_addressid', $table);
        $this->dropColumn($table,'addressid');
        // удаляем ссылку на условия съемки
        $this->dropIndex('idx_conditionsid', $table);
        $this->dropColumn($table, 'conditionsid');
        
        // Добавляем ИНН
        $this->addColumn($table, 'inn', "varchar(32) DEFAULT NULL");
        // игровой возраст
        $this->addColumn($table, 'playagemin', "int(11) UNSIGNED DEFAULT NULL");
        $this->addColumn($table, 'playagemax', "int(11) UNSIGNED DEFAULT NULL");
        $this->createIndex('idx_playagemin', $table, 'playagemin');
        $this->createIndex('idx_playagemax', $table, 'playagemax');
        // тип лица
        $this->addColumn($table, 'facetype', "varchar(20) DEFAULT NULL");
        $this->createIndex('idx_facetype', $table, 'facetype');
        // длина волос
        $this->addColumn($table, 'hairlength', "varchar(20) DEFAULT NULL");
        $this->createIndex('idx_hairlength', $table, 'hairlength');
        // согласен ли покрасить волосы для съемок
        $this->addColumn($table, 'canrepainthair', "tinyint(1) DEFAULT NULL");
        $this->createIndex('idx_canrepainthair', $table, 'canrepainthair');
        // есть ли опыт работы в театре
        $this->addColumn($table, 'istheatreactor', "tinyint(1) DEFAULT NULL");
        $this->createIndex('idx_istheatreactor', $table, 'istheatreactor');
        // Медийный актер
        $this->addColumn($table, 'ismediaactor', "tinyint(1) DEFAULT NULL");
        $this->createIndex('idx_ismediaactor', $table, 'ismediaactor');
        
        
        // добавляем специальность к ВУЗам
        $table = '{{q_university_instances}}';
        $this->addColumn($table, 'specialty', "varchar(255) DEFAULT NULL");
        $this->createIndex('idx_specialty', $table, 'specialty');
        
        // Создаем таблицы для хранения информации о работе в театре
        $table = '{{q_theatres}}';
        $fields = array(
            "id"     => "pk",
            "name"   => "varchar(255) DEFAULT NULL",
            "system" => "tinyint(1) DEFAULT 0",
        );
        
        $this->createTable($table, $fields, $this->MySqlOptions);
        $this->_createIndexes($table, $fields);
        unset($fields);
        
        // связь анкет с театрами
        $table = '{{q_theatre_instances}}';
        $fields = array(
            "id" => "pk",
            "questionaryid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "theatreid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timestart" => "int(11) UNSIGNED DEFAULT NULL",
            "timeend" => "int(11) UNSIGNED DEFAULT NULL",
            "director" => "varchar(255) DEFAULT NULL",
            "timecreated" => "int(11) UNSIGNED DEFAULT NULL",
            "timemodified" => "int(11) UNSIGNED DEFAULT NULL",
        );
        
        $this->createTable($table, $fields, $this->MySqlOptions);
        $this->_createIndexes($table, $fields);
        unset($fields);
        
        // Добавляем новые значения по умолчанию для новых полей
        $table = '{{q_activity_types}}';
        
        $language = 'ru';
        $values   = array();
        
        // Новые типы внешности
        $name = 'looktype';
        $translations = array(
            'arabian' => 'Арабский',
            'latino' => 'Латиноамериканский',
            'metis' => 'Метис',
            'hindu' => 'Индусский',
            'mulatto' => 'Мулат',
            'tatarian' => 'Татарский',
            'gypsy' => 'Цыганский',
        );
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
        
        // тип лица
        $name = 'facetype';
        $translations = array(
            'oval' => 'Овальное',
            'square' => 'Квадратное',
            'long' => 'Вытянутое',
            'triangle' => 'Треугольное',
            'round' => 'Круглое',
        );
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
        
        // длина волос
        $name = 'hairlength';
        $translations = array(
            'no' => 'Отсутствуют',
            'short' => 'Короткая стрижка',
            'middle' => 'Средняя',
            'beforeshoulder' => 'До плеч',
            'aftershoulder' => 'Ниже плеч',
            'verylong' => 'Ниже талии',
        );
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
        
        foreach ($values as $value)
        {// заносим все стандартные значения в базу
            $this->insert($table, $value);
        }
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