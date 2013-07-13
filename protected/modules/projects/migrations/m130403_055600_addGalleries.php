<?php

/**
 * Добавление фотогалереи к проекту и мероприятию
 */
class m130403_055600_addGalleries extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';

    /**
     * @var string - проекты
     */
    protected $_projectsTable = "{{projects}}";
    /**
     * @var string - мероприятия
     */
    protected $_eventsTable = "{{project_events}}";

    public function safeUp()
    {
        // Проект
        // фотогалерея
        $this->addColumn($this->_projectsTable, 'photogalleryid', "int(11) UNSIGNED DEFAULT 0");
        $this->createIndex('idx_photogalleryid', $this->_projectsTable, 'photogalleryid');

        // удлинняем описание
        $this->dropIndex('idx_description', $this->_projectsTable);
        $this->alterColumn($this->_projectsTable, 'description', 'VARCHAR(4095) DEFAULT NULL');
        $this->createIndex('idx_description', $this->_projectsTable, 'description');

        // описание для заказчика
        $this->addColumn($this->_projectsTable, 'customerdescription', 'VARCHAR(4095) DEFAULT NULL');
        $this->createIndex('idx_customerdescription', $this->_projectsTable, 'customerdescription');

        // краткое описание
        $this->addColumn($this->_projectsTable, 'shortdescription', 'VARCHAR(4095) DEFAULT NULL');
        $this->createIndex('idx_shortdescription', $this->_projectsTable, 'shortdescription');

        // Помошник
        $this->addColumn($this->_projectsTable, 'supportid', "int(11) UNSIGNED DEFAULT 0");
        $this->createIndex('idx_supportid', $this->_projectsTable, 'supportid');

        // Мероприятие
        // фотогалерея
        $this->addColumn($this->_eventsTable, 'photogalleryid', "int(11) UNSIGNED DEFAULT 0");
        $this->createIndex('idx_photogalleryid', $this->_eventsTable, 'photogalleryid');

        // удлинняем описание
        $this->dropIndex('idx_description', $this->_eventsTable);
        $this->alterColumn($this->_eventsTable, 'description', 'VARCHAR(4095) DEFAULT NULL');
        $this->createIndex('idx_description', $this->_eventsTable, 'description');

        //$this->addColumn($this->_projectsTable, 'photogalleryid', "int(11) UNSIGNED DEFAULT 0");
        // создаем индексы для id страны, id региона и id города
        // $this->createIndex('idx_countryid', $this->_projectsTable, 'countryid');
    }
}