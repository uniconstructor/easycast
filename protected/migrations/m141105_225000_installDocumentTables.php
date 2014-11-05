<?php

class m141105_225000_installDocumentTables extends EcMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table   = "{{documents}}";
        $columns = array(
            'id'           => 'pk',
            'schemaid'     => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timecreated'  => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timemodified' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'status'       => "VARCHAR(50) DEFAULT NULL",
        );
        $this->createTable($table, $columns);
        $this->ecCreateIndexes($table, $columns);
        unset($table, $columns);
        
        
        $table   = "{{document_schemas}}";
        $columns = array(
            'id'           => 'pk',
            'title'        => "VARCHAR(255) DEFAULT NULL",
            'description'  => "VARCHAR(4095) DEFAULT NULL",
            'type'         => "VARCHAR(127) DEFAULT NULL",
            'freebasetype' => "VARCHAR(255) DEFAULT NULL",
            'timecreated'  => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timemodified' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
        );
        $this->createTable($table, $columns);
        $this->ecCreateIndexes($table, $columns);
        unset($table, $columns);
        
        
        
        $table = "{{extra_fields}}";
        $this->renameColumn($table, 'label', 'title');
        $this->addColumn($table, 'valueschemaid', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
        $this->createIndex('idx_valueschemaid', $table, 'valueschemaid');
        
        $this->addColumn($table, 'rules', 'VARCHAR(4095) DEFAULT NULL');
        
        $this->addColumn($table, 'freebaseproperty', 'VARCHAR(4095) DEFAULT NULL');
        $this->createIndex('idx_freebaseproperty', $table, 'freebaseproperty');
        
        $this->addColumn($table, 'optionslistid', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
        $this->createIndex('idx_optionslistid', $table, 'optionslistid');
        
        $this->addColumn($table, 'parentid', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
        $this->createIndex('idx_parentid', $table, 'parentid');
        unset($table);
        
        
        $table   = "{{document_data}}";
        $columns = array(
            'id'            => 'pk',
            'documentid'    => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'schemafieldid' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'value'         => "VARCHAR(4095) DEFAULT NULL",
            'freebaseitem'  => "VARCHAR(255) DEFAULT NULL",
            'timecreated'   => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timemodified'  => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
        );
        $this->createTable($table, $columns);
        $this->ecCreateIndexes($table, $columns);
        unset($table, $columns);
        
        
        $table   = "{{document_data_history}}";
        $columns = array(
            'id'            => 'pk',
            'documentid'    => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'schemafieldid' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'value'         => "VARCHAR(4095) DEFAULT NULL",
            'freebaseitem'  => "VARCHAR(255) DEFAULT NULL",
            'timecreated'   => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'version'       => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
        );
        $this->createTable($table, $columns);
        $this->ecCreateIndexes($table, $columns);
        unset($table, $columns);
        
        
        $formDefault = CJSON::encode(array('class' => 'EcFlexibleForm'));
        $table   = "{{flexible_forms}}";
        $columns = array(
            'id'                => 'pk',
            'title'             => "VARCHAR(255) DEFAULT NULL",
            'description'       => "VARCHAR(4095) DEFAULT NULL",
            'title'             => "VARCHAR(6) NOT NULL DEFAULT 'post'",
            'action'            => "VARCHAR(255) DEFAULT NULL",
            'activeformoptions' => "VARCHAR(4095) DEFAULT '{$formDefault}'",
            'displaytype'       => "VARCHAR(12) DEFAULT NULL",
            'clientvalidation'  => 'tinyint(1) UNSIGNED NOT NULL DEFAULT 0',
            'ajaxvalidation'    => 'tinyint(1) UNSIGNED NOT NULL DEFAULT 0',
            'timecreated'       => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timemodified'      => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
        );
        $this->createTable($table, $columns);
        $this->ecCreateIndexes($table, $columns, array('activeformoptions'));
        unset($table, $columns);
        
        
        $table   = "{{flexible_form_fields}}";
        $columns = array(
            'id'                => 'pk',
            'objecttype'        => "VARCHAR(64) DEFAULT NULL",
            'objectid'          => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'widget'            => "VARCHAR(255) NOT NULL  DEFAULT 'TbFormInputElement'",
            'name'              => "VARCHAR(255) NOT NULL",
            'label'             => "VARCHAR(255) DEFAULT NULL",
            'labeloptions'      => "VARCHAR(255) DEFAULT NULL",
            'hint'              => "VARCHAR(255) DEFAULT NULL",
            'hintoptions'       => "VARCHAR(255) DEFAULT NULL",
            'prepend'           => "VARCHAR(255) DEFAULT NULL",
            'prependoptions'    => "VARCHAR(255) DEFAULT NULL",
            'append'            => "VARCHAR(255) DEFAULT NULL",
            'appendoptions'     => "VARCHAR(255) DEFAULT NULL",
            'clientvalidation'  => 'tinyint(1) UNSIGNED NOT NULL DEFAULT 1',
            'ajaxvalidation'    => 'tinyint(1) UNSIGNED NOT NULL DEFAULT 1',
            'htmloptions'       => "VARCHAR(255) NOT NULL",
            'timecreated'       => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'timemodified'      => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
        );
        $this->createTable($table, $columns);
        $this->ecCreateIndexes($table, $columns, array('activeformoptions'));
        unset($table, $columns);
    }
}