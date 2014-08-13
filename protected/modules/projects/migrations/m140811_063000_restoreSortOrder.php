<?php

class m140811_063000_restoreSortOrder extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $vacancies = $this->dbConnection->createCommand()->select()->
            from("{{event_vacancies}}")->queryAll();
        foreach ( $vacancies as $vacancy )
        {
            $userFields = $this->dbConnection->createCommand()->select()->
                from("{{q_field_instances}}")->
                where("objecttype='vacancy' AND objectid={$vacancy['id']}")->queryAll();
            $extraFields = $this->dbConnection->createCommand()->select()->
                from("{{extra_field_instances}}")->
                where("objecttype='vacancy' AND objectid={$vacancy['id']}")->
                queryAll();
            $i = 1;
            foreach ( $userFields as $ufInstance )
            {
                $this->update("{{q_field_instances}}", array('sortorder' => $i), 'id='.$ufInstance['id']);
                $i++;
            }
            $i = 1;
            foreach ( $extraFields as $efInstance )
            {
                $this->update("{{q_field_instances}}", array('sortorder' => $i), 'id='.$efInstance['id']);
                $i++;
            }
        }
    }
}