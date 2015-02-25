<?php

/**
 * 
 */
class m150225_010900_installRdf extends EcMigration
{
    /**
     * 
     */
    public function safeUp()
    {
        $prefix = 'rds_';
        $tables = array(
            'entities' => array(
                'columns' => array(
                    'id'    => 'pk',
                    'name'  => 'string',
                    'title' => 'string',
                ),
            ),
            'entity_fields' => array(
                'columns' => array(
                    'id'        => 'pk',
                    'entityid'  => 'integer',
                    'fieldid'   => 'integer',
                    'typeid'    => 'integer',
                    'sortorder' => 'integer',
                ),
            ),
            'fields' => array(
                'columns' => array(
                    'id'    => 'pk',
                    'name'  => 'string',
                    'title' => 'string',
                ),
            ),
            'entity_field_types' => array(
                'columns' => array(
                    'id'    => 'pk',
                    'name'  => 'integer',
                    'title' => 'integer',
                    'model' => 'string',
                ),
            ),
            'field_value' => array(
                'columns' => array(
                    'id'        => 'pk',
                    'entityid'  => 'integer',
                    'fieldid'   => 'integer',
                    'typeid'    => 'integer',
                    'valueid'   => 'integer',
                    'sortorder' => 'integer',
                ),
            ),
            'value_int' => array(
                'columns' => array(
                    'id'           => 'pk',
                    'value'        => 'integer',
                    'timecreated'  => 'integer',
                    'timemodified' => 'integer',
                ),
            ),
            'value_string' => array(
                'columns' => array(
                    'id'           => 'pk',
                    'value'        => 'string',
                    'timecreated'  => 'integer',
                    'timemodified' => 'integer',
                ),
            ),
            'value_text' => array(
                'columns' => array(
                    'id'           => 'pk',
                    'value'        => 'text',
                    'timecreated'  => 'integer',
                    'timemodified' => 'integer',
                ),
                'no_index' => array('value'),
            ),
            'value_boolean' => array(
                'columns' => array(
                    'id'           => 'pk',
                    'value'        => 'boolean',
                    'timecreated'  => 'integer',
                    'timemodified' => 'integer',
                ),
            ),
            'value_float' => array(
                'columns' => array(
                    'id'           => 'pk',
                    'value'        => 'float',
                    'timecreated'  => 'integer',
                    'timemodified' => 'integer',
                ),
            ),
        );
        foreach ( $tables as $tableName => $tableData )
        {
            $noIndex = array();
            $table   = '{{'.$prefix.$tableName.'}}';
            $columns = $tableData['columns'];
            if ( isset($tableData['no_index']) )
            {
                $noIndex = $tableData['no_index'];
            }
            $this->createOneTable($table, $columns, $noIndex);
        }
    }
    
    /**
     * 
     * @param type $table
     * @param type $columns
     * @param type $noIndex
     */
    protected function createOneTable($table, $columns, $noIndex=array())
    {
        $this->createTable($table, $columns);
        $this->ecCreateIndexes($table, $columns, $noIndex);
    }
}
