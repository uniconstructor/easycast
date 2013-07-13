<?php

/**
 * Работа со значениями по умолчанию
 */
class QManageDefaultValuesBehavior extends CActiveRecordBehavior
{
    /**
     * Получить стандартное значение для отображения пользовтелю
     * @param unknown $type
     * @param unknown $value
     * @return unknown
     * unknown
     */
    public function getDefaultValueForDisplay($type=null, $value=null)
    {
        if ( ! $type )
        {
            $type = $this->owner->type;
        }
        if ( ! $value )
        {
            $value = $this->owner->value;
        }
        
        $condition = '`name`=:name AND `value`=:value AND `language`=:lang';
        $params    = array(':name' => $type, ':value' => $value, ':lang' => Yii::app()->language);
        
        if ( $default = QActivityType::model()->find($condition, $params) )
        {
            return $default->translation;
        }
        
        return $value;
    }
    
    /**
     * Получить список значений по умолчанию для отображения выпадающего меню
     * @param string $field - поле формы для которого получаются значения
     * @param bool $chooseElement - добавлять ли в начало пункт "выбрать"
     * @return array
     */
    public function getFieldVariants($field, $chooseElement=true)
    {
        $defaults  = array();
        
        $criteria  = new CDbCriteria();
        $criteria->compare('`name`', $field);
        $criteria->compare('`language`', Yii::app()->language);
        $criteria->order = '`translation` ASC';
        
        //$criteria->condition = "`name`=:name AND `language`=:lang";
        //$criteria->params = array(':name' => $field, ':lang' => Yii::app()->language);
        
        
        if ( $chooseElement )
        {
            $defaults[Questionary::VALUE_NOT_SET] = Yii::t('coreMessages','choose');
        }
        
        if ( $variants = QActivityType::model()->findAll($criteria) )
        {
            foreach ( $variants as $variant )
            {
                $defaults[$variant->value] = $variant->translation;
            }
        }else
       {
            return array(Questionary::VALUE_NOT_SET => 'error');
        }
        
        return $defaults;
    }
    
    /**
     * определить, является ли переданный экземпляр сложного значения стандартным
     * @param unknown_type $type
     * @param unknown_type $value
     */
    public function isDefaultValue($type, $value)
    {
        $variants = $this->getFieldVariants($type, false);
        if ( ! isset($variants[$value]) )
        {
            return false;
        }
        return true;
    }
}