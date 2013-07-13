<?php

/**
 * Эта миграфия устанавливает поля "Название фильтра" и "Порядок сортировки" в таблицу фильтров
 * Чтобы потом можно было легко добавить любой набор фильтров на любую страницу
 */
class m130624_200000_prepareFilterInstallation extends CDbMigration
{
    public function safeUp()
    {
        $table = "{{catalog_filters}}";
        $this->addColumn($table, 'name', 'VARCHAR(255) DEFAULT NULL');
        
        $table = "{{catalog_filter_instances}}";
        $this->addColumn($table, 'order', 'int(6) UNSIGNED NOT NULL DEFAULT 0');
    }
}