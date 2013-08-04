<?php 

/**
 * Эта миграция позволяет создавать мероприятия без определенной даты
 * (на случай если время съемок еще не известно)
 */
class m130803_220600_allowNoDateEvents extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
    
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = '{{project_events}}';
        
        $this->addColumn($table, 'nodates', "tinyint(1) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_nodates', $table, 'nodates');
        
        // создаем таблицу, в которой храниться информация о том какие приглашения право на какие вакансии дают
        /*$inviteVacanciesTable = '{{invite_vacancies}}';
        $fields = array(
            'id' => "pk",
            'inviteid' => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            'vacancyid' => "int(11) UNSIGNED NOT NULL DEFAULT 0",
        );
        $this->createTable($inviteVacanciesTable, $fields, $this->MySqlOptions);
        $this->createIndex('idx_inviteid', $table, 'inviteid');
        $this->createIndex('idx_vacancyid', $table, 'vacancyid');*/
    }
}