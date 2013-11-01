<?php

/**
 * Связь разделов каталога с вкладками
 *
 * Таблица '{{catalog_tab_instances}}':
 * @property integer $id
 * @property integer $sectionid
 * @property integer $parentid
 * @property integer $tabid
 * @property string $newname
 * @property string $lang
 * @property integer $visible
 * 
 * Relations:
 * @property CatalogTab $tab
 * @property CatalogTab $parent
 * @property CatalogTabInstance $parentLink
 * @property CatalogSection $section
 * 
 * @todo убрать поле lang - оно здесь не нужно
 * @todo добавить поле order - чтобы вкладки можно было выводить в указанном порядке
 */
class CatalogTabInstance extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CatalogTabInstance the static model class
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
		return '{{catalog_tab_instances}}';
	}
	
	/**
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave()
	{
	    if ( $this->isNewRecord )
	    {
	        if ( ! $this->parentid )
	        {
	            $this->parentid = 0;
	        }
	    }
	    return parent::beforeSave();
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('visible', 'numerical', 'integerOnly' => true),
			array('sectionid, parentid, tabid', 'length', 'max' => 11),
			array('newname', 'length', 'max' => 128),
			array('lang', 'length', 'max' => 5),
			// The following rule is used by search().
			array('id, sectionid, parentid, tabid, newname, lang, visible', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // Вкладка с критериями поиска
		    'tab'     => array(self::BELONGS_TO, 'CatalogTab', 'tabid'),
		    // Раздел каталога
		    'section' => array(self::BELONGS_TO, 'CatalogSection', 'sectionid'),
		    // ссылка на родительскую вкладку
		    'parentLink' => array(self::BELONGS_TO, 'CatalogTabInstance', 'parentid'),
		    // Родительская вкладка
		    'parent' => array(self::HAS_ONE, 'CatalogTab', 'tabid', 'through' => 'parentLink'),
		);
	}
	
	/**
	 * @see CActiveRecord::defaultScope()
	 */
	public function defaultScope()
	{
	    return array(
	        //'order' => '`order` ASC, `id` ASC',
	        'order' => '`id` ASC',
	    );
	}
	
	/**
	 * @see CActiveRecord::scopes()
	 */
	public function scopes()
	{
	    return array(
	        'visible' => array(
	            'condition' => '`visible` = 1',
	        ),
	    );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'sectionid' => 'Sectionid',
			'parentid' => 'Parentid',
			'tabid' => 'Tabid',
			'newname' => 'Newname',
			'lang' => 'Lang',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('sectionid',$this->sectionid,true);
		$criteria->compare('parentid',$this->parentid,true);
		$criteria->compare('tabid',$this->tabid,true);
		$criteria->compare('newname',$this->newname,true);
		$criteria->compare('lang',$this->lang,true);
		$criteria->compare('visible',$this->visible);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}