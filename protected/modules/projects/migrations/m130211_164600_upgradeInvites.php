<?php

/**
 * Пустая миграция - добавлена по ошибке. Оставлена для совместимости
 */
class m130211_164600_upgradeInvites extends CDbMigration
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
        
    }
}