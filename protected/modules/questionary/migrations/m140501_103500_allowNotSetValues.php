<?php

class m140501_103500_allowNotSetValues extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = Yii::app()->getModule('questionary')->questionaryTable;
        
        $this->alterColumn($table, 'status', "varchar(50) NOT NULL DEFAULT 'draft'");
        $this->alterColumn($table, 'hastatoo', "tinyint(1) UNSIGNED DEFAULT NULL");
        $this->alterColumn($table, 'cityid', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->alterColumn($table, 'admincomment', "varchar(4095) DEFAULT NULL");
        $this->alterColumn($table, 'moderationcomment', "varchar(4095) DEFAULT NULL");
    }
}