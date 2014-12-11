<?php

class m141028_025700_installFilterTables extends EcMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table   = "{{search_filters}}";
        $columns = array(
            'id'           => 'pk',
            'searchdataid' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'name'         => "VARCHAR(255) DEFAULT NULL",
            'title'        => "VARCHAR(255) DEFAULT NULL",
            'description'  => "VARCHAR(4095) DEFAULT NULL",
            'parentid'     => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'targetmodel'  => "VARCHAR(128) DEFAULT NULL",
            'timecreated'  => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timemodified' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
        );
        $this->createTable($table, $columns);
        $this->ecCreateIndexes($table, $columns);
        
        $table   = "{{search_filter_fields}}";
        $columns = array(
            'id'            => 'pk',
            'filterid'      => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'name'          => "VARCHAR(255) NOT NULL",
            'title'         => "VARCHAR(255) DEFAULT NULL",
            'fieldtype'     => "VARCHAR(255) NOT NULL",
            'maxvalues'     => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            'defaultlistid' => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            'combine'       => "VARCHAR(20) NOT NULL DEFAULT 'OR'",
            'minvalue'      => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'maxvalue'      => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'stepvalue'     => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timecreated'   => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timemodified'  => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
        );
        $this->createTable($table, $columns);
        $this->ecCreateIndexes($table, $columns);
        
        $table   = "{{search_filter_values}}";
        $columns = array(
            'id'            => 'pk',
            'filterfieldid' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'title'         => "VARCHAR(255) DEFAULT NULL",
            'combine'       => "VARCHAR(20) NOT NULL DEFAULT 'AND'",
            'objecttype'    => "VARCHAR(128) NOT NULL",
            'objectfield'   => "VARCHAR(128) NOT NULL",
            'objectvalue'   => "VARCHAR(255) NOT NULL",
            'prefix'        => "VARCHAR(20) DEFAULT NULL",
            'operation'     => "VARCHAR(20) NOT NULL DEFAULT '='",
            'timecreated'   => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timemodified'  => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
        );
        $this->createTable($table, $columns);
        $this->ecCreateIndexes($table, $columns);
    }
}