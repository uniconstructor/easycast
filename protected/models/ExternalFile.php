<?php

/**
 * Модель для работы с внешними файлами (внешними файлами считаются те которые хранятся
 * на других серверах, в основном на Amazon S3)
 *
 * Таблица '{{external_files}}':
 * @property integer $id
 * @property string $originalid
 * @property string $previousid
 * @property string $name
 * @property string $title
 * @property string $description
 * @property string $oldname
 * @property string $newname
 * @property string $storage
 * @property string $timecreated
 * @property string $timemodified
 * @property string $timeuploaded
 * @property string $bucket
 * @property string $path
 * @property string $mimetype
 * @property string $size
 * @property string $md5
 * @property string $updateaction
 * @property string $deleteaction
 * @property string $deleteafter
 * @property string $status
 * 
 * @todo документировать все поля
 * @todo прописать связи
 * @todo подключить настройки
 */
class ExternalFile extends SWActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{external_files}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name', 'required'),
			array('originalid, previousid, timecreated, timemodified, timeuploaded, deleteafter', 'length', 'max'=>11),
			array('name, title, oldname, newname, path', 'length', 'max' => 255),
			array('description', 'length', 'max' => 4095),
			array('storage, mimetype, updateaction, deleteaction', 'length', 'max' => 10),
			array('bucket, md5', 'length', 'max' => 128),
			array('size', 'length', 'max' => 21),
			array('status', 'length', 'max' => 50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			//array('id, originalid, previousid, name, title, description, oldname, newname, storage, timecreated, timemodified, timeuploaded, bucket, path, mimetype, size, md5, updateaction, deleteaction, deleteafter, status', 'safe', 'on'=>'search'),
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
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
	    return array(
	        // автоматическое заполнение дат создания и изменения
	        'EcTimestampBehavior' => array(
	            'class' => 'application.behaviors.EcTimestampBehavior',
	        ),
	        // подключаем расширение для работы со статусами
	        'swBehavior' => array(
	            'class' => 'ext.simpleWorkflow.SWActiveRecordBehavior',
	        ),
	        // это поведение позволяет изменять набор связей модели в процессе выборки
	        'CustomRelationsBehavior' => array(
	            'class' => 'application.behaviors.CustomRelationsBehavior',
	        ),
	        // это поведение позволяет задать поиск по любому полю модели в виде scopes()
	        'CustomScopesBehavior' => array(
	            'class' => 'application.behaviors.CustomScopesBehavior',
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
			'originalid' => 'Оригинал файла',
			'previousid' => 'Предыдущая версия файла',
			'name' => 'Name',
			'title' => 'Title',
			'description' => 'Description',
			'oldname' => 'Oldname',
			'newname' => 'Newname',
			'storage' => 'Storage',
			'timecreated' => 'Timecreated',
			'bucket' => 'Bucket',
			'path' => 'Path',
			'mimetype' => 'Mimetype',
			'size' => 'Size',
			'md5' => 'Md5',
			'updateaction' => 'Updateaction',
			'deleteaction' => 'Deleteaction',
			'deleteafter' => 'Deleteafter',
			'status' => 'Status',
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
	/*public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('originalid',$this->originalid,true);
		$criteria->compare('previousid',$this->previousid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('oldname',$this->oldname,true);
		$criteria->compare('newname',$this->newname,true);
		$criteria->compare('storage',$this->storage,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);
		$criteria->compare('timeuploaded',$this->timeuploaded,true);
		$criteria->compare('bucket',$this->bucket,true);
		$criteria->compare('path',$this->path,true);
		$criteria->compare('mimetype',$this->mimetype,true);
		$criteria->compare('size',$this->size,true);
		$criteria->compare('md5',$this->md5,true);
		$criteria->compare('updateaction',$this->updateaction,true);
		$criteria->compare('deleteaction',$this->deleteaction,true);
		$criteria->compare('deleteafter',$this->deleteafter,true);
		$criteria->compare('status',$this->status,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}*/

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ExternalFile the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
