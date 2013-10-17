<?php

/**
 * Добавляет возможность создавать "виртуальные" проекты и мероприятия: такие события и проекты
 * не происходят где-то физически, а присутствие участников требуется только онлайновое
 * Поле введено для того чтобы была возможность создавать онлайн-кастинги 
 * (они физически нигде не проходят, только на сайте)
 */
class m131017_141400_addVirtualProjects extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = '{{projects}}';
        $this->addColumn($table, 'virtual', 'tinyint(1) NOT NULL DEFAULT 0');
        $this->createIndex('idx_virtual', $table, 'virtual');
        
        $table = '{{project_events}}';
        $this->addColumn($table, 'virtual', 'tinyint(1) NOT NULL DEFAULT 0');
        $this->createIndex('idx_virtual', $table, 'virtual');
    }
}