<?php

/**
 * Изменения после установки модуля выбора города и страны
 * Поля "страна" и "город" теперь ссылаются на id в соответствующих таблицах
 * @author frost
 *
 */
class m121130_124800_upgradeAddressGeoData extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
    private $_tableName = "{{addresses}}";
    
    public function safeUp()
    {
        // удаляем индексы полей "страна" и "регион"
        $this->dropIndex('idx_country', $this->_tableName);
        $this->dropIndex('idx_region', $this->_tableName);
        
        // удаляем поля страны и региона
        $this->dropColumn($this->_tableName,'country');
        $this->dropColumn($this->_tableName,'region');
        
        // добавляем поля id страны, id региона и id города
        $this->addColumn($this->_tableName, 'countryid', "int(11) UNSIGNED DEFAULT 0");
        $this->addColumn($this->_tableName, 'regionid', "int(11) UNSIGNED DEFAULT 0");
        $this->addColumn($this->_tableName, 'cityid', "int(11) UNSIGNED DEFAULT 0");
        
        // создаем индексы для id страны, id региона и id города
        $this->createIndex('idx_countryid', $this->_tableName, 'countryid');
        $this->createIndex('idx_regionid', $this->_tableName, 'regionid');
        $this->createIndex('idx_cityid', $this->_tableName, 'cityid');
    }
}