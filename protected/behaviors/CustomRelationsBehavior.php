<?php

/**
 * Этот класс позволяет динамически добавлять связи (realtions) в модель в зависимости
 * от данных которые в ней хранятся
 * Используется всеми классами поведения которые работают с условиями поиска
 * 
 * Если связь уже добавлена в модель - она больше не подгружается
 * Добавление каждой новой связи в класс модели требует обновления ее метаданных
 * 
 * Большинство JOIN-запросов в именованных группах условий требуют параметра together
 * при выполнении, более подробно об этом можно прочитать здесь:
 * @see http://www.yiiframework.com/wiki/280/1-n-relations-sometimes-require-cdbcriteria-together/
 * 
 * @property CActiveRecord $owner
 */
class CustomRelationsBehavior extends CActiveRecordBehavior
{
    /**
     * @var array - массив с дополнительными связями (relations) для owner-модели
     */
    public $customRelations = array();
    
    /**
     * Связи по умолчанию: они могут быть у любой модели
     * 
     * @return array
     * 
     * @todo configItems - все настройки ссылающиеся на эту модель за значением
     * @todo comments    - все связанные комментарии
     */
    protected function getDefaultRelations()
    {
        return array(
            // все элементы списка, содержащие эту модель
            'listItems' => array(
                CActiveRecord::HAS_MANY,
                'EasyListItem',
                'objectid',
                'scopes' => array(
                    'withObjectType' => array(get_class($this->owner)),
                ),
            ),
            // все настройки, прикрепленные к этой модели
            'configParams' => array(
                CActiveRecord::HAS_MANY,
                'Config',
                'objectid',
                'scopes' => array(
                    'withObjectType' => array(get_class($this->owner)),
                ),
            ),
        );
    }
    
    /**
     * @see CActiveRecord::relations()
     *
     * @todo создавать исключение если связи с такими именами в модели уже есть
     */
    public function relations()
    {
        // добавляем к стандартным связям дополнительные
        $this->customRelations = CMap::mergeArray($this->getDefaultRelations(), $this->customRelations);
        // получаем существующие связи модели
        $modelRelations = $this->owner->relations();
        // возвращаем список в котором совмещены связи модели ниши связи через составно внешний ключ
        return CMap::mergeArray($modelRelations, $this->customRelations);
    }
    
    /**
     * 
     * @return array
     */
    public function getCustomRelations()
    {
        return $this->customRelations;
    }
    
    /**
     * 
     * @param  array $relations
     * @return void
     * 
     * @todo обновлять метаданные только если набор связей действительно изменился
     */
    public function setCustomRelations($relations)
    {
        $this->customRelations = $relations;
        // обновляем метаданные таблицы чтобы заработали новые связи
        $this->owner->refreshMetaData();
    }
    
    /**
     * Добавить список связей к модели
     * 
     * @param  array  $relations
     * @param  string $refresh
     * @param  string $replace
     * @return void
     */
    public function addCustomRelations($relations, $refresh=true, $replace=false)
    {
        foreach ( $relations as $name => $data )
        {
            $this->addCustomRelation($name, $data, false, $replace);
        }
        if ( $refresh )
        {// обновляем метаданные таблицы чтобы заработали новые связи
            $this->owner->refreshMetaData();
        }
    }
    
    /**
     * Добавить одну связь к модели
     * 
     * @param  string $name
     * @param  array  $data
     * @param  bool   $refresh
     * @param  bool   $replace
     * @return void
     */
    public function addCustomRelation($name, $data, $refresh=true, $replace=false)
    {
        if ( isset($this->customRelations[$name]) AND ! $replace )
        {// связь уже есть и заменять ее не нужно
            return;
        }
        // добавляем связь к общему списку
        $this->customRelations[$name] = $data;
        if ( $refresh )
        {// нужно обновить метаданные модели чтобы добавленные связи заработали 
            // (их недостаточно просто добавить в relations())
            if ( ! $this->owner->hasRelated($name) OR $replace )
            {
                $this->owner->refreshMetaData();
            }
        }
    }
}