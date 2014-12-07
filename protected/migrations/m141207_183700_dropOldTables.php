<?php

class m141207_183700_dropOldTables extends EcMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $this->dropTable('{{post}}');
        $this->dropTable('{{forum}}');
        $this->dropTable('{{forumuser}}');
        $this->dropTable('{{thread}}');
    }
}