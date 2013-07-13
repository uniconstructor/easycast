<?php

class m130622_102200_clearEmptyActivities extends CDbMigration
{
    public function safeUp()
    {
        Yii::app()->getModule('questionary');
        Yii::import('application.modules.questionary.models.*');
        Yii::import('application.modules.questionary.models.complexValues.*');
        
        // Характеристики
        $table = "{{q_activities}}";
        $condition = "`value` = 'custom' AND (`uservalue` = '' OR `uservalue` = ' ' OR `uservalue` IS NULL)";
        $activities = QActivity::model()->findAll($condition);
        
        foreach ( $activities as $activity )
        {
            $this->delete($table, 'id='.$activity->id);
        }
        
        // ссылки на битые ВУЗы
        $this->delete("{{q_university_instances}}", 'universityid=36 OR universityid=242');
        
        // ВУЗы
        $this->delete('{{q_universities}}', 'id=36 OR id=242');
        
        // ссылки на театры
        $this->delete("{{q_theatre_instances}}", 'theatreid=19');
        
        // театры
        $this->delete("{{q_theatres}}", 'id=19');
        
        // ссылки на фильмы
        $this->delete("{{q_film_instances}}", 'filmid=304');
        
        // фильмы
        $this->delete("{{q_films}}", 'id=304');
    }
}