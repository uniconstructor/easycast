<?php

/**
 * Модель для работы с настройками приложения.
 * Настройки могут быть прикреплены к любой модели в системе. 
 * Значения настроек хранятся отдельно, в таблице config_values.
 * 
 * 
 * Чтобы работать с настройками нужно хорошо различать три разных понятия:
 * 1) Текущее значение: это запись значения настройки (ConfigValue) привязанное к модели настройки
 *    (или список текущих значений, если в настройке допустим множественный выбор)
 * 2) Значение по умолчанию: это запись значения настройки (ConfigValue) привязанная к родительской
 *    модели настройки (таким образом у корневых настроек значений по умолчанию быть не может:
 *    они задаются в коде руками и попадают в базу через миграцию в момент добавления новой настройки)
 *    (или список значений по умолчанию, если в настройке допустим множественный выбор)
 * 3) Список стандартных значений: это список (EasyList) хранящий элементы (EasyListItem)
 *    названия значения которых используются как предлагаемые варианты выбора при указании
 *    значения настройки (ConfigValue)
 *    Называется "списком возможных значений" если в настройке разрешено указание своих вариантов
 *    помимо стандартных и "списком допустимых значений" если в значении настройки свои варианты
 *    указывать запрещено :)
 * 
 * Самое главное из всего этого то, что список стандартных значений 
 * и список значений по умолчанию это разные вещи.
 * 
 * Настройка может быть привязана к модели, поэтому важно понимать чем отличается запрос 
 * "список настроек со указанным значением" от запроса
 * "список моделей с настройкой имеющих указанное значение"
 *
 * Таблица '{{config}}':
 * @property int    $id
 * @property int    $easylistid   - id списка со стандартными значениями этой настройки 
 *                                  (для select-списков)
 *                                  0 если стандартных значений не предусмотрено
 * @property int    $parentid     - id базовой настройки. Базовой считается настройка, которая
 *                                  была использована как шаблон чтобы создать эту модель.
 *                                  Значения, заданные в родительской настройке считаются стандартными
 *                                  значениями для дочерних настроек
 *                                  Самым верхнем уровнем являются системные настройки: 
 *                                  они не привязываются к какому-либо объекту в системе, а служат
 *                                  образцом для создания настроек такого же типа.
 * @property string $name         - служебное имя настройки, маленькие латинские буквы и точки
 *                                  @todo две системные настройки не могут иметь одинаковых названий
 * @property string $title        - название настройки для отображения
 * @property string $description  - пояснение для настройки
 * @property string $type         - тип настройки (чаще всего совпадает с названием input-типа для
 *                                  элемента формы или названием класса виджета, который будет использован
 *                                  для вывода этой настройки)
 * @property int    $minvalues    - минимальное количество значений которые нужно выбрать в этой настройке
 *                                  0   : заполнение необязательно
 *                                  1   : заполнение обязательно
 *                                  n>1 : (все что больше единицы) требуется выбрать как минимум n
 *                                        значений, иначе настройка не будет считаться заполненой.
 *                                        Заполнение такой настройки может быть обязательно,
 *                                        но если она не заполнена - мы подставляем значения по 
 *                                        умолчанию из родительской настройки
 * @property int    $maxvalues    - максимальное количество выбранных значений для этой настройки
 *                                  0   : неограничено
 *                                  1   : только одно значение: используется для текстовых строк,  
 *                                        JSON-значений а также для обычных элементов radio или select
 *                                  n>1 : (все что больше единицы) ограничение максимального количества
 *                                        одновременно выбранных вариантов
 * @property string $objecttype   - тип объекта к которому привязана настройка: чаще всего здесь
 *                                  указан класс модели к которой привязана эта настройка
 *                                  Для системных настроек это поле равно "system"
 * @property int    $objectid     - id объекта к которому привязана настройка
 * @property int    $timecreated  - дата создания
 * @property int    $timemodified - последнее изменение
 * @property int    $userlistid   - id списка со дополнительными значениями для этой настройки
 * @property string $valuetype 
 * @property string $valuefield 
 * @property int    $valueid 
 * 
 * Relations:
 * @property ConfigValue[]   $configValues - все значения этой настройки
 * @property Config          $parentConfig - родительская настройка, из которой была создана эта
 * @property EasyList        $easyList
 * @property EasyList        $userList
 * @property CActiveRecord   $valueObject
 * @property CActiveRecord[] $valueObjects - список моделей которые считаются выбранными значениями
 *                                           для этой настройки 
 *                                           (если в настройке разрешен множественный выбор)
 *                                           Как правило это список EasyListItem 
 * 
 * @todo проверка для максимального/минимального количества значений
 * @todo проверка правильности указания служебного имени
 * @todo Создаваемые корневые настройки должны быть сразу же помечены измененными
 */
class Config extends CActiveRecord
{
    /**
     * @var string - тип настройки:
     */
    const TYPE_TEXT         = 'text';
    /**
     * @var string - тип настройки:
     */
    const TYPE_TOGGLE       = 'toggle';
    /**
     * @var string - тип настройки:
     */
    const TYPE_RADIO        = 'radio';
    /**
     * @var string - тип настройки:
     */
    const TYPE_CHECKBOX     = 'checkbox';
    /**
     * @var string - тип настройки:
     */
    const TYPE_CHECKBOXLIST = 'checkboxlist';
    /**
     * @var string - тип настройки: список
     */
    const TYPE_SELECT       = 'select';
    /**
     * @var string - тип настройки: список с множественным выбором
     */
    const TYPE_MULTISELECT  = 'multiselect';
    
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{config}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name, title', 'required'),
			array('name, title', 'length', 'max' => 255),
			array('description', 'length', 'max' => 4095),
			array('type', 'length', 'max' => 20),
			array('objecttype, valuetype, valuefield', 'length', 'max' => 50),
			array('userlistid, easylistid, parentid, minvalues, maxvalues, objectid, valueid, 
			    timecreated, timemodified', 
			    'length', 
			    'max' => 11,
            ),
			
			// The following rule is used by search().
			/*array('id, easylistid, parentid, name, title, description, type, 
			    minvalues, maxvalues, objecttype, objectid, timecreated, timemodified', 
			    'safe', 
			    'on' => 'search',
            ),*/
		);
	}
	
	/**
	 * @see CActiveRecord::beforeSave()
	 * @todo проверять существование привязанного объекта значения если тип значения это клас модели
	 */
	public function beforeSave()
	{
	    if ( $this->isNewRecord )
	    {
	        $listTypes = array(self::TYPE_CHECKBOXLIST, self::TYPE_SELECT, self::TYPE_RADIO);
	        if ( in_array($this->type, $listTypes) )
	        {// для новой настройки создаем список стандартных значений (если требуется)
                $easyList       = new EasyList();
                $easyList->name = 'Список значений для настройки "'.$this->title.'"';
                $easyList->save();
                // привязываем созданный пустой список к этой настройке
                $this->easylistid = $easyList->id;
	        }
	        if ( ! $this->parentid )
	        {// создается корневая настройка
        	    if ( $this->objectid != 0 )
        	    {// наждая настройка должна от чего-то наследоваться
        	        throw new CException('Новые корневые настройки могут быть созданы только для 
        	            моделей или системных настроек с objectid=0');
        	    }
    	    }
    	    if ( $this->forObject($this->objecttype, $this->objectid)->withName($this->name)->exists() )
    	    {// имя настройки должно быть уникальным
    	        if ( $this->objectid == 0 AND $this->objecttype != 'system' )
    	        {
    	            throw new CException('Невозможно создать настройку: 
    	                сочетание objecttype + objectid + name должно быть уникальным для каждой записи.
    	                Только настройка уровня system может иметь более одного objectid=0');
    	        }
    	    }
	    }
	    if ( $this->easylistid AND ! EasyList::model()->findByPk($this->easylistid) )
	    {// задан id списка значений - нужно проверить что он не пустой
	        throw new CException('Невозможно сохранить настройку: указанный в ней список стандартных
	            значений (id='.$this->easylistid.') не существует');
	    }
	    if ( $this->parentid AND ! Config::model()->findByPk($this->parentid) )
	    {// задан id списка значений - нужно проверить что он не пустой
	        throw new CException('Невозможно сохранить настройку: указанная родительская настройка
	            (id='.$this->easylistid.') не существует');
	    }
	    if ( $this->objecttype === 'system' AND $this->objectid != 0 )
	    {// все системные настройки должны придерживаться определенного формата
	        throw new CException('Невозможно сохранить настройку: настройка с типом system
	            может иметь только objectid=0');
	    }
	    return parent::beforeSave();
	}
	
	/**
	 * @see CActiveRecord::beforeDelete()
	 */
	public function beforeDelete()
	{
	    if ( $this->objecttype == 'system' OR ! $this->parentid OR ! $this->objectid )
	    {// Корневые или системные настройки удаляются только миграцией
	        throw new CException('Корневые или системные настройки удаляются только миграцией');
	    }
	    foreach ( $this->configValues as $value )
	    {// удаляем все значений этой настройки
	        $value->delete();
	    }
	    foreach ( $this->withParentId($this->id)->findAll() as $config )
	    {// удаляем все настройки, наследуемые от этой
	        $config->delete();
	    }
	    // @todo удаляем привязаный список стандартных значений (если он больше нигде не используется)
	    if ( $this->forUserList($this->userList) )
	    {// удаляем введенные пользовательские значения для настройки при удалении настройки,
	        // если список нигде не используется: список введенных пользователем значений
	        // с гораздо меньшей вероятностью где-то используется, поэтому будем искать
	        // ссылки на него только в настройках
	        // @todo каждый раз при привязке списка создавать EasyListInstance чтобы 
	        //       запоминать какие модели нужно проверить перед удалением списка
	        $this->userList->delete();
	    }
	    return parent::beforeDelete();
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // родительская настройка (из которой создана эта)
		    'parentConfig' => array(self::BELONGS_TO, 'Config', 'parentid'),
		    // список стандартных значений
		    'easyList'     => array(self::BELONGS_TO, 'EasyList', 'easylistid'),
		    // список нестандартных, введенных пользователем значений (если список разрешено дополнять)
		    'userList'     => array(self::BELONGS_TO, 'EasyList', 'userlistid'),
		    // модель в которой хранится значение настройки
		    // (если значение настройки задано как ссылка на поле в другой модели)
		    // @todo не добавлять эту связь если у настройки нет значения или тип значения 
		    // не является классом модели
		    'valueObject'  => array(self::BELONGS_TO, $this->valuetype, 'valueid'),
		    // список текущих значений этой настройки (только для настроек с множественным выбором)
		    // @todo проверить работу составного ключа
		    // @todo добавлять эту настройку только если valueType=easylist и maxvalues <> 0
		    'valueObjects' => array(self::HAS_MANY, $this->valuetype, array('valueid' => $this->valuefield)),
		    // список содержащий значения для выбранной настройки (частный случай valueObject)
		    'valueListObject' => array(self::BELONGS_TO, 'EasyList', 'valueid'),
		);
	}
	
	/**
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
	    return array(
	        // автоматическое заполнение дат создания и изменения
	        'CTimestampBehavior' => array(
	            'class'           => 'zii.behaviors.CTimestampBehavior',
	            'createAttribute' => 'timecreated',
	            // следует считать изменением настройки только изменение ее значений (ConfigValue) 
	            // Каждое удаление, изменение или добавление записей значений ссылающихся на эту 
	            // (а не родительскую) настройку должно обновлять timemodified
	            'updateAttribute' => 'timemodified',
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
			'easylistid' => 'Список стандартных вариантов значений для этой настройки',
		    'userlistid' => 'Список дополнительных вариантов значений для этой настройки (если разрешено добавление своих вариантов помимо стандартных)',
			'parentid' => 'Шаблон для этой настройки',
			'name' => Yii::t('coreMessages', 'name'),
			'title' => Yii::t('coreMessages', 'title'),
			'description' => Yii::t('coreMessages', 'description'),
			'type' => 'Тип настройки',
			'minvalues' => 'Минимальное количество выбранных значений',
			'maxvalues' => 'Максимальное количество выбранных значений (включительно)',
			'timecreated' => Yii::t('coreMessages', 'timecreated'),
			'timemodified' => Yii::t('coreMessages', 'timemodified'),
			'valuetype' => 'Тип значения или название класса модели в котором оно хранится',
			'valuefield' => 'Поле, в котором хранится значение',
			'valueid' => 'id модели в которой хранится значение настройки',
		);
	}
	
	/**
	 * @see CActiveRecord::scopes()
	 * @todo прописать все условия в комментариях класа как method чтобы работал codeAssist
	 */
	public function scopes()
	{
	    return array(
	        // все настройки, содержащие несколько значений (множественный выбор)
	        'multipleOnly' => array(
	            'condition' => $this->getTableAlias(true).".`maxvalues` <> 1",
	        ),
	        // все настройки, содержащие только одно значение (текст, число или select с одним варианом)
	        'singleOnly' => array(
	            'condition' => $this->getTableAlias(true).".`maxvalues` = 0",
	        ),
	        // обязательные настройки (должны быть заполнены)
	        'requiredOnly' => array(
	            'condition' => $this->getTableAlias(true).".`minvalues` > 0",
	        ),
	        // необязательные настройки
	        'optionalOnly' => array(
	            'condition' => $this->getTableAlias(true).".`minvalues` = 0",
	        ),
	        // системные настройки (не привязаны к моделям, управляют поведением сайта в целом)
	        'systemOnly' => array(
	            'scopes' => array(
	                'forObject' => array('system', 0),
	            ),
	        ),
	        // настройки у которых предусмотрен набор вариантов для выбора значения
	        // (не важно, пустой или нет)
	        'hasDefaultOptions' => array(
	            'condition' => $this->getTableAlias(true).".`easylistid` <> 0",
	        ),
	        // @todo настройки у которых есть значения по умолчанию
	        /*'hasDefaultValues' => array(
	            'scopes' => array(
	                '' => array('', 0),
	            ),
	        ),*/
	        // @todo hasUserOptions
	        // отредактированые записи
	        'modified' => array(
	            'condition' => $this->getTableAlias(true).".`timemodified` > 0",
	        ),
	        // никогда не редактировавшиеся записи
	        'neverModified' => array(
	            'condition' => $this->getTableAlias(true).".`timemodified` = 0",
	        ),
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
		$criteria = new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('easylistid',$this->easylistid,true);
		$criteria->compare('parentid',$this->parentid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('minvalues',$this->minvalues,true);
		$criteria->compare('maxvalues',$this->maxvalues,true);
		$criteria->compare('objecttype',$this->objecttype,true);
		$criteria->compare('objectid',$this->objectid,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}*/

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Config the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * Определить, является ли эта настройка системной (не относящейся ни к одной модели)
	 * @return bool
	 */
	public function isSystem()
	{
	    return $this->systemOnly()->exists();
	}
	
	/**
	 * Определить, может ли настройка содержать несколько значений (ConfigValue)
	 * @return bool
	 */
	public function isMultiple()
	{
	    return $this->multipleOnly()->exists();
	}
	
	/**
	 * Функция подготовки первого изменения значения или списка значений
	 * для этой настройки (ConfigValue): мы храним только те значения настроек которые
	 * отличаются от значений по умолчанию, поэтому при первом изменении значения любой настройки
	 * мы должны ее инициализировать
	 * Эта функция вызывается из модели ConfigValue перед сохранением редактированием или обновлением
	 *
	 * @param  string      $operation - действие производимое с настройкой (ConfigValue):
	 *                                  insert
	 *                                  update
	 *                                  delete
	 * @param  ConfigValue $configValue - создаваемое, обновляемое или удаляемое значение настройки
	 * @return bool
	 */
	public function beforeFirstEdit($operation, $configValue)
	{
	    if ( $this->timemodified OR ! $this->parentid OR $this->isSystem() )
	    {// это не первое редактирование настройки или настройка корневая/системная:
	        // не требуется никаких дополнительных операций подготовки при изменении ее записей
	        return true;
	    }
	    if ( ! $this->parentConfig )
	    {// проверяем целостность иерархии настроек
	        throw new CException('Невозможно изменить настройку: она ссылается на несуществующую
	            родительскую запись');
	    }
	    $this->timemodified = time();
	    
	    switch ( $operation )
	    {// определим какая операция выполняется
	        // добавляется новое значение настройки
	        case 'insert':
	            if ( $this->isMultiple() )
	            {// у настройки может быть несколько значений
	                
	            }else
	            {// у настройки может бытьтолько одно значение
	                
	            }
                if ( ! $this->parentConfig->configValues )
                {// в родительской настройке нет значений по умолчанию: она или не обязательна
                    // к заполнению или не имеет значений по умолчанию 
                    // создаем и сохраняем новую запись из переданных значений
                    
                }
	        break;
	        // обновляется текущее значение настройки
	        case 'update':
                
	        break;
	        // удаляется одно из добавленых значений настройки
	        case 'delete':
                
            break;
            default: throw new CException('Неизвестный тип операции с настройкой:'.$operation);
	    }
	    return $this->save();
	}
	
	/**
	 * Именованая группа условий: все записи привязанные к объекту определенного типа
	 *
	 * @param  string $objectType
	 * @param  int    $objectId
	 * @return Config
	 */
	public function forObject($objectType, $objectId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`objecttype`', $objectType);
	    $criteria->compare($this->getTableAlias(true).'.`objectid`', $objectId);
	
	    $this->getDbCriteria()->mergeWith($criteria);
	
	    return $this;
	}
	
	/**
	 * Именованая группа условий: все записи привязанные к нескольким объектам
	 *
	 * @param string $objectType
	 * @param array  $objectIds
	 * @return Config
	 */
	public function forObjects($objectType, $objectIds)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`objecttype`', $objectType);
	    $criteria->addInCondition($this->getTableAlias(true).'.`objectid`', $objectIds);
	
	    $this->getDbCriteria()->mergeWith($criteria);
	
	    return $this;
	}
	
	/**
	 * Именованая группа условий: получить все настройки прикрепленные к переданной модели 
	 *
	 * @param string|CActiveRecord $object - модель к которой прикреплены настройки 
	 *                                       или название класса такой модели если мо хотим получить
	 *                                       базовые настройки для всех моделей этого класса
	 * @return Config
	 */
	public function forModel($object)
	{
	    if ( is_object($object) )
	    {// передана модель целиком
	        $objectType = get_class($object);
	        $objectId   = $object->id;
	    }else
	    {// пердано только название класа
	        $objectType = $object;
	        $objectId   = 0;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`objecttype`', $objectType);
	    $criteria->compare($this->getTableAlias(true).'.`objectid`', $objectId);
	
	    $this->getDbCriteria()->mergeWith($criteria);
	
	    return $this;
	}
	
	/**
	 * Именованая группа условий: все настройки, использующие этот список вариантов значений
	 *
	 * @param  int|EasyList $easyList - список, на который ссылаются настройки   
	 * @return Config
	 */
	public function forEasyList($easyList)
	{
	    if ( is_object($easyList) )
	    {// передан объекта целиком
	        $id = $easyList->id;
	    }else
	    {
	        $id = $easyList;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`easylistid`', $id);
	
	    $this->getDbCriteria()->mergeWith($criteria);
	
	    return $this;
	}
	
	/**
	 * Именованая группа условий: все настройки, использующие список дополнительных значений настройки
	 *
	 * @param  int|EasyList $userList - список, на который ссылаются настройки   
	 * @return Config
	 */
	public function forUserList($userList)
	{
	    if ( is_object($userList) )
	    {// передан объекта целиком
	        $id = $userList->id;
	    }else
	    {
	        $id = $userList;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`userlistid`', $id);
	
	    $this->getDbCriteria()->mergeWith($criteria);
	
	    return $this;
	}
	
	/**
	 * Именованая группа условий: все записи c заданным названием (полное совпедение)
	 *
	 * @param  string $name - название настройки
	 * @return Config
	 */
	public function withName($name)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`name`', $name);
	
	    $this->getDbCriteria()->mergeWith($criteria);
	
	    return $this;
	}
	
	/**
	 * Именованая группа условий: 
	 *
	 * @param  string $objectType - тип связи либо название класса модели
	 * @return Config
	 */
	public function withObjectType($objectType)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`objecttype`', $objectType);
	
	    $this->getDbCriteria()->mergeWith($criteria);
	
	    return $this;
	}
	
	/**
	 * Именованая группа условий: 
	 *
	 * @param  int $objectId - id связанного объекта
	 * @return Config
	 */
	public function withObjectId($objectId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`objectid`', $objectId);
	
	    $this->getDbCriteria()->mergeWith($criteria);
	
	    return $this;
	}
	
	/**
	 * Именованая группа условий: все дочерние записи для указанной настройки
	 *
	 * @param  string $parentId - id родительской настройки
	 * @return Config
	 */
	public function withParentId($parentId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`parentid`', $parentId);
	
	    $this->getDbCriteria()->mergeWith($criteria);
	
	    return $this;
	}
	
	/**
	 * Именованая группа условий: все настройки с указанным значением в привязаном объекте значения
	 *
	 * @param  array|string $value - требуемое значение в поле value для связанной модели
	 * @return Config
	 * 
	 * @todo функция withLinkedValueId все настройки с указанным EasyListItem.id 
	 *       в привязаном объекте значения (получаем одно или несколько EasyListItem.id 
	 *       из стандартного (или пользовательского) списка занчений и ищем в своем 
	 *       списке ссылки на них)
	 */
	public function withLinkedValue($value)
	{
	    $criteria = new CDbCriteria();
	    if ( $this->isMultiple() )
	    {// настройка с множественным выбором всегда ссылается на список значений
	        $with = array(
	            'valueListObject' => array(
    	            'scopes' => array(
        	            'withValue' => array($value),
                    ),
                ),
	        );
	    }else
	    {// настройка с одиночным выбором всегда ссылается на значение в поле
	        /* @var $model CActiveRecord */
	        $model = $this->valuetype;
	        $alias = $model::model()->getTableAlias(true);
	        // @todo IN-условие если значений несколько
	        // создаем условие поиска по значению связанного поля
	        $with = array(
	            'valueObject' => array(
                    'condition' => "{$alias}.`{$this->valuefield}` = '{$value}'",
    	        ),
    	    );
	        // @todo проверить вариант с $criteria
	    }
	    $criteria->with = $with;
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
	/**
	 * Именованая группа условий: все настройки с указанным значением 
	 * (в привязаном объекте значения)
	 *
	 * @param  array $values - требуемые значения настройки (масив строк, ищется любое из указанных)
	 *                         ключами в массиве могут быть типы значений - тогда
	 *                         это условие будет учитывать их при поиске
	 * @return Config
	 */
	/*public function withValues($values)
	{
	    
	}*/
}