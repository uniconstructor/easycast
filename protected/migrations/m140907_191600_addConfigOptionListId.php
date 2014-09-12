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
        // возможность ссылаться на поля других моделей в качестве значений
        $this->addColumn($table, 'valuetype', "VARCHAR(50) NOT NULL");
        $this->createIndex('idx_valuetype', $table, 'valuetype');
        $this->addColumn($table, 'valuefield', "VARCHAR(50) NOT NULL");
        $this->createIndex('idx_valuefield', $table, 'valuefield');
        $this->addColumn($table, 'valueid', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_valueid', $table, 'valueid');
        // список где хранятся добавленные участником значения 
        // (если разрешено вводить свои значения настройки помимо стандартных)
        // (не путать в выбранными значениями) 
        // по умолчанию списки дополнять запрещено
        $this->addColumn($table, 'userlistid', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_userlistid', $table, 'userlistid');
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
        
        $configItems = $this->dbConnection->createCommand()->select()->
            from('{{config}}')->queryAll();
        foreach ( $configItems as $configItem )
        {
            $this->update('{{config}}', array('objecttype' => 'system'), 'id='.$configItem['id']);
        }
        
        // удаляем поле "тип объекта" из таблицы значений для настроек
        // (теперь значения привязываются только к модели настройки) 
        $this->dropColumn('{{config_values}}', 'objecttype');
        
        // удаляем таблицу с экземплярами настроек (после оптимизации архитектуры не нужна)
        $this->dropTable('{{config_instances}}');
        
        // удаляем таблицу значений настроек (после второй оптимизации архитектуры тоже стала не нужна)
        $this->dropTable('{{config_values}}');
    }
}