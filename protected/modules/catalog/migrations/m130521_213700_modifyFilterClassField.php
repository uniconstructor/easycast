<?php

/**
 * Сделать два поля widgetclass и handlerclass
 */
class m130521_213700_modifyFilterClassField extends CDbMigration
{
    protected $MySqlOptions = "ENGINE=InnoDB CHARSET=utf8";
    
    public function safeUp()
    {
        $table = "{{catalog_filters}}";
        $this->renameColumn($table, 'class', 'widgetclass');
        $this->addColumn($table, 'handlerclass', 'varchar(255) NOT NULL');
        
        // удаляем старые индексы
        $this->dropIndex('idx_name', $table);
        $this->dropIndex('idx_class', $table);
        
        // и добавляем новые
        $this->createIndex('idx_widgetclass', $table, 'widgetclass');
        $this->createIndex('idx_handlerclass', $table, 'handlerclass');
    }
}