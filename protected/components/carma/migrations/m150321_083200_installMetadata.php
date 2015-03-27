<?php

/**
 * @todo наследовать от оригинального класса миграции
 */
class m150321_083200_installMetadata extends EcMigration
{
    /**
     * 
     */
    public function safeUp()
    {
        $prefix = 'ar_';
        $tables = array(
            'models' => array(
                'columns' => array(
                    'id'           => 'pk',
                    'model'        => 'string',
                    'table'        => 'string',
                    'title'        => 'string',
                    'description'  => 'string',
                    'timecreated'  => 'integer',
                    'timemodified' => 'integer',
                ),
            ),
            'relations' => array(
                'columns' => array(
                    'id'            => 'pk',
                    'modelid'       => 'integer',
                    'name'          => 'string',
                    'type'          => 'string',
                    'fkdata'        => 'string',
                    'relatedmodel'  => 'string',
                    'title'         => 'string',
                    'description'   => 'string',
                    'timecreated'   => 'integer',
                    'timemodified'  => 'integer',
                ),
            ),
            'rules' => array(
                'columns' => array(
                    'id'           => 'pk',
                    'modelid'      => 'integer',
                    'attributes'   => 'string',
                    'validator'    => 'string',
                    'on'           => 'string',
                    'configdataid' => 'integer',
                    'title'        => 'string',
                    'description'  => 'string',
                    'timecreated'  => 'integer',
                    'timemodified' => 'integer',
                ),
            ),
            'templates' => array(
                'columns' => array(
                    'id'           => 'pk',
                    'name'         => 'string',
                    'content'      => 'text',
                    'title'        => 'string',
                    'description'  => 'text',
                    'timecreated'  => 'integer',
                    'timemodified' => 'integer',
                ),
                'no_index' => array('content', 'description'),
            ),
            'widgets' => array(
                'columns' => array(
                    'id'           => 'pk',
                    'name'         => 'string',
                    'templateid'   => 'integer',
                    'configdataid' => 'integer',
                    'title'        => 'string',
                    'description'  => 'text',
                    'timecreated'  => 'integer',
                    'timemodified' => 'integer',
                ),
                'no_index' => array('description'),
            ),
            'pointers' => array(
                'columns' => array(
                    'id'           => 'pk',
                    'modelid'      => 'string',
                    'attribute'    => 'string',
                    'recordid'     => 'integer',
                    'description'  => 'string',
                    'timecreated'  => 'integer',
                    'timemodified' => 'integer',
                ),
            ),
            'attributes' => array(
                'columns' => array(
                    'id'           => 'pk',
                    'parentid'     => 'integer',
                    'name'         => 'string',
                    'title'        => 'string',
                    'description'  => 'string',
                    'timecreated'  => 'integer',
                    'timemodified' => 'integer',
                ),
            ),
            'meta_links' => array(
                'columns' => array(
                    'id'              => 'pk',
                    'name'            => 'string',
                    'title'           => 'string',
                    'sourcepointerid' => 'integer',
                    'targetpointerid' => 'integer',
                    'timecreated'     => 'integer',
                    'timemodified'    => 'integer',
                ),
            ),
            'value_json' => array(
                'columns' => array(
                    'id'           => 'pk',
                    'data'         => 'text',
                    'timecreated'  => 'integer',
                    'timemodified' => 'integer',
                ),
                'no_index' => array('data'),
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
            'forms' => array(
                'columns' => array(
                    'id'                => 'pk',
                    'name'              => 'string',
                    'method'            => 'string',
                    'action'            => 'string',
                    'activeformoptions' => 'string',
                    'displaytype'       => 'string',
                    'clientvalidation'  => 'boolean',
                    'ajaxvalidation'    => 'boolean',
                    'title'             => 'string',
                    'description'       => 'text',
                    'timecreated'       => 'integer',
                    'timemodified'      => 'integer',
                ),
                'no_index' => array('description'),
            ),
            'form_fields' => array(
                'columns' => array(
                    'id'               => 'pk',
                    'formid'           => 'integer',
                    'name'             => 'string',
                    'label'            => 'string',
                    'labeloptionsid'   => 'integer',
                    'description'      => 'text',
                    'hint'             => 'text',
                    'hintoptionsid'    => 'integer',
                    'prepend'          => 'string',
                    'prependoptionsid' => 'integer',
                    'append'           => 'string',
                    'appendoptionsid'  => 'integer',
                    'clientvalidation' => 'boolean',
                    'ajaxvalidation'   => 'boolean',
                    'htmloptionsid'    => 'integer',
                    'timecreated'      => 'integer',
                    'timemodified'     => 'integer',
                    'sortorder'        => 'integer',
                ),
                'no_index' => array('description', 'hint'),
            ),
            'events' => array(
                'columns' => array(
                    'id'            => 'pk',
                    'name'          => 'string',
                    'eventclass'    => 'string',
                    'title'         => 'string',
                    'description'   => 'text',
                    'enabled'       => 'boolean',
                    'timecreated'   => 'integer',
                    'timemodified'  => 'integer',
                ),
                'no_index' => array('description'),
            ),
            'events_listeners' => array(
                'columns' => array(
                    'id'                => 'pk',
                    'eventid'           => 'integer',
                    'modelid'           => 'integer',
                    'launcherid'        => 'integer',
                    'actionchainid'     => 'integer',
                    'sleepconditionid'  => 'integer',
                    'muteconditionid'   => 'integer',
                    'enabled'           => 'boolean',
                    'timecreated'       => 'integer',
                    'timemodified'      => 'integer',
                ),
            ),
            'event_launchers' => array(
                'columns' => array(
                    'id'                 => 'pk',
                    'eventid'            => 'integer',
                    'modelid'            => 'integer',
                    'silenceconditionid' => 'integer',
                    'enabled'            => 'boolean',
                    'timecreated'        => 'integer',
                    'timemodified'       => 'integer',
                ),
            ),
        );
        $this->createTableList($tables, $prefix);
    }
}

