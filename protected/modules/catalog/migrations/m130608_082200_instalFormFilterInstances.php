<?php

/**
 * Миграция устанавливающая первые доступные фильтры в каталог
 */
class m130608_082200_instalFormFilterInstances extends CDbMigration
{
    public function safeUp()
    {
        Yii::import('application.modules.catalog.models.*');
        
        // Получаем все разделы каталога кроме служебного корневого
        $sections = CatalogSection::model()->findAll('`id` > 1 ');
        
        foreach ( $sections as $section )
        {
            $this->installStandardFilters($section->id);
        }
        
        // Добавляем фильтры в большую форму поиска
        $this->installStandardFilters(0);
    }
    
    /**
     * Установить все доступные фильтры (пол, возраст, рост вес) в один раздел каталога
     * @param int $sectionId - id раздела каталога в который устанавливаются фильтры
     * @return null
     */
    protected function installStandardFilters($sectionId)
    {
        
        $instance = new CatalogFilterInstance();
        $instance->sectionid = $sectionId;
        $instance->filterid = 1;
        $instance->save();
        unset($instance);
        
        $instance = new CatalogFilterInstance();
        $instance->sectionid = $sectionId;
        $instance->filterid = 3;
        $instance->save();
        unset($instance);
        
        $instance = new CatalogFilterInstance();
        $instance->sectionid = $sectionId;
        $instance->filterid = 2;
        $instance->save();
        unset($instance);
        
        $instance = new CatalogFilterInstance();
        $instance->sectionid = $sectionId;
        $instance->filterid = 4;
        $instance->save();
        unset($instance);
    }
}