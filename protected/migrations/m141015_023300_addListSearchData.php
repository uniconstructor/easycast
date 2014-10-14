<?php

class m141015_023300_addListSearchData extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{easy_lists}}";
        $this->addColumn($table, 'itemtype', "VARCHAR(50) NOT NULL DEFAULT 'EasyListItem'");
        $this->createIndex('idx_itemtype', $table, 'itemtype');
        $this->addColumn($table, 'searchdataid', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_searchdataid', $table, 'searchdataid');
        // по умолчанию требуем уникальности элементов в списке
        $this->alterColumn($table, 'unique', "tinyint(1) NOT NULL DEFAULT 1");
    }
}