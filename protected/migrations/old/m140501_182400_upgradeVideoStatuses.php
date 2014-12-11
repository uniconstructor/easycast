<?php

class m140501_182400_upgradeVideoStatuses extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{video}}";
        
        $this->update($table, array('status' => 'swVideo/pending'), "`status` = 'pending'");
    }
}