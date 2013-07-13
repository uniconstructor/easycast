<?php
/**
 * Добавляет к университетам поле "system" - чтобы можно было отличить
 * Добавленные нами ВУЗы от добавленных пользователем
 */
class m121226_031500_addUniversityClassification extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';

    private $_tableName = "{{q_universities}}";

    public function safeUp()
    {
        // добавляем новые поля
        // Стандартное или добавленное пользователем значение
        // 1 - стандартное, 0 - пользовательское
        $this->addColumn($this->_tableName, 'system', "tinyint(1) NOT NULL DEFAULT 0");

        // создаем индексы
        $this->createIndex('idx_system', $this->_tableName, 'system');
    }
}
