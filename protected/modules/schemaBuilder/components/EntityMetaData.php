<?php

namespace SchemaBuilder\RDF;

/**
 * Custom metadata class for loading relations from the base
 */
class EntityMetaData
{
    /**
     * @var CDbTableSchema the table schema information
     */
    public $tableSchema;
    /**
     * @var array table columns
     */
    public $columns;
    /**
     * @var array list of relations
     */
    public $relations=array();
    /**
     * @var array attribute default values
    */
    public $attributeDefaults=array();
    /**
     * @var string
     */
    private $_modelClassName;

    /**
     * Constructor.
     * @param CActiveRecord $model the model instance
     * @throws CDbException if specified table for active record class cannot be found in the database
     */
    public function __construct($model)
    {
        $this->_modelClassName=get_class($model);

        $tableName=$model->tableName();
        if(($table=$model->getDbConnection()->getSchema()->getTable($tableName))===null)
            throw new CDbException(Yii::t('yii','The table "{table}" for active record class "{class}" cannot be found in the database.',
                array('{class}'=>$this->_modelClassName,'{table}'=>$tableName)));
        if($table->primaryKey===null)
        {
            $table->primaryKey=$model->primaryKey();
            if(is_string($table->primaryKey) && isset($table->columns[$table->primaryKey]))
                $table->columns[$table->primaryKey]->isPrimaryKey=true;
            elseif(is_array($table->primaryKey))
            {
                foreach($table->primaryKey as $name)
                {
                    if(isset($table->columns[$name]))
                        $table->columns[$name]->isPrimaryKey=true;
                }
            }
        }
        $this->tableSchema=$table;
        $this->columns=$table->columns;

        foreach($table->columns as $name=>$column)
        {
            if(!$column->isPrimaryKey && $column->defaultValue!==null)
                $this->attributeDefaults[$name]=$column->defaultValue;
        }

        foreach($model->relations() as $name=>$config)
        {
            $this->addRelation($name,$config);
        }
    }
    
    /**
     * Загрузить стандартный AR-массив метаданных из таблиц модуля
     *
     * @return void
     */
    protected function loadCustomMetaData($model=null)
    {
    
    }
}