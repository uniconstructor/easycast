<?php

namespace SchemaBuilder\Model;

/**
 * Entity class - parent for all AR-classes
 */
class Entity extends \CActiveRecord
{
    /**
     * @var string - entity model class
     */
    protected static $_entityClass = 'Entity';
    
    /**
     * Returns the static model of Entity table
     *
     * @static
     * @access public
     * @param  string $surveyid
     * @return SurveyDynamic
     */
    public static function model($entityClass=null)
    {
        $refresh = false;
        if ( ! is_null($entityClass) )
        {
            self::_entityClass($entityClass);
            $refresh = true;
        }
        $model = parent::model(__CLASS__);
        //We need to refresh if we changed EntityClass
        if ( $refresh === true )
        {
            $model->refreshMetaData();
        }
        return $model;
    }
    
    /**
     * Sets the entity class for the model
     *
     * @static
     * @access public
     * @param  string $className
     * @return void
     */
    public static function setEntityClass($entityClass)
    {
        if ( ! $entityClass )
        {
            throw new InvalidArgumentException('Entity class not set');
        }
        self::$_entityClass = mb_strtolower($entityClass);
    }
    
    /**
     * Returns the setting's table name to be used by the model
     *
     * @access public
     * @return string
     */
    public function tableName()
    {
        if ( ! self::$_entityClass )
        {
            throw new InvalidArgumentException('Entity class not set');
        }
        $prefix = Yii::app()->getModule('smartAdmin')->tablePrefix;
        return '{{'.$prefix.'_'.mb_strtolower(self::$_entityClass).'}}';
    }
    
    /**
     * Create new instance of a specified class and populate it with given data.
     * @see http://stackoverflow.com/questions/829823/can-you-create-instance-properties-dynamically-in-php
     *
     * @param string $className
     * @param array $data  e.g. array(columnName => value, ..)
     * @param array $mappings  Map column name to class field name, e.g. array(columnName => fieldName)
     * @return object  Populated instance of $className
     */
    public function createEntity($className, array $data, $mappings = array())
    {
        $reflClass = new ReflectionClass($className);
        // Creates a new instance of a given class, without invoking the constructor.
        $entity = unserialize(sprintf('O:%d:"%s":0:{}', strlen($className), $className));
        foreach ($data as $column => $value)
        {
            // translate column name to an entity field name
            $field = isset($mappings[$column]) ? $mappings[$column] : $column;
            if ($reflClass->hasProperty($field))
            {
                $reflProp = $reflClass->getProperty($field);
                $reflProp->setAccessible(true);
                $reflProp->setValue($entity, $value);
            }
        }
        return $entity;
    }
    
    /**
     * Returns the meta-data for this AR
     * 
     * @return CActiveRecordMetaData the meta for this AR class.
     */
    public function getMetaData()
    {
        $className = get_class($this);
        if ( ! array_key_exists($className,self::$_md) )
        {
            self::$_md[$className] = null; // preventing recursive invokes of {@link getMetaData()} via {@link __get()}
            self::$_md[$className] = new EntityMetaData($this);
        }
        return self::$_md[$className];
    }
}

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

