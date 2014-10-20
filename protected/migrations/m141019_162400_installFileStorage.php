<?php

class m141019_162400_installFileStorage extends EcMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table   = "{{external_files}}";
        $columns = array(
            'id'           => 'pk',
            'originalid'   => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'previousid'   => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'name'         => "VARCHAR(255) NOT NULL",
            'title'        => "VARCHAR(255) DEFAULT NULL",
            'description'  => "VARCHAR(4095) DEFAULT NULL",
            'oldname'      => "VARCHAR(255) DEFAULT NULL",
            'newname'      => "VARCHAR(255) DEFAULT NULL",
            'storage'      => "VARCHAR(10) NOT NULL DEFAULT 's3'",
            'timecreated'  => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timemodified' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'lastupload'   => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'lastupsync'   => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'bucket'       => "VARCHAR(128) NOT NULL DEFAULT 'img.easycast.ru'",
            'path'         => "VARCHAR(255) NOT NULL DEFAULT '_tmp'",
            'mimetype'     => "VARCHAR(255) NOT NULL DEFAULT 'text/plain'",
            'size'         => 'bigint(21) UNSIGNED NOT NULL DEFAULT 0',
            'md5'          => "VARCHAR(128) DEFAULT NULL",
            'updateaction' => "VARCHAR(10) NOT NULL DEFAULT 'update'",
            'deleteaction' => "VARCHAR(10) NOT NULL DEFAULT 'erase'",
            'deleteafter'  => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'status'       => "VARCHAR(50) NOT NULL DEFAULT 'swExternalFile/draft'",
        );
        $this->createTable($table, $columns);
        $this->ecCreateIndexes($table, $columns);
    }
}