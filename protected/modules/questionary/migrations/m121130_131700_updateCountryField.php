<?php

/**
 * Изменяем поле country на countryid
 * @author frost
 *
 */
class m121130_131700_updateCountryField extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';

    public function safeUp()
    {
        if ( ! Yii::app()->getModule('questionary') )
        {
            echo "\n\nAdd to console.php :\n"
            ."'modules'=>array(\n"
            ."...\n"
            ."    'questionary'=>array(\n"
            ."        ... # copy settings from main config\n"
            ."    ),\n"
            ."...\n"
            ."),\n"
            ."\n";
            return false;
        }
        
        $table = Yii::app()->getModule('questionary')->questionaryTable;
        
        // удаляем индекс поля "гражданство"
        $this->dropIndex('idx_country', $table);

        // удаляем поле "гражданство"
        $this->dropColumn($table, 'country');

        // добавляем поле id страны гражданства
        $this->addColumn($table, 'countryid', "int(11) UNSIGNED DEFAULT 0");

        // создаем индекс для id страны гражданства
        $this->createIndex('idx_countryid', $table, 'countryid');
    }
}