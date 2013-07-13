<?php

/**
 * This is the model class for table "{{menu_items}}".
 *
 * The followings are the available columns in table '{{menu_items}}':
 * @property string $id
 * @property string $type
 * @property string $label
 * @property string $relpath
 * @property string $fullpath
 * @property integer $custom
 * @property integer $newwindow
 * @property integer $order
 * @property string $pictureid
 * @property integer $visible
 */
class ECMenuItem extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ECMenuItem the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{menu_items}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('type, label, order', 'required'),
            array('custom, newwindow, order, visible', 'numerical', 'integerOnly'=>true),
            array('type', 'length', 'max'=>20),
            array('label', 'length', 'max'=>127),
            array('relpath, fullpath', 'length', 'max'=>255),
            array('pictureid', 'length', 'max'=>11),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, type, label, relpath, fullpath, custom, newwindow, order, pictureid, visible', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
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
            'type' => 'Type',
            'label' => 'Label',
            'relpath' => 'Relpath',
            'fullpath' => 'Fullpath',
            'custom' => 'Custom',
            'newwindow' => 'Newwindow',
            'order' => 'Order',
            'pictureid' => 'Pictureid',
            'visible' => 'Visible',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id,true);
        $criteria->compare('type',$this->type,true);
        $criteria->compare('label',$this->label,true);
        $criteria->compare('relpath',$this->relpath,true);
        $criteria->compare('fullpath',$this->fullpath,true);
        $criteria->compare('custom',$this->custom);
        $criteria->compare('newwindow',$this->newwindow);
        $criteria->compare('order',$this->order);
        $criteria->compare('pictureid',$this->pictureid,true);
        $criteria->compare('visible',$this->visible);

        return new CActiveDataProvider($this, array(
                                                   'criteria'=>$criteria,
                                              ));
    }

    /**
     * Получить ссылку на пункт меню
     * @todo написать получение ссылки на добавленные пользователем пункты
     */
    public function getImage()
    {
        if ( ! $this->custom )
        {
            if ( $this->visible )
            {
                return $this->label.'.png';
            }else
           {
                return $this->label.'_gray.png';
            }
            
        }
        
        return '';
    }

    /**
     * Получить ссылку, на которую указывает пункт меню
     * @return string
     */
    public function getLink()
    {
        if ( ! $this->custom )
        {
            if ( $this->visible )
            {
                return Yii::app()->getBaseUrl(true).'/'.$this->relpath;
            }else
           {
                return '#';
            }
        }

        return '#';
    }
    
    public function getLinkTarget()
    {
        if ( $this->newwindow )
        {
            return '_blank';
        }else
        {
            return '_self';
        }
    }
}