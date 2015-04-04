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
                    'id'            => 'bigpk',
                    'model'         => 'string',
                    'table'         => 'string',
                    'title'         => 'string',
                    'description'   => 'string',
                    'system'        => 'boolean',
                    'defaultformid' => 'bigint',
                    'timecreated'   => 'bigint',
                    'timemodified'  => 'bigint',
                ),
            ),
            'relations' => array(
                'columns' => array(
                    'id'            => 'bigpk',
                    'modelid'       => 'bigint',
                    'name'          => 'string',
                    'type'          => 'string',
                    'fkdata'        => 'string',
                    'condition'     => 'string',
                    'relatedmodel'  => 'string',
                    'title'         => 'string',
                    'description'   => 'string',
                    'timecreated'   => 'bigint',
                    'timemodified'  => 'bigint',
                ),
            ),
            'rules' => array(
                'columns' => array(
                    'id'                     => 'bigpk',
                    'modelid'                => 'bigint',
                    'validatedattributes'    => 'string',
                    'validator'              => 'string',
                    'on'                     => 'string',
                    'except'                 => 'string',
                    'message'                => 'string',
                    'skiponerror'            => 'boolean',
                    'enableclientvalidation' => 'boolean',
                    'safe'                   => 'boolean',
                    'title'                  => 'string',
                    'timecreated'            => 'bigint',
                    'timemodified'           => 'bigint',
                ),
            ),
            'templates' => array(
                'columns' => array(
                    'id'           => 'bigpk',
                    'name'         => 'string',
                    'content'      => 'text',
                    'title'        => 'string',
                    'description'  => 'text',
                    'timecreated'  => 'bigint',
                    'timemodified' => 'bigint',
                ),
                'no_index' => array('content', 'description'),
            ),
            'widgets' => array(
                'columns' => array(
                    'id'           => 'bigpk',
                    'name'         => 'string',
                    'templateid'   => 'bigint',
                    'configdataid' => 'bigint',
                    'title'        => 'string',
                    'description'  => 'text',
                    'timecreated'  => 'bigint',
                    'timemodified' => 'bigint',
                ),
                'no_index' => array('description'),
            ),
            'pointers' => array(
                'columns' => array(
                    'id'           => 'bigpk',
                    'modelid'      => 'string',
                    'objectid'     => 'bigint',
                    'objectfield'  => 'string',
                    'timecreated'  => 'bigint',
                    'timemodified' => 'bigint',
                ),
            ),
            'meta_links' => array(
                'columns' => array(
                    'id'              => 'bigpk',
                    'name'            => 'string',
                    'title'           => 'string',
                    'sourcepointerid' => 'bigint',
                    'targetpointerid' => 'bigint',
                    'timecreated'     => 'bigint',
                    'timemodified'    => 'bigint',
                ),
            ),
            'attributes' => array(
                'columns' => array(
                    'id'               => 'bigpk',
                    'parentid'         => 'bigint',
                    'name'             => 'string',
                    'title'            => 'string',
                    'description'      => 'string',
                    'valuetypemodelid' => 'bigint',
                    'multiple'         => 'boolean',
                    'timecreated'      => 'bigint',
                    'timemodified'     => 'bigint',
                ),
            ),
            'model_attributes' => array(
                'columns' => array(
                    'id'           => 'bigpk',
                    'modelid'      => 'bigint',
                    'objectid'     => 'bigint',
                    'newtitle'     => 'string',
                    'attributeid'  => 'bigint',
                    'defaultvalue' => 'text',
                    'timecreated'  => 'bigint',
                    'timemodified' => 'bigint',
                ),
                'no_index' => array('defaultvalue'),
            ),
            'attribute_values' => array(
                'columns' => array(
                    'id'           => 'bigpk',
                    'attributeid'  => 'bigint',
                    'valuemodelid' => 'bigint',
                    'valueid'      => 'bigint',
                    'timecreated'  => 'bigint',
                    'timemodified' => 'bigint',
                    //'valuemodelid' => 'bigint',
                    //'modelid'      => 'bigint',
                    //'objectid'     => 'bigint',
                ),
            ),
            'value_json' => array(
                'columns' => array(
                    'id'           => 'bigpk',
                    'data'         => 'text',
                    'timecreated'  => 'bigint',
                    'timemodified' => 'bigint',
                ),
                'no_index' => array('data'),
            ),
            'value_int' => array(
                'columns' => array(
                    'id'           => 'bigpk',
                    'value'        => 'bigint',
                    'timecreated'  => 'bigint',
                    'timemodified' => 'bigint',
                ),
            ),
            'value_string' => array(
                'columns' => array(
                    'id'           => 'bigpk',
                    'value'        => 'string',
                    'timecreated'  => 'bigint',
                    'timemodified' => 'bigint',
                ),
            ),
            'value_text' => array(
                'columns' => array(
                    'id'           => 'bigpk',
                    'value'        => 'text',
                    'timecreated'  => 'bigint',
                    'timemodified' => 'bigint',
                ),
                'no_index' => array('value'),
            ),
            'value_boolean' => array(
                'columns' => array(
                    'id'           => 'bigpk',
                    'value'        => 'boolean',
                    'timecreated'  => 'bigint',
                    'timemodified' => 'bigint',
                ),
            ),
            'value_float' => array(
                'columns' => array(
                    'id'           => 'bigpk',
                    'value'        => 'float',
                    'timecreated'  => 'bigint',
                    'timemodified' => 'bigint',
                ),
            ),
            'forms' => array(
                'columns' => array(
                    'id'                => 'bigpk',
                    'name'              => 'string',
                    'method'            => 'string',
                    'action'            => 'string',
                    'activeformoptions' => 'string',
                    'displaytype'       => 'string',
                    'clientvalidation'  => 'boolean',
                    'ajaxvalidation'    => 'boolean',
                    'title'             => 'string',
                    'description'       => 'text',
                    'timecreated'       => 'bigint',
                    'timemodified'      => 'bigint',
                ),
                'no_index' => array('description'),
            ),
            'form_fields' => array(
                'columns' => array(
                    'id'               => 'bigpk',
                    'formid'           => 'bigint',
                    'type'             => 'string',
                    'widgetid'         => 'bigint',
                    'name'             => 'string',
                    'label'            => 'string',
                    'labeloptionsid'   => 'bigint',
                    'description'      => 'text',
                    'hint'             => 'text',
                    'hintoptionsid'    => 'bigint',
                    'prepend'          => 'string',
                    'prependoptionsid' => 'bigint',
                    'append'           => 'string',
                    'appendoptionsid'  => 'bigint',
                    'clientvalidation' => 'boolean',
                    'ajaxvalidation'   => 'boolean',
                    'htmloptionsid'    => 'bigint',
                    'timecreated'      => 'bigint',
                    'timemodified'     => 'bigint',
                    'sortorder'        => 'int',
                ),
                'no_index' => array('description', 'hint'),
            ),
            'events' => array(
                'columns' => array(
                    'id'            => 'bigpk',
                    'name'          => 'string',
                    'eventclass'    => 'string',
                    'title'         => 'string',
                    'description'   => 'text',
                    'enabled'       => 'boolean',
                    'timecreated'   => 'bigint',
                    'timemodified'  => 'bigint',
                ),
                'no_index' => array('description'),
            ),
            'events_listeners' => array(
                'columns' => array(
                    'id'                => 'bigpk',
                    'eventid'           => 'bigint',
                    'listenermodelid'   => 'bigint',
                    'launchermodelid'   => 'bigint',
                    'actionchainid'     => 'bigint',
                    //'sleepconditionid'  => 'bigint',
                    'muteconditionid'   => 'bigint',
                    'enabled'           => 'boolean',
                    'timecreated'       => 'bigint',
                    'timemodified'      => 'bigint',
                ),
            ),
            'event_launchers' => array(
                'columns' => array(
                    'id'                 => 'bigpk',
                    'eventid'            => 'bigint',
                    'launchermodelid'    => 'bigint',
                    'launcherobjectid'   => 'bigint',
                    //'silenceconditionid' => 'bigint',
                    'enabled'            => 'boolean',
                    'timecreated'        => 'bigint',
                    'timemodified'       => 'bigint',
                ),
            ),
            'entities' => array(
                'columns' => array(
                    'id'            => 'bigpk',
                    'parentid'      => 'bigint',
                    'modelid'       => 'bigint',
                    'timecreated'   => 'bigint',
                    'timemodified'  => 'bigint',
                ),
            ),
        );
        $this->createTableList($tables, $prefix);
    }
}