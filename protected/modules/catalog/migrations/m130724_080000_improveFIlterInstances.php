<?php

/**
 * Эта миграция позволяет привязывать фильтры не только к разделам каталога но и к вакансиям
 */
class m130724_080000_improveFIlterInstances extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{catalog_filter_instances}}";
        
        // добавляем поле "тип связи"
        $this->addColumn($table, 'linktype', "varchar(20) DEFAULT 'section'");
        $this->createIndex('idx_linktype', $table, 'linktype');
        
        $this->renameColumn($table, 'sectionid', 'linkid');
        $this->dropIndex('idx_sectionid', $table);
        $this->createIndex('idx_linkid', $table, 'linkid');
    }
}