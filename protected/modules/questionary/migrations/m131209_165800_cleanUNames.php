<?php

class m131209_165800_cleanUNames extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        Yii::import('questionary.models.*');
        Yii::import('questionary.models.complexValues.*');
        
        $universities = QUniversity::model()->findAll();
        echo "\n";
        
        foreach ( $universities as $university )
        {
            $university->name = htmlspecialchars_decode($university->name);
            $university->save(false, array('name'));
            echo "Saving university {$university->id}\n";
        }
        
        $table = "{{q_universities}}";
        $this->alterColumn($table, 'name', "varchar(255) NOT NULL DEFAULT ''");
        $this->alterColumn($table, 'link', "varchar(255) NOT NULL DEFAULT ''");
        $this->alterColumn($table, 'type', "varchar(20) NOT NULL DEFAULT ''");
    }
}