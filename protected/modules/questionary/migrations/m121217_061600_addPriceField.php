<?php

class m121217_061600_addPriceField extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';

    private $_tableName = "{{questionaries}}";

    public function safeUp()
    {
        // удаляем дублирующийся индекс первичного ключа (он был создан по ошибке)
        $this->dropIndex('idx_id', $this->_tableName);
        // удаляем старый индекс для поля "новый цвет волос" (его нужно пересоздать из-за изменения типа поля)
        $this->dropIndex('idx_newhaircolor', $this->_tableName);

        // добавляем новые поля:
        // Стоимость участия в день (видна только администраторам)
        $this->addColumn($this->_tableName, 'salary', "int(11) UNSIGNED DEFAULT NULL");
        // Профиль facebook
        $this->addColumn($this->_tableName, 'fbprofile', "varchar(128) DEFAULT NULL");
        // Профиль в одноклассниках
        $this->addColumn($this->_tableName, 'okprofile', "varchar(128) DEFAULT NULL");

        // Меняем тип поля "новый цвет волос" с enum на text
        $this->alterColumn($this->_tableName, 'newhaircolor', "varchar(20) DEFAULT NULL");

        // создаем индексы
        $this->createIndex('idx_salary', $this->_tableName, 'salary');
        $this->createIndex('idx_newhaircolor', $this->_tableName, 'newhaircolor');
    }
}