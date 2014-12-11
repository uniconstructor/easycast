<?php

class m141029_062500_installComments extends EcMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table   = "{{comments}}";
        $columns = array(
            'id'           => 'pk',
            'userid'       => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'subject'      => "VARCHAR(255) DEFAULT NULL",
            'message'      => "VARCHAR(4095) DEFAULT NULL",
            'objectid'     => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'objecttype'   => "VARCHAR(50) DEFAULT NULL",
            'timecreated'  => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timemodified' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
        );
        $this->createTable($table, $columns);
        $this->ecCreateIndexes($table, $columns, array('message'));
        
        // удаляем устаревшие системные настройки
        $this->delete('{{config}}', 'id<11');
    }
}