<?php
/**
 * Добавляет в базу поле "наличие татуировок" и удаляет устаревшее поле "уровень музыканта"
 * (уровень владения каждым музыкальным инструментом теперь устанавливается отдельно)
 */
class m121227_044444_addTatooField extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';

    private $_tableName = "{{questionaries}}";

    public function safeUp()
    {
        $this->dropIndex('idx_musicianlevel', $this->_tableName);
        $this->dropColumn($this->_tableName, 'musicianlevel');

        $this->addColumn($this->_tableName, 'hastatoo', "tinyint(1) NOT NULL DEFAULT 0");
        $this->createIndex('idx_hastatoo', $this->_tableName, 'hastatoo');
    }
}
