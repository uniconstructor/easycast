<?php

namespace SchemaBuilder\RDF;

/**
 * This is the model class for table "{{rds_entities}}".
 *
 * Таблица '{{rds_entities}}':
 * @property integer $id
 * @property string $name
 * @property string $title
 */
class RdfEntity extends CActiveRecord
{
    /**
     * @var string
     */
    protected static $rdsPrefix   = 'rds_';
    /**
     * @var array
     */
    protected static $rdsTableMap = array(
        'Entity' => 'entities',
        'EntityFieldType' => 'entity_field_types',
    );
    /**
     * @var string - entity model class
     */
    protected static $rdsClassMap = array();
    /**
     * @var string
     */
    protected $entityClass = 'Entity';
    
    /**
     * @access public
     * @return string the associated database table name
     */
    public function tableName()
    {
        if ( ! self::$_entityClass )
        {
            throw new InvalidArgumentException('Entity class not set');
        }
        if ( ! isset(self::$rdsClassMap[]) )
        {
            
        }
        return '{{'.self::$rdsPrefix.'entities}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name, title', 'length', 'max' => 255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, title', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'title' => 'Title',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('title',$this->title,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * 
     * @param string $entityClass active record class name.
     * @return Entity the static model class
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
            self::$_md[$className] = \SchemaBuilder\RDF\EntityMetaData($this);
        }
        return self::$_md[$className];
    }
}