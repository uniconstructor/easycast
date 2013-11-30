<?php

class m131128_144441_allowEndlessProjects extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = '{{projects}}';
        // возможность создавать проект без определенной даты начала 
        $this->addColumn($table, 'notimestart', 'tinyint(1) NOT NULL DEFAULT 0');
        $this->createIndex('idx_notimestart', $table, 'notimestart');
        // и "бесконечные проекты" - без даты окончания 
        $this->addColumn($table, 'notimeend', 'tinyint(1) NOT NULL DEFAULT 0');
        $this->createIndex('idx_notimeend', $table, 'notimeend');
        // убираем null-значения во имя соблюдения стандарта кодирования
        $this->alterColumn($table, 'photogalleryid', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
        $this->alterColumn($table, 'description', "varchar(4095) NOT NULL DEFAULT ''");
        $this->alterColumn($table, 'customerdescription', "varchar(4095) NOT NULL DEFAULT ''");
        $this->alterColumn($table, 'shortdescription', "varchar(4095) NOT NULL DEFAULT ''");
        
        // убираем null-значения во имя соблюдения стандарта кодирования
        $table = '{{project_events}}';
        $this->alterColumn($table, 'name', "varchar(255) NOT NULL DEFAULT ''");
        $this->alterColumn($table, 'description', "varchar(4095) NOT NULL DEFAULT ''");
        $this->alterColumn($table, 'photogalleryid', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
        $this->alterColumn($table, 'memberinfo', "varchar(4095) NOT NULL DEFAULT ''");
        $this->alterColumn($table, 'meetingplace', "varchar(4095) NOT NULL DEFAULT ''");
        $this->alterColumn($table, 'memberinfo', "varchar(4095) NOT NULL DEFAULT ''");
        // убираем "размер оплаты" из мероприятия (давно пора)
        $this->dropColumn($table, 'salary');
        
        // убираем null-значения во имя соблюдения стандарта кодирования
        $table = '{{project_members}}';
        $this->alterColumn($table, 'request', "varchar(4095) NOT NULL DEFAULT ''");
        $this->alterColumn($table, 'responce', "varchar(4095) NOT NULL DEFAULT ''");
        $this->alterColumn($table, 'status', "varchar(50) NOT NULL DEFAULT 'draft'");
    }
}