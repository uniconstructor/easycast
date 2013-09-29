<?php

class m130929_030000_addOwnerId extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        Yii::app()->getModule('questionary');
        $table = Yii::app()->getModule('questionary')->questionaryTable;
        
        $this->addColumn($table, 'ownerid', 'int(11) NOT NULL DEFAULT 1');
        $this->createIndex('idx_ownerid', $table, 'ownerid');
    }
}