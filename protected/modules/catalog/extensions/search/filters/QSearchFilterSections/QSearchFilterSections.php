<?php

/**
 * Фильтр поиска, в котором можно выбрать в каких разделах искать
 * Если не выбрано ни одного раздела - происходит поиск по всей базе
 * 
 * @todo сделать переключатель "вся база/только выбранные"
 * @todo Сделать поиск в подразделах
 */
class QSearchFilterSections extends QSearchFilterBaseSelect2
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('sections');
    
    /**
     * (non-PHPdoc)
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "Разделы каталога";
    }
    
    /**
     * (non-PHPdoc)
     * @see QSearchFilterBaseSelect2::getMenuVariants()
     */
    protected function getMenuVariants()
    {
        $variants = array();
        
        $sections = $this->getCatalogSections();
        foreach ( $sections as $section )
        {
            $variants[$section->id] = $section->name;
        }
        
        return $variants;
    }
    
    /**
     * Получить список разделов каталога, в которых можно искать
     * @return array
     */
    protected function getCatalogSections()
    {
        $criteria = new CDbCriteria();
        $criteria->compare('parentid', 1);
        $criteria->compare('visible', 1);
        $criteria->compare('content', 'users');
        $criteria->select = '`id`, `name`';
        $criteria->order = '`name` ASC';
        
        return CatalogSection::model()->findAll($criteria);
    }
}