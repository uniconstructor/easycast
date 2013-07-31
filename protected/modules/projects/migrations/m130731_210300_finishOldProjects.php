<?php

/**
 * Завершает все ранее созданные проекты мероприятия и вакансии, 
 * чтобы потом никому случайно не отправились приглашения
 */
class m130731_210300_finishOldProjects extends CDbMigration
{
    public function safeUp()
    {
        $table = '{{event_vacancies}}';
        $this->update($table, array('status' => 'finished'));
        
        $table = '{{project_events}}';
        $this->update($table, array('status' => 'finished'));
        
        $table = '{{projects}}';
        $this->update($table, array('status' => 'finished'));
        
        $table = '{{project_members}}';
        $this->update($table, array('status' => 'finished'));
    }
}