<?php

class m131025_031400_expandQComment extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        Yii::app()->getModule('questionary');
        $table = Yii::app()->getModule('questionary')->questionaryTable;
        
        $this->alterColumn($table, 'moderationcomment', "varchar(4095) NOT NULL DEFAULT ''");
        $this->alterColumn($table, 'admincomment', "varchar(4095) NOT NULL DEFAULT ''");
    }
}