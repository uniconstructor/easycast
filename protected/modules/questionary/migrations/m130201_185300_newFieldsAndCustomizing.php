<?php

/**
 * Надеюсь, что последнее изменение структуры данных анкеты
 * Добавляет новые поля: телеведущий, непрофессиональный актер, статист, актер массовых сцен
 * Убирает все почти все enum-поля, чтобы администраторы могли вводить любые 
 * значения по умолчанию для любых полей формы
 * 
 * Минимизирует количество индексов в таблице, чтобы уместиться в ограничение 64 индекса
 */
class m130201_185300_newFieldsAndCustomizing extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
    
    public function safeUp()
    {
        $table = Yii::app()->getModule('questionary')->questionaryTable;
        
        // удаляем поле "время заполнения анкеты" (не пригодилось, используем timemodified)
        // Освобождаем 1 индекс (их максимум 64 в таблице MYSQL, их надо экономить)
        $this->dropIndex('idx_timefilled', $table);
        $this->dropColumn($table, 'timefilled');
        // Удаляем "согласие с политикой сайта" (будет перенесено в модель User)
        $this->dropIndex('idx_policyagreed', $table);
        $this->dropColumn($table, 'policyagreed');
        // удаляем "оплату за день" (перемещено в условия участия)
        $this->dropIndex('idx_salary', $table);
        $this->dropColumn($table, 'salary');
        // удаляем "согласие на командировки" (перемещено в условия участия)
        $this->dropIndex('idx_wantsbusinesstrips', $table);
        $this->dropColumn($table, 'wantsbusinesstrips');
        // удаляем "есть загранпаспорт" (перемещено в условия участия)
        $this->dropIndex('idx_hasforeignpassport', $table);
        $this->dropColumn($table, 'hasforeignpassport');
        // удаляем "срок истечения загранпаспорта" (перемещено в условия участия)
        $this->dropIndex('idx_passportexpires', $table);
        $this->dropColumn($table, 'passportexpires');
        // удаляем "данные зашифрованы" (переедет в таблицу персональных данных)
        $this->dropIndex('idx_encrypted', $table);
        $this->dropColumn($table, 'encrypted');
        
        
        
        // удаляем индексы по полям "стриптиз" (тип и уровень) - они будут заменены составными
        $this->dropIndex('idx_isstripper', $table);
        $this->dropIndex('idx_striptype', $table);
        $this->dropIndex('idx_striplevel', $table);
        // удаляем индексы по полю "вокал" (заменяем составным)
        $this->dropIndex('idx_issinger', $table);
        $this->dropIndex('idx_singlevel', $table);
        
        
        
        // добавляем новые поля
        // непрофессиональный актер
        $this->addColumn($table, 'isamateuractor', "tinyint(1) DEFAULT NULL");
        // телеведущий
        $this->addColumn($table, 'istvshowmen', "tinyint(1) DEFAULT NULL");
        // условия участия
        $this->addColumn($table, 'conditionsid', "int(11) UNSIGNED DEFAULT NULL");
        // статист
        $this->addColumn($table, 'isstatist', "tinyint(1) DEFAULT NULL");
        // актер массовых сцен
        $this->addColumn($table, 'ismassactor', "tinyint(1) DEFAULT NULL");
        // страна рождения
        $this->addColumn($table, 'nativecountryid', "int(11) UNSIGNED DEFAULT NULL");
        
        
        
        // изменяем старые поля так, чтобы их значения по умолчанию могли быть любыми
        // тип внешности
        $this->dropIndex('idx_looktype', $table);
        $this->alterColumn($table, 'looktype', 'varchar(32) DEFAULT NULL');
        // цвет волос
        $this->dropIndex('idx_haircolor', $table);
        $this->alterColumn($table, 'haircolor', 'varchar(32) DEFAULT NULL');
        // цвет глаз
        $this->dropIndex('idx_eyecolor', $table);
        $this->alterColumn($table, 'eyecolor', 'varchar(32) DEFAULT NULL');
        // телосложение
        $this->dropIndex('idx_physiquetype', $table);
        $this->alterColumn($table, 'physiquetype', 'varchar(32) DEFAULT NULL');
        
        
        // создаем новые индексы
        // составной индекс по полю "стриптиз"
        $this->createIndex('idx_striptease', $table, 'isstripper, striptype, striplevel');
        // Составной индекс по полю "вокал"
        $this->createIndex('idx_singer', $table, 'issinger, singlevel');
        
        // все остальные
        $this->createIndex('idx_isamateuractor', $table, 'isamateuractor');
        $this->createIndex('idx_istvshowmen', $table, 'istvshowmen');
        $this->createIndex('idx_conditionsid', $table, 'conditionsid');
        $this->createIndex('idx_isstatist', $table, 'isstatist');
        $this->createIndex('idx_ismassactor', $table, 'ismassactor');
        $this->createIndex('idx_nativecountryid', $table, 'nativecountryid');
        $this->createIndex('idx_looktype', $table, 'looktype');
        $this->createIndex('idx_haircolor', $table, 'haircolor');
        $this->createIndex('idx_eyecolor', $table, 'eyecolor');
        $this->createIndex('idx_physiquetype', $table, 'physiquetype');
        
        
        // создаем новую таблицу "условия участия"
        $table = "{{q_recording_conditions}}";
        $fields = array(
            'id' => 'pk',
            'questionaryid' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'salary' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'wantsbusinesstrips' => 'tinyint(1) DEFAULT NULL',
            'hasforeignpassport' => 'tinyint(1) DEFAULT NULL',
            'passportexpires' => 'int(11) UNSIGNED DEFAULT NULL',
            'isnightrecording' => 'tinyint(1) DEFAULT NULL',
            'istoplessrecording' => 'tinyint(1) DEFAULT NULL',
            'isfreerecording' => 'tinyint(1) DEFAULT NULL',
            'custom' => 'VARCHAR(255) DEFAULT NULL',
            );
        
        $this->createTable($table, $fields, $this->MySqlOptions);
        $this->_createIndexes($table, $fields);
        unset($fields);
        
        // создаем новую таблицу "телепроекты ведущего"
        $table = "{{q_tvshow_instances}}";
        $fields = array(
            'id' => 'pk',
            'questionaryid' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'channelname' => 'VARCHAR(255) DEFAULT NULL',
            'projectname' => 'VARCHAR(255) DEFAULT NULL',
            'timestart' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timeend' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timecreated' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
        );
        
        $this->createTable($table, $fields, $this->MySqlOptions);
        $this->_createIndexes($table, $fields);
        unset($fields);
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