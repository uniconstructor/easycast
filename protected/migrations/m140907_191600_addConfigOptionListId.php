<?php

class m140907_191600_addConfigOptionListId extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{config}}";
        // список стандартных значений настройки (если настройка - выпадающий список)
        $this->addColumn($table, 'easylistid', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_easylistid', $table, 'easylistid');
        // чтобы ссылаться на системные настройки или настройки уровнем выше
        $this->addColumn($table, 'parentid', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_parentid', $table, 'parentid');
        // привязка настройки к любым другим объектам
        $this->addColumn($table, 'objecttype', "VARCHAR(50) NOT NULL");
        $this->createIndex('idx_objecttype', $table, 'objecttype');
        $this->addColumn($table, 'objectid', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_objectid', $table, 'objectid');
        unset($table);
        
        $table = "{{config_values}}";
        // оставляем привязку значений только к настройкам
        $this->dropIndex('idx_objectid', $table);
        $this->renameColumn($table, 'objectid', 'configid');
        $this->createIndex('idx_configid', $table, 'configid');
        // удаляем неиспользуемое поле "стандартное/введенное значение настройки"
        $this->dropColumn($table, 'default');
        unset($table);
        
        // обновляем кэш чтобы в рамках этой же миграции перенести данные из старых таблиц в новые
        $this->refreshTableSchema('{{config}}');
        $this->refreshTableSchema('{{config_values}}');
        
        
        // получаем все значения настроек
        $configValues = $this->dbConnection->createCommand()->select()->
            from('{{config_values}}')->queryAll();
        foreach ( $configValues as $configValue )
        {
            if ( $configValue['objecttype'] != 'config' )
            {// если они привязаны к чему-то кроме настроек - удалим их
                $this->delete('{{config_values}}', 'id='.$configValue['id']);
            }
        }
        
        // удаляем поле "тип объекта" из таблицы значений для настроек
        // (теперь значения привязываются только к модели настройки) 
        $this->dropColumn('{{config_values}}', 'objecttype');
        
        // удаляем таблицу с экземплярами настроек (после оптимизации архитектуры не нужна)
        $this->dropTable('{{config_instances}}');
    }
}