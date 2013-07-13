<?php

/**
 * Добавление вкладок в разделы каталога
 * (только вкладки, фильтры поиска следующей миграцией)
 */
class m130421_212300_installCatalogTabs extends CDbMigration
{
    protected $MySqlOptions = "ENGINE=InnoDB CHARSET=utf8";
    
    /**
     * @var id модели Questionary в таблицe search_scope_models
     */
    const QID = 1;
    
    public function safeUp()
    {
        Yii::import('application.modules.catalog.CatalogModule');
        Yii::import('application.modules.catalog.models.*');
        
        // Привязываем ранее созданные вкладки к разделам
        
        // получаем из базы ранее созданные вкладки
        $menTab = CatalogTab::model()->find("shortname = 'men'");
        $womenTab = CatalogTab::model()->find("shortname = 'women'");
        $childrenTab = CatalogTab::model()->find("shortname = 'children'");
        
        // перебираем все разделы и привязываем к ним вкладки
        
        // Вся база (нельзя, содержит только разделы - придется воротить каколй-то пиздец в коже отображения разделов)
        // $section = 'all_base';
        
        // Медийные актеры
        $this->assignTabs('media_actors', array($menTab, $womenTab, $childrenTab));
        
        // Модели
        $this->assignTabs('models', array($menTab, $womenTab, $childrenTab));
        
        // Профессиональные актеры
        $this->assignTabs('professional_actors', array($menTab, $womenTab, $childrenTab));
        
        // Дети (там фильтры те же, но названия вкладок другие)
        $childrenSection = CatalogSection::model()->find("shortname = 'children_section'");
        
        $tabInstance = new CatalogTabInstance();
        $tabInstance->sectionid = $childrenSection->id;
        $tabInstance->tabid     = $menTab->id;
        $tabInstance->lang      = 'ru';
        $tabInstance->newname   = 'Мальчики';
        $tabInstance->visible   = 1;
        $tabInstance->parentid  = 0;
        $tabInstance->save();
        unset($tabInstance);
        
        $tabInstance = new CatalogTabInstance();
        $tabInstance->sectionid = $childrenSection->id;
        $tabInstance->tabid     = $womenTab->id;
        $tabInstance->lang      = 'ru';
        $tabInstance->newname   = 'Девочки';
        $tabInstance->visible   = 1;
        $tabInstance->parentid  = 0;
        $tabInstance->save();
        unset($tabInstance);
        
        
        // актеры-студенты и выпускники
        $this->assignTabs('student_actors', array($menTab, $womenTab));
        
        // атлеты
        $this->assignTabs('athletes', array($menTab, $womenTab));
        
        // ведущие
        $this->assignTabs('emcees', array($menTab, $womenTab, $childrenTab));
        
        // певцы / певицы
        $this->assignTabs('singers', array($menTab, $womenTab, $childrenTab));
        
        // музыканты
        $this->assignTabs('musicians', array($menTab, $womenTab, $childrenTab));
        
        // танцоры
        $this->assignTabs('dancers', array($menTab, $womenTab, $childrenTab));
        
        // Близнецы
        $this->assignTabs('doubles', array($menTab, $womenTab, $childrenTab));
        
        // Двойники
        $this->assignTabs('twins', array($menTab, $womenTab, $childrenTab));
        
        // Непрофессиональные актеры
        $this->assignTabs('nopro_actors', array($menTab, $womenTab, $childrenTab));
        
        // маленькие люди
        $this->assignTabs('small_people', array($menTab, $womenTab));
        
        // Статисты
        $this->assignTabs('statists', array($menTab, $womenTab, $childrenTab));
        
        // Артисты массовых сцен
        $this->assignTabs('mass_actors', array($menTab, $womenTab, $childrenTab));
    }
    
    protected function assignTabs($sectionName, $tabs)
    {
        $section = CatalogSection::model()->find("shortname = '{$sectionName}'");
        foreach ( $tabs as $tab )
        {
            $tabInstance = new CatalogTabInstance();
            $tabInstance->sectionid = $section->id;
            $tabInstance->tabid     = $tab->id;
            $tabInstance->lang      = 'ru';
            $tabInstance->visible   = 1;
            $tabInstance->parentid  = 0;
            $tabInstance->save();
        }
    }
}