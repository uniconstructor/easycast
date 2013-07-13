<?php

/**
 * Удалить тип лица, плкраску волос, и т. п.
 */
class m130416_231600_remofeFaceType extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
    
    public function safeUp()
    {
        Yii::import('application.modules.questionary.models.*');
        Yii::import('application.modules.questionary.models.complexValues.*');
        
        // удаляем уровни владения языком
        $oldLangLevels = QActivityType::model()->findAll("name = 'languagelevel' ");
        foreach ( $oldLangLevels as $oldLangLevel )
        {
            $oldLangLevel->delete();
        }

        // создаем новые уровни владения языком
        $langLevel = new QActivityType();
        $langLevel->name = 'languagelevel';
        $langLevel->translation = 'Базовый';
        $langLevel->value = 'base';
        $langLevel->language = 'ru';
        $langLevel->save();
        unset($langLevel);
        
        $langLevel = new QActivityType();
        $langLevel->name = 'languagelevel';
        $langLevel->value = 'speak';
        $langLevel->translation = 'Разговорный';
        $langLevel->language = 'ru';
        $langLevel->save();
        unset($langLevel);
        
        $langLevel = new QActivityType();
        $langLevel->name = 'languagelevel';
        $langLevel->value = 'fluent';
        $langLevel->translation = 'Свободный';
        $langLevel->language = 'ru';
        $langLevel->save();
        unset($langLevel);
        
        $langLevel = new QActivityType();
        $langLevel->name = 'languagelevel';
        $langLevel->value = 'perfect';
        $langLevel->translation = 'В совершенстве';
        $langLevel->language = 'ru';
        $langLevel->save();
        unset($langLevel);
        
        $langLevel = new QActivityType();
        $langLevel->name = 'languagelevel';
        $langLevel->value = 'native';
        $langLevel->translation = 'Родной язык';
        $langLevel->language = 'ru';
        $langLevel->save();
        unset($langLevel);
        
        $table = '{{questionaries}}';
        
        // обновляем старые уровни владения языком
        $oldBaseLevels = QActivity::model()->findAll(" type = 'languagelevel' AND level = 'amateur' ");
        foreach ( $oldBaseLevels as $oldBaseLevel )
        {
            $oldBaseLevel->level = 'speak';
            $oldBaseLevel->save();
        }
        
        $oldProfLevels = QActivity::model()->findAll(" type = 'languagelevel' AND level = 'professional' ");
        foreach ( $oldProfLevels as $oldProfLevel )
        {
            $oldProfLevel->level = 'fluent';
            $oldProfLevel->save();
        }
        
        // удаляем покраску волос
        $this->dropIndex('idx_iscoloredhair', $table);
        $this->dropColumn($table, 'iscoloredhair');
        
        // удаляем новый цвет волос
        $this->dropIndex('idx_newhaircolor', $table);
        $this->dropColumn($table, 'newhaircolor');
        
        // удаляем согласие на покраску волос
        $this->dropIndex('idx_canrepainthair', $table);
        $this->dropColumn($table, 'canrepainthair');
        
        // удаляем тип лица
        $this->dropIndex('idx_facetype', $table);
        $this->dropColumn($table, 'facetype');
        
        // добавляем поле "комментарий при модерации"
        $this->addColumn($table, 'moderationcomment', "varchar(255) DEFAULT NULL");
        
        // Исправляем тип поля "размер груди"
        $this->dropIndex('idx_titsize', $table);
        $this->alterColumn($table, 'titsize', "ENUM('AA', 'A', 'B', 'C', 'D', 'DD') DEFAULT NULL");
        $this->createIndex('idx_titsize', $table, 'titsize');
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