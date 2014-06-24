<?php

class m140624_175900_increaseInstanceComment extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = '{{member_instances}}';
        $this->alterColumn($table, 'comment', 'VARCHAR(4095) DEFAULT NULL');
    }
}