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
                    'model'         => "string NOT NULL",
                    'table'         => "string NOT NULL",
                    'title'         => "string NOT NULL",
                    'description'   => "text",
                    'system'        => 'boolean NOT NULL DEFAULT 0',
                    'defaultformid' => "bigint NOT NULL DEFAULT 0",
                    'timecreated'   => "bigint NOT NULL DEFAULT 0",
                    'timemodified'  => "bigint NOT NULL DEFAULT 0",
                ),
                'no_index' => array('description'),
            ),
            'relations' => array(
                'columns' => array(
                    'id'            => 'bigpk',
                    'modelid'       => "bigint NOT NULL DEFAULT 0",
                    'name'          => 'string NOT NULL',
                    'type'          => 'string NOT NULL',
                    'fk0'           => 'varchar(64)',
                    'fk1'           => 'varchar(64)',
                    'fk2'           => 'varchar(64)',
                    'pk1'           => 'varchar(64)',
                    'pk2'           => 'varchar(64)',
                    'fkc1'          => 'varchar(64)',
                    'pkc1'          => 'varchar(64)',
                    'fkc2'          => 'varchar(64)',
                    'pkc2'          => 'varchar(64)',
                    'condition'     => 'string',
                    'relatedmodel'  => 'string NOT NULL',
                    'title'         => 'string',
                    'description'   => 'string',
                    'timecreated'   => "bigint NOT NULL DEFAULT 0",
                    'timemodified'  => "bigint NOT NULL DEFAULT 0",
                ),
            ),
            'rules' => array(
                'columns' => array(
                    'id'                     => 'bigpk',
                    'modelid'                => "bigint NOT NULL DEFAULT 0",
                    'validatedattributes'    => "string NOT NULL",
                    'validator'              => 'string NOT NULL',
                    'on'                     => 'string',
                    'except'                 => 'string',
                    'message'                => 'string',
                    'skiponerror'            => "boolean NOT NULL DEFAULT 0",
                    'enableclientvalidation' => "boolean NOT NULL DEFAULT 0",
                    'safe'                   => "boolean NOT NULL DEFAULT 0",
                    'title'                  => 'string',
                    'timecreated'            => "bigint NOT NULL DEFAULT 0",
                    'timemodified'           => "bigint NOT NULL DEFAULT 0",
                ),
            ),
            'templates' => array(
                'columns' => array(
                    'id'           => 'bigpk',
                    'name'         => "string",
                    'content'      => 'text NOT NULL',
                    'title'        => 'string',
                    'description'  => 'text',
                    'timecreated'  => "bigint NOT NULL DEFAULT 0",
                    'timemodified' => "bigint NOT NULL DEFAULT 0",
                ),
                'no_index' => array('content', 'description'),
            ),
            'widgets' => array(
                'columns' => array(
                    'id'           => 'bigpk',
                    'name'         => 'string',
                    'templateid'   => "bigint NOT NULL DEFAULT 0",
                    'configdataid' => "bigint NOT NULL DEFAULT 0",
                    'title'        => 'string NOT NULL',
                    'description'  => 'text',
                    'timecreated'  => "bigint NOT NULL DEFAULT 0",
                    'timemodified' => "bigint NOT NULL DEFAULT 0",
                ),
                'no_index' => array('description'),
            ),
            'pointers' => array(
                'columns' => array(
                    'id'           => 'bigpk',
                    'modelid'      => "bigint NOT NULL DEFAULT 0",
                    'objectid'     => "bigint NOT NULL DEFAULT 0",
                    'objectfield'  => 'string NOT NULL',
                    'timecreated'  => "bigint NOT NULL DEFAULT 0",
                    'timemodified' => "bigint NOT NULL DEFAULT 0",
                ),
            ),
            'meta_links' => array(
                'columns' => array(
                    'id'              => 'bigpk',
                    'name'            => 'string',
                    'title'           => 'string',
                    'sourcepointerid' => "bigint NOT NULL DEFAULT 0",
                    'targetpointerid' => "bigint NOT NULL DEFAULT 0",
                    'timecreated'     => "bigint NOT NULL DEFAULT 0",
                    'timemodified'    => "bigint NOT NULL DEFAULT 0",
                ),
            ),
            'attributes' => array(
                'columns' => array(
                    'id'               => 'bigpk',
                    'parentid'         => "bigint NOT NULL DEFAULT 0",
                    'name'             => 'string NOT NULL',
                    'title'            => 'string',
                    'description'      => 'string',
                    'valuetypemodelid' => "bigint NOT NULL DEFAULT 0",
                    'multiple'         => "boolean NOT NULL DEFAULT 0",
                    'timecreated'      => "bigint NOT NULL DEFAULT 0",
                    'timemodified'     => "bigint NOT NULL DEFAULT 0",
                ),
            ),
            'model_attributes' => array(
                'columns' => array(
                    'id'           => 'bigpk',
                    'modelid'      => "bigint NOT NULL DEFAULT 0",
                    'objectid'     => "bigint NOT NULL DEFAULT 0",
                    'newtitle'     => 'string',
                    'attributeid'  => "bigint NOT NULL DEFAULT 0",
                    'defaultvalue' => 'text',
                    'timecreated'  => "bigint NOT NULL DEFAULT 0",
                    'timemodified' => "bigint NOT NULL DEFAULT 0",
                ),
                'no_index' => array('defaultvalue'),
            ),
            'attribute_values' => array(
                'columns' => array(
                    'id'           => 'bigpk',
                    'attributeid'  => "bigint NOT NULL DEFAULT 0",
                    'valuemodelid' => "bigint NOT NULL DEFAULT 0",
                    'valueid'      => "bigint NOT NULL DEFAULT 0",
                    'timecreated'  => "bigint NOT NULL DEFAULT 0",
                    'timemodified' => "bigint NOT NULL DEFAULT 0",
                    //'valuemodelid' => "bigint NOT NULL DEFAULT 0",
                    //'modelid'      => "bigint NOT NULL DEFAULT 0",
                    //'objectid'     => "bigint NOT NULL DEFAULT 0",
                ),
            ),
            'value_json' => array(
                'columns' => array(
                    'id'           => 'bigpk',
                    'data'         => "text NOT NULL",
                    'timecreated'  => "bigint NOT NULL DEFAULT 0",
                    'timemodified' => "bigint NOT NULL DEFAULT 0",
                ),
                'no_index' => array('data'),
            ),
            'value_int' => array(
                'columns' => array(
                    'id'           => 'bigpk',
                    'value'        => "bigint NOT NULL DEFAULT 0",
                    'timecreated'  => "bigint NOT NULL DEFAULT 0",
                    'timemodified' => "bigint NOT NULL DEFAULT 0",
                ),
            ),
            'value_string' => array(
                'columns' => array(
                    'id'           => 'bigpk',
                    'value'        => 'string NOT NULL',
                    'timecreated'  => "bigint NOT NULL DEFAULT 0",
                    'timemodified' => "bigint NOT NULL DEFAULT 0",
                ),
            ),
            'value_text' => array(
                'columns' => array(
                    'id'           => 'bigpk',
                    'value'        => 'text NOT NULL',
                    'timecreated'  => "bigint NOT NULL DEFAULT 0",
                    'timemodified' => "bigint NOT NULL DEFAULT 0",
                ),
                'no_index' => array('value'),
            ),
            'value_boolean' => array(
                'columns' => array(
                    'id'           => 'bigpk',
                    'value'        => "boolean NOT NULL DEFAULT 0",
                    'timecreated'  => "bigint NOT NULL DEFAULT 0",
                    'timemodified' => "bigint NOT NULL DEFAULT 0",
                ),
            ),
            'value_float' => array(
                'columns' => array(
                    'id'           => 'bigpk',
                    'value'        => 'float',
                    'timecreated'  => "bigint NOT NULL DEFAULT 0",
                    'timemodified' => "bigint NOT NULL DEFAULT 0",
                ),
            ),
            'forms' => array(
                'columns' => array(
                    'id'                => 'bigpk',
                    'name'              => 'string',
                    'title'             => 'string',
                    'description'       => 'text',
                    'method'            => "varchar(6) NOT NULL DEFAULT 'post'",
                    'action'            => 'string',
                    'displaytype'       => "string NOT NULL DEFAULT 'vertical'",
                    'clientvalidation'  => "boolean NOT NULL DEFAULT 0",
                    'ajaxvalidation'    => "boolean NOT NULL DEFAULT 0",
                    'timecreated'       => "bigint NOT NULL DEFAULT 0",
                    'timemodified'      => "bigint NOT NULL DEFAULT 0",
                ),
                'no_index' => array('description'),
            ),
            'form_fields' => array(
                'columns' => array(
                    'id'               => 'bigpk',
                    'formid'           => "bigint NOT NULL DEFAULT 0",
                    'type'             => 'string',
                    'widgetid'         => "bigint NOT NULL DEFAULT 0",
                    'name'             => 'string NOT NULL',
                    'label'            => 'string',
                    'description'      => 'text',
                    'hinttext'         => 'text',
                    'prependtext'      => 'string',
                    'appendtext'       => 'string',
                    'clientvalidation' => "boolean NOT NULL DEFAULT 0",
                    'ajaxvalidation'   => "boolean NOT NULL DEFAULT 0",
                    'timecreated'      => "bigint NOT NULL DEFAULT 0",
                    'timemodified'     => "bigint NOT NULL DEFAULT 0",
                    'sortorder'        => 'int',
                ),
                'no_index' => array('description', 'hinttext'),
            ),
            'events' => array(
                'columns' => array(
                    'id'            => 'bigpk',
                    'name'          => 'string NOT NULL',
                    'eventclass'    => 'string NOT NULL',
                    'title'         => 'string NOT NULL',
                    'description'   => 'text',
                    'enabled'       => "boolean NOT NULL DEFAULT 1",
                    'timecreated'   => "bigint NOT NULL DEFAULT 0",
                    'timemodified'  => "bigint NOT NULL DEFAULT 0",
                ),
                'no_index' => array('description'),
            ),
            'events_listeners' => array(
                'columns' => array(
                    'id'                => 'bigpk',
                    'eventid'           => "bigint NOT NULL DEFAULT 0",
                    'listenermodelid'   => "bigint NOT NULL DEFAULT 0",
                    'launchermodelid'   => "bigint NOT NULL DEFAULT 0",
                    'actionchainid'     => "bigint NOT NULL DEFAULT 0",
                    //'sleepconditionid'  => "bigint NOT NULL DEFAULT 0",
                    'muteconditionid'   => "bigint NOT NULL DEFAULT 0",
                    'enabled'           => "boolean NOT NULL DEFAULT 1",
                    'timecreated'       => "bigint NOT NULL DEFAULT 0",
                    'timemodified'      => "bigint NOT NULL DEFAULT 0",
                ),
            ),
            'event_launchers' => array(
                'columns' => array(
                    'id'                 => 'bigpk',
                    'eventid'            => "bigint NOT NULL DEFAULT 0",
                    'launchermodelid'    => "bigint NOT NULL DEFAULT 0",
                    'launcherobjectid'   => "bigint NOT NULL DEFAULT 0",
                    //'silenceconditionid' => "bigint NOT NULL DEFAULT 0",
                    'enabled'            => "boolean NOT NULL DEFAULT 1",
                    'timecreated'        => "bigint NOT NULL DEFAULT 0",
                    'timemodified'       => "bigint NOT NULL DEFAULT 0",
                ),
            ),
            'entities' => array(
                'columns' => array(
                    'id'            => 'bigpk',
                    'parentid'      => "bigint NOT NULL DEFAULT 0",
                    'modelid'       => "bigint NOT NULL DEFAULT 0",
                    'timecreated'   => "bigint NOT NULL DEFAULT 0",
                    'timemodified'  => "bigint NOT NULL DEFAULT 0",
                ),
            ),
        );
        $this->createTableList($tables, $prefix);
    }
}