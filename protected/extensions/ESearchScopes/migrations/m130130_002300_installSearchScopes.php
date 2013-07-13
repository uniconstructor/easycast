<?php

/**
 * @author Ilia Smirnov <php1602agregator@gmail.com>
 * This migration installs ESearchScopes module tables
 * @todo provide installation migrations for other database types (now supported mysql only)
 */
class m130130_002300_installSearchScopes extends CDbMigration
{
    protected $MySqlOptions = "ENGINE=InnoDB CHARSET=utf8";
    
    /**
     * @var string search scopes table name
     */
    protected $_scopesTable = "{{search_scopes}}";
    /**
     * @var string search scope condition table name
     */
    protected $_scopeConditionsTable = "{{scope_conditions}}";
    /**
     * @var string allowed comparsion types table name
     */
    protected $_comparisonTypesTable = "{{allowed_comparison_types}}";
    
    
    public function safeUp()
    {
        // creating the scopes table
        $fields = array(
            "id" => "pk",
            // id of the parent model - if this scope belongs to another model
            // or null - if this scope does not belong to any model
            "parentid" => "int(11) UNSIGNED DEFAULT NULL",
            // human-readable name of the scope (for users)
            "name" => "varchar(255) DEFAULT NULL",
            // short system name of the scope (may be useful if you have too much scopes)
            "shortname" => "varchar(64) DEFAULT NULL",
            // the model class (only class name, without path).
            // Can be null, if scope is used in different models. Use it carefully.
            "model" => "varchar(128) DEFAULT NULL",
            // standard timestamps for tracking create/modify scopes
            // can be used for cache updating 
            // updates every time when child instance or child scope is updated
            "timecreated" => "int(11) UNSIGNED DEFAULT NULL",
            "timemodified" => "int(11) UNSIGNED DEFAULT NULL"
        );
        
        $this->createTable($this->_scopesTable, $fields, $this->MySqlOptions);
        $this->_createIndexes($this->_scopesTable, $fields);
        unset($fields);
        
        
        // creating scope conditions table
        // (each scope can consist from some conditions or scopes)
        $fields = array(
            "id" => "pk",
            // scope-owner of this condition
            "scopeid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            // condition type: can be field condition, sort condition, or another existing scope
            "type" => "enum('field', 'scope', 'sort') NOT NULL",
            // field name (for "field" or "sort" conditions) or NULL (for "scope" conditions)
            "field" => "varchar(128) DEFAULT NULL",
            
            // field value (for "field" conditions)
            // sort direction (for "sort" condition(ASC/DESC))
            // JSON array (for field "in" condition)
            // or scope id (for "scope" condition)
            "value" => "varchar(255) DEFAULT NULL",
            
            // comparison type (for "field" condition)
            // or NULL (for "sort" and "scope" conditions)
            // "isempty" means: empty string or NULL or {}
            // "isset" means: NOT (empty string OR null OR 0 OR {})
            "comparison" => "enum('equals', 'startswith', 'endswith', 'contains', 'morethen', 'lessthen', 'in', 'isnull', 'isempty', 'isset') DEFAULT NULL",
            
            // how this condition will be combined with others?
            "combine" => "enum('and', 'or') NOT NULL",
            // shoud we inverse the condition? (usually adds "NOT(..." before condition)
            // 1 - inverse the condition
            // 0 - do not inverse the condition
            "inverse" => "tinyint(1) NOT NULL DEFAULT 0",
            
            // id of the previous condition (to maintain order)
            // @todo not implemented yet!
            "previousid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
        );
        
        $this->createTable($this->_scopeConditionsTable, $fields, $this->MySqlOptions);
        $this->_createIndexes($this->_scopeConditionsTable, $fields);
        unset($fields);
        
        // creating table for allowed comparsion types
        $fields = array(
            "id" => "pk",
            // the model class (only class name, without path).
            "model" => "varchar(128) NOT NULL",
            // model field name
            "field" => "varchar(128) NOT NULL",
            // human-readable field name (for users)
            "fieldlabel" => "varchar(255) DEFAULT NULL",
            // JSON array of comparsion types allowed for this field
            "types" => "varchar(255) DEFAULT NULL",
        );
        
        $this->createTable($this->_comparisonTypesTable, $fields, $this->MySqlOptions);
        $this->_createIndexes($this->_comparisonTypesTable, $fields);
        unset($fields);
    }
    
    /**
     * Create indexes for all fields in the table
     * @param string $table - the table name
     * @param array $fields - table fields 
     *                         example: array( "fieldname1" => "fieldtype1", "fieldname2" => "fieldtype2", ... )
     * @param array $excluded - not indexed fields
     *                           example: array("fieldname1", "fieldname2", "fieldname3", ...)
     * @param string $idxPrefix - index name prefix (default is "idx_")
     * 
     * @return null
     */
    protected function _createIndexes($table, $fields, $excluded=array(), $idxPrefix="idx_")
    {
        // gather all field names
        $fieldNames = array_keys($fields);
        
        // exclude not needed fields from index
        // ("id" is already primary key, so we never need to create additional index for it)
        $noIndex = CMap::mergeArray(array("id"), $excluded);
        
        $indexedFields = array_diff($fieldNames, $noIndex);
        
        foreach ( $indexedFields as $field )
        {
            $this->createIndex($idxPrefix.$field, $table, $field);
        }
    }
}