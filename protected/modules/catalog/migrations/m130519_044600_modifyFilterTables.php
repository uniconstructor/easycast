<?php

class m130519_044600_modifyFilterTables extends CDbMigration
{
    protected $MySqlOptions = "ENGINE=InnoDB CHARSET=utf8";
    
    public function safeUp()
    {
        $table = "{{catalog_filters}}";
        
        // добавляем в таблицу название класса виджета, отвечающего за фильтр
        $this->addColumn($table, 'class', 'varchar(255) NOT NULL');
        $this->createIndex('idx_class', $table, 'class');
        
        // Убераем лишние поля из таблицы, связывающей фильтры поиска и разделы, т. к.
        // решено было не хранить данные поисковых фильтров в базе
        $table = '{{catalog_filter_instances}}';
        $this->dropColumn($table, 'type');
        $this->dropColumn($table, 'customvalues');
        $this->dropColumn($table, 'default');
    }
}