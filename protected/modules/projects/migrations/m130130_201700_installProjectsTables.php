<?php

/**
 * Миграция, устанавливающая таблицы проектов и мероприятий
 */
class m130130_201700_installProjectsTables extends CDbMigration
{
    /**
     * @var string - проекты
     */
    protected $_projectsTable = "{{projects}}";
    /**
     * @var string - мероприятия
     */
    protected $_eventsTable = "{{project_events}}";
    /**
     * @var string - вакансии для мероприятий
     */
    protected $_vacanciesTable = "{{event_vacancies}}";
    /**
     * @var string - участники и заявки на участие в проекте
     */
    protected $_membersTable = "{{project_members}}";
    /**
     * @var string - менеджеры проектов (чтобы каждый проект мог иметь несколько менеджеров)
     */
    protected $_managersTable = "{{project_managers}}";
    
    
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
    
    public function safeUp()
    {
        // Проекты
        $fields = array(
            "id" => "pk",
            "name" => "VARCHAR(255) NOT NULL",
            "type" => "enum('ad', 'film', 'series', 'tvshow', 'expo', 'promo', 'flashmob', 'videoclip') NOT NULL",
            "description" => "VARCHAR(1023) DEFAULT NULL",
            "galleryid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timestart" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timeend" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timecreated" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timemodified" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "leaderid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            // id заказчика проекта (0, если это чисто наш проект)
            "customerid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            // заказ, из которого сформирован проект (0, если создан вручную)
            "orderid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            // Является ли проект благотворительностью
            "isfree" => "tinyint(1) NOT NULL DEFAULT 0",
            // количество участников проекта (кеширование, для быстроты)
            "memberscount" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "status" => "enum('draft', 'active', 'suspended', 'rejected', 'finished') NOT NULL DEFAULT 'draft'",
            );
        $this->createTable($this->_projectsTable, $fields, $this->MySqlOptions);
        $this->_createIndexes($this->_projectsTable, $fields);
        unset($fields);
        
        // мероприятия
        $fields = array(
            "id" => "pk",
            "projectid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "name" => "VARCHAR(255) NOT NULL",
            "description" => "VARCHAR(255) DEFAULT NULL",
            "timestart" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timeend" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timecreated" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timemodified" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "addressid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "status" => "enum('draft', 'active', 'suspended', 'finished') NOT NULL DEFAULT 'draft'",
            );
        $this->createTable($this->_eventsTable, $fields, $this->MySqlOptions);
        $this->_createIndexes($this->_eventsTable, $fields);
        unset($fields);
        
        // вакансии
        $fields = array(
            "id" => "pk",
            "eventid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "name" => "VARCHAR(255) NOT NULL",
            "description" => "VARCHAR(255) DEFAULT NULL",
            "scopeid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "limit" => "int(6) UNSIGNED NOT NULL DEFAULT 0",
            "timecreated" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timemodified" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "status" => "enum('draft', 'active', 'suspended', 'finished') NOT NULL DEFAULT 'draft'",
        );
        $this->createTable($this->_vacanciesTable, $fields, $this->MySqlOptions);
        $this->_createIndexes($this->_vacanciesTable, $fields);
        unset($fields);
        
        // заявки и участники
        $fields = array(
            "id" => "pk",
            "memberid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "vacancyid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timecreated" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timemodified" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "managerid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "request" => "VARCHAR(255) DEFAULT NULL",
            "responce" => "VARCHAR(255) DEFAULT NULL",
            "timestart" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timeend" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "status" => "enum('draft', 'active', 'suspended', 'finished') NOT NULL DEFAULT 'draft'",
        );
        $this->createTable($this->_membersTable, $fields, $this->MySqlOptions);
        $this->_createIndexes($this->_membersTable, $fields);
        unset($fields);
        
        // менеджеры проектов
        $fields = array(
            "id" => "pk",
            "managerid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "projectid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timestart" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timeend" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "status" => "enum('active', 'suspended', 'finished') NOT NULL DEFAULT 'active'",
        );
        $this->createTable($this->_managersTable, $fields, $this->MySqlOptions);
        $this->_createIndexes($this->_managersTable, $fields);
        unset($fields);
        
    }
    /**
     * Create indexes for all fields in the table
     * @param string $table - the table name
     * @param array $fields - table fields
     *                         example: array( "fieldname1" => "fieldtype1", "fieldname2" => "fieldtype2", ... )
     * @param array $excluded - not indexed fields
     *                           example: array("fieldname1", "fieldname2", "fieldname3", ...)
     * @param string $idxPrefix - index name prefix (default is "idx_")
     *
     * @return null
     */
    protected function _createIndexes($table, $fields, $excluded=array(), $idxPrefix="idx_")
    {
        // gather all field names
        $fieldNames = array_keys($fields);
    
        // exclude not needed fields from index
        // ("id" is already primary key, so we never need to create additional index for it)
        $noIndex = CMap::mergeArray(array("id"), $excluded);
    
        $indexedFields = array_diff($fieldNames, $noIndex);
    
        foreach ( $indexedFields as $field )
        {
            $this->createIndex($idxPrefix.$field, $table, $field);
        }
    }
}