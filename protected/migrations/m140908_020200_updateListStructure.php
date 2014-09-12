<?php

class m140908_020200_updateListStructure extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{easy_lists}}";
        $this->dropColumn($table, 'allowupdate');
        // убираем интервал автообновления для обновления для новых списков: 
        // непонятно сколько нужно времени для каждого так что он будет определен через настройку позже
        $this->alterColumn($table, 'updateperiod', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        // что вызывает обновления списка: по умолчанию создаем все списки как статические
        // (набор значений в них не дополняется и не уменьшается) 
        $this->alterColumn($table, 'updatemethod', "VARCHAR(20) NOT NULL DEFAULT 'never'");
        $this->dropIndex('idx_updatemethod', $table);
        $this->renameColumn($table, 'updatemethod', 'triggerupdate');
        $this->createIndex('idx_triggerupdate', $table, 'triggerupdate');
        // переименовываем поле хранящее время последнего обновления списка
        $this->dropIndex('idx_timeupdated', $table);
        $this->renameColumn($table, 'timeupdated', 'lastupdate');
        $this->createIndex('idx_lastupdate', $table, 'lastupdate');
        // время последнего запуска очистки
        $this->addColumn($table, 'lastcleanup', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
        $this->createIndex('idx_lastcleanup', $table, 'lastcleanup');
        // что вызывает очистку списка (по умолчанию не предусматриваем очистку списка)
        $this->addColumn($table, 'triggercleanup', "VARCHAR(20) NOT NULL DEFAULT 'never'");
        $this->createIndex('idx_triggercleanup', $table, 'triggercleanup');
        // интервал очистки списка от устаревших значений (если включена автоматическая очистка) 
        $this->addColumn($table, 'cleanupperiod', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_cleanupperiod', $table, 'cleanupperiod');
        unset($table);
        
        $table = "{{easy_list_instances}}";
        $this->addColumn($table, 'timemodified', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
        $this->createIndex('idx_timemodified', $table, 'timemodified');
        unset($table);
        
        $table = "{{easy_list_items}}";
        // обновляем все элементы всех списков, заменяем пустой статус на 'active'
        $this->update($table, array('status' => 'active'));
        // добавляем поля значения и описания для того чтобы списки стаи самодостаточными
        // это позволит больше не нуждаться в дополнительных таблицах или моделях с
        // сомнительными наборами полей
        $this->addColumn($table, 'value', "VARCHAR(255) DEFAULT NULL");
        $this->createIndex('idx_value', $table, 'value');
        $this->addColumn($table, 'description', "VARCHAR(4095) DEFAULT NULL");
        $this->createIndex('idx_description', $table, 'description');
        // колонка статуса должна содержать хоть что-то по умолчанию, даже если
        // у нас пока не введен workflow
        $this->dropIndex('idx_status', $table);
        $this->alterColumn($table, 'status', "VARCHAR(50) NOT NULL DEFAULT 'draft'");
        $this->createIndex('idx_status', $table, 'status');
        // поле в котором хранится значение элемента
        // (для случаев когда элемент списка является ссылкой на значение в другой модели)
        $this->addColumn($table, 'objectfield', "VARCHAR(50) DEFAULT NULL");
        $this->createIndex('idx_objectfield', $table, 'objectfield');
        unset($table);
    }
}