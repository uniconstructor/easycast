<?php

class m130910_142100_updateVacancyDescription extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = '{{event_vacancies}}';
        
        $this->alterColumn($table, 'description', "varchar(4095) DEFAULT ''");
    }
}