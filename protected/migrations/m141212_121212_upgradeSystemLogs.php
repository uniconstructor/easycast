<?php

class m141212_121212_upgradeSystemLogs extends EcMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = '{{system_logs}}';
        $this->dropTable($table);
        
        $columns = array(
            'id'         => 'pk',
            'level'      => "VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'info'",
            'category'   => "VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'system'",
            'title'      => "VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL",
            'logtime'    => 'BIGINT(21) NOT NULL DEFAULT 0',
            'message'    => "TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL",
            'userid'     => 'BIGINT(21) NOT NULL DEFAULT 0',
            'referer'    => "VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL",
            'module'     => "VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL",
            'controller' => "VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL",
            'action'     => "VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL",
            'userip'     => "varchar(16) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL",
            'sourcetype' => "varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'system'",
            'sourceid'   => 'BIGINT(21) NOT NULL DEFAULT 0',
            'targettype' => "varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'system'",
            'targetid'   => 'BIGINT(21) NOT NULL DEFAULT 0',
        );
        $this->createTable($table, $columns);
        $this->ecCreateIndexes($table, $columns, array('message'));
    }
}