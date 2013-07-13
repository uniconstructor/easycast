<?php

class m130521_052200_addPrivateComment extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
    
    public function safeUp()
    {
        Yii::app()->getModule('questionary');
        $table = Yii::app()->getModule('questionary')->questionaryTable;
        
        $this->addColumn($table, 'privatecomment', 'varchar(4095) DEFAULT NULL');
    }
}