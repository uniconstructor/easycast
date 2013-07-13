<?php

/**
 * Миграция, устанавливающая все основные фильтры поиска во все разделы
 */
class m130624_205600_installBaseFilters extends CDbMigration
{
    public function safeUp()
    {
        Yii::import('application.modules.catalog.CatalogModule');
        Yii::import('application.modules.catalog.models.*');
        Yii::import('application.extensions.ESearchScopes.models.*');
        Yii::import('application.extensions.ESearchScopes.ESearchScopes');
        
        // удаляем старые фильтры 
        $this->clearOldFilters();
        
        // Устанавливаем новые фильтры
        $this->installAllFilters();
        
        // распихиваем фильтры по разделам
        
        // медийные актеры
        $this->bindFilter('media_actors', 'name', 1);
        $this->bindFilter('media_actors', 'age', 2);
        $this->bindFilter('media_actors', 'gender', 3);
        
        // модели
        $this->bindFilter('models', 'gender', 1);
        $this->bindFilter('models', 'age', 2);
        $this->bindFilter('models', 'modeltype', 3);
        $this->bindFilter('models', 'haircolor', 4);
        $this->bindFilter('models', 'looktype', 5);
        $this->bindFilter('models', 'nativecountryid', 6);
        $this->bindFilter('models', 'titsize', 7);
        $this->bindFilter('models', 'height', 8);
        $this->bindFilter('models', 'weight', 9);
        $this->bindFilter('models', 'wearsize', 10);
        $this->bindFilter('models', 'shoessize', 11);
        $this->bindFilter('models', 'hairlength', 12);
        $this->bindFilter('models', 'body', 13);
        $this->bindFilter('models', 'tatoo', 14);
        $this->bindFilter('models', 'striptease', 15);
        
        // проф. актеры
        $this->bindFilter('professional_actors', 'gender', 1);
        $this->bindFilter('professional_actors', 'age', 2);
        $this->bindFilter('professional_actors', 'looktype', 3);
        $this->bindFilter('professional_actors', 'nativecountryid', 4);
        $this->bindFilter('professional_actors', 'height', 5);
        $this->bindFilter('professional_actors', 'weight', 6);
        $this->bindFilter('professional_actors', 'haircolor', 7);
        $this->bindFilter('professional_actors', 'hairlength', 8);
        $this->bindFilter('professional_actors', 'wearsize', 9);
        $this->bindFilter('professional_actors', 'shoessize', 10);
        $this->bindFilter('professional_actors', 'titsize', 11);
        $this->bindFilter('professional_actors', 'tatoo', 12);
        $this->bindFilter('professional_actors', 'dancer', 13);
        $this->bindFilter('professional_actors', 'voicetype', 14);
        $this->bindFilter('professional_actors', 'voicetimbre', 15);
        $this->bindFilter('professional_actors', 'instrument', 16);
        $this->bindFilter('professional_actors', 'striptease', 17);
        $this->bindFilter('professional_actors', 'language', 18);
        $this->bindFilter('professional_actors', 'driver', 19);
        
        // дети
        $this->bindFilter('children_section', 'gender', 1);
        $this->bindFilter('children_section', 'age', 2);
        $this->bindFilter('children_section', 'looktype', 3);
        $this->bindFilter('children_section', 'nativecountryid', 4);
        $this->bindFilter('children_section', 'height', 5);
        $this->bindFilter('children_section', 'weight', 6);
        $this->bindFilter('children_section', 'haircolor', 7);
        $this->bindFilter('children_section', 'hairlength', 8);
        $this->bindFilter('children_section', 'wearsize', 9);
        $this->bindFilter('children_section', 'shoessize', 10);
        $this->bindFilter('children_section', 'modeltype', 11);
        $this->bindFilter('children_section', 'dancer', 12);
        $this->bindFilter('children_section', 'voicetype', 13);
        $this->bindFilter('children_section', 'voicetimbre', 14);
        $this->bindFilter('children_section', 'instrument', 15);
        $this->bindFilter('children_section', 'sporttype', 16);
        $this->bindFilter('children_section', 'extremaltype', 17);
        
        // выпускники
        $this->bindFilter('student_actors', 'actoruniversities', 1);
        $this->bindFilter('student_actors', 'gender', 2);
        $this->bindFilter('student_actors', 'age', 3);
        $this->bindFilter('student_actors', 'looktype', 4);
        $this->bindFilter('student_actors', 'nativecountryid', 5);
        $this->bindFilter('student_actors', 'height', 6);
        $this->bindFilter('student_actors', 'weight', 7);
        $this->bindFilter('student_actors', 'haircolor', 8);
        $this->bindFilter('student_actors', 'hairlength', 9);
        $this->bindFilter('student_actors', 'wearsize', 10);
        $this->bindFilter('student_actors', 'shoessize', 11);
        $this->bindFilter('student_actors', 'titsize', 12);
        $this->bindFilter('student_actors', 'tatoo', 13);
        $this->bindFilter('student_actors', 'dancer', 14);
        $this->bindFilter('student_actors', 'voicetype', 15);
        $this->bindFilter('student_actors', 'voicetimbre', 16);
        $this->bindFilter('student_actors', 'instrument', 17);
        $this->bindFilter('student_actors', 'striptease', 18);
        $this->bindFilter('student_actors', 'language', 19);
        $this->bindFilter('student_actors', 'driver', 20);
        
        // атлеты
        $this->bindFilter('athletes', 'age', 1);
        $this->bindFilter('athletes', 'looktype', 2);
        $this->bindFilter('athletes', 'nativecountryid', 3);
        $this->bindFilter('athletes', 'height', 4);
        $this->bindFilter('athletes', 'weight', 5);
        $this->bindFilter('athletes', 'body', 6);
        $this->bindFilter('athletes', 'haircolor', 7);
        $this->bindFilter('athletes', 'hairlength', 8);
        $this->bindFilter('athletes', 'wearsize', 9);
        $this->bindFilter('athletes', 'shoessize', 10);
        $this->bindFilter('athletes', 'gender', 11);
        $this->bindFilter('athletes', 'titsize', 12);
        $this->bindFilter('athletes', 'tatoo', 13);
        $this->bindFilter('athletes', 'dancer', 14);
        $this->bindFilter('athletes', 'voicetype', 15);
        $this->bindFilter('athletes', 'voicetimbre', 16);
        $this->bindFilter('athletes', 'instrument', 17);
        $this->bindFilter('athletes', 'striptease', 18);
        $this->bindFilter('athletes', 'language', 19);
        $this->bindFilter('athletes', 'driver', 20);
        
        // ведущие
        $this->bindFilter('emcees', 'mctype', 1);
        $this->bindFilter('emcees', 'gender', 2);
        $this->bindFilter('emcees', 'age', 3);
        $this->bindFilter('emcees', 'looktype', 4);
        $this->bindFilter('emcees', 'nativecountryid', 5);
        $this->bindFilter('emcees', 'height', 6);
        $this->bindFilter('emcees', 'weight', 7);
        $this->bindFilter('emcees', 'haircolor', 8);
        $this->bindFilter('emcees', 'hairlength', 9);
        $this->bindFilter('emcees', 'wearsize', 10);
        $this->bindFilter('emcees', 'shoessize', 11);
        $this->bindFilter('emcees', 'titsize', 12);
        $this->bindFilter('emcees', 'tatoo', 13);
        $this->bindFilter('emcees', 'dancer', 14);
        $this->bindFilter('emcees', 'voicetype', 15);
        $this->bindFilter('emcees', 'voicetimbre', 16);
        $this->bindFilter('emcees', 'instrument', 17);
        $this->bindFilter('emcees', 'language', 18);
        $this->bindFilter('emcees', 'driver', 19);
        $this->bindFilter('emcees', 'sporttype', 20);
        
        // певцы
        $this->bindFilter('singers', 'voicetype', 1);
        $this->bindFilter('singers', 'voicetimbre', 2);
        $this->bindFilter('singers', 'gender', 3);
        $this->bindFilter('singers', 'age', 4);
        $this->bindFilter('singers', 'looktype', 5);
        $this->bindFilter('singers', 'nativecountryid', 6);
        $this->bindFilter('singers', 'height', 7);
        $this->bindFilter('singers', 'weight', 8);
        $this->bindFilter('singers', 'haircolor', 9);
        $this->bindFilter('singers', 'hairlength', 10);
        $this->bindFilter('singers', 'wearsize', 11);
        $this->bindFilter('singers', 'shoessize', 12);
        $this->bindFilter('singers', 'titsize', 13);
        $this->bindFilter('singers', 'language', 14);
        
        // музыканты
        $this->bindFilter('musicians', 'instrument', 1);
        $this->bindFilter('musicians', 'gender', 2);
        $this->bindFilter('musicians', 'age', 3);
        $this->bindFilter('musicians', 'looktype', 4);
        $this->bindFilter('musicians', 'nativecountryid', 5);
        $this->bindFilter('musicians', 'height', 6);
        $this->bindFilter('musicians', 'weight', 7);
        $this->bindFilter('musicians', 'haircolor', 8);
        $this->bindFilter('musicians', 'hairlength', 9);
        $this->bindFilter('musicians', 'wearsize', 10);
        $this->bindFilter('musicians', 'shoessize', 11);
        $this->bindFilter('musicians', 'titsize', 12);
        $this->bindFilter('musicians', 'language', 13);
        
        // танцоры
        $this->bindFilter('dancers', 'dancer', 1);
        $this->bindFilter('dancers', 'gender', 2);
        $this->bindFilter('dancers', 'age', 3);
        $this->bindFilter('dancers', 'looktype', 4);
        $this->bindFilter('dancers', 'nativecountryid', 5);
        $this->bindFilter('dancers', 'height', 6);
        $this->bindFilter('dancers', 'weight', 7);
        $this->bindFilter('dancers', 'haircolor', 8);
        $this->bindFilter('dancers', 'hairlength', 9);
        $this->bindFilter('dancers', 'wearsize', 10);
        $this->bindFilter('dancers', 'shoessize', 11);
        $this->bindFilter('dancers', 'titsize', 12);
        $this->bindFilter('dancers', 'tatoo', 13);
        $this->bindFilter('dancers', 'striptease', 14);
        
        // близнецы
        $this->bindFilter('doubles', 'gender', 1);
        $this->bindFilter('doubles', 'age', 2);
        $this->bindFilter('doubles', 'looktype', 3);
        $this->bindFilter('doubles', 'nativecountryid', 4);
        $this->bindFilter('doubles', 'height', 6);
        $this->bindFilter('doubles', 'weight', 7);
        $this->bindFilter('doubles', 'haircolor', 8);
        $this->bindFilter('doubles', 'hairlength', 9);
        $this->bindFilter('doubles', 'wearsize', 10);
        $this->bindFilter('doubles', 'shoessize', 11);
        $this->bindFilter('doubles', 'titsize', 12);
        $this->bindFilter('doubles', 'dancer', 13);
        $this->bindFilter('doubles', 'voicetype', 14);
        $this->bindFilter('doubles', 'voicetimbre', 15);
        $this->bindFilter('doubles', 'language', 16);
        $this->bindFilter('doubles', 'driver', 17);
        
        // двойники
        $this->bindFilter('twins', 'gender', 1);
        $this->bindFilter('twins', 'age', 2);
        $this->bindFilter('twins', 'height', 3);
        $this->bindFilter('twins', 'weight', 4);
        $this->bindFilter('twins', 'wearsize', 5);
        $this->bindFilter('twins', 'shoessize', 6);
        $this->bindFilter('twins', 'language', 7);
        $this->bindFilter('twins', 'driver', 8);
        
        // непроф. актеры
        $this->bindFilter('nopro_actors', 'gender', 1);
        $this->bindFilter('nopro_actors', 'age', 2);
        $this->bindFilter('nopro_actors', 'looktype', 3);
        $this->bindFilter('nopro_actors', 'nativecountryid', 4);
        $this->bindFilter('nopro_actors', 'height', 5);
        $this->bindFilter('nopro_actors', 'weight', 6);
        $this->bindFilter('nopro_actors', 'haircolor', 7);
        $this->bindFilter('nopro_actors', 'hairlength', 8);
        $this->bindFilter('nopro_actors', 'wearsize', 9);
        $this->bindFilter('nopro_actors', 'shoessize', 10);
        $this->bindFilter('nopro_actors', 'titsize', 11);
        $this->bindFilter('nopro_actors', 'tatoo', 12);
        $this->bindFilter('nopro_actors', 'dancer', 13);
        $this->bindFilter('nopro_actors', 'voicetype', 14);
        $this->bindFilter('nopro_actors', 'voicetimbre', 15);
        $this->bindFilter('nopro_actors', 'instrument', 16);
        $this->bindFilter('nopro_actors', 'striptease', 17);
        $this->bindFilter('nopro_actors', 'language', 18);
        $this->bindFilter('nopro_actors', 'driver', 19);
        
        // маленькие люди
        $this->bindFilter('small_people', 'gender', 1);
        $this->bindFilter('small_people', 'age', 2);
        $this->bindFilter('small_people', 'height', 3);
        $this->bindFilter('small_people', 'looktype', 4);
        $this->bindFilter('small_people', 'nativecountryid', 5);
        $this->bindFilter('small_people', 'haircolor', 6);
        $this->bindFilter('small_people', 'hairlength', 7);
        $this->bindFilter('small_people', 'wearsize', 8);
        $this->bindFilter('small_people', 'shoessize', 9);
        $this->bindFilter('small_people', 'titsize', 10);
        $this->bindFilter('small_people', 'dancer', 11);
        $this->bindFilter('small_people', 'voicetype', 12);
        $this->bindFilter('small_people', 'voicetimbre', 13);
        $this->bindFilter('small_people', 'instrument', 14);
        $this->bindFilter('small_people', 'language', 18);
        $this->bindFilter('small_people', 'driver', 19);
        
        // статисты
        $this->bindFilter('statists', 'gender', 1);
        $this->bindFilter('statists', 'age', 2);
        $this->bindFilter('statists', 'looktype', 3);
        $this->bindFilter('statists', 'nativecountryid', 4);
        $this->bindFilter('statists', 'height', 5);
        $this->bindFilter('statists', 'weight', 6);
        $this->bindFilter('statists', 'haircolor', 7);
        $this->bindFilter('statists', 'hairlength', 8);
        $this->bindFilter('statists', 'wearsize', 9);
        $this->bindFilter('statists', 'shoessize', 10);
        $this->bindFilter('statists', 'titsize', 11);
        $this->bindFilter('statists', 'tatoo', 12);
        $this->bindFilter('statists', 'dancer', 13);
        $this->bindFilter('statists', 'voicetype', 14);
        $this->bindFilter('statists', 'voicetimbre', 15);
        $this->bindFilter('statists', 'instrument', 16);
        $this->bindFilter('statists', 'striptease', 17);
        $this->bindFilter('statists', 'language', 18);
        $this->bindFilter('statists', 'driver', 19);
        
        // актеры массовых сцен
        $this->bindFilter('mass_actors', 'gender', 1);
        $this->bindFilter('mass_actors', 'age', 2);
        $this->bindFilter('mass_actors', 'looktype', 3);
        $this->bindFilter('mass_actors', 'nativecountryid', 4);
        $this->bindFilter('mass_actors', 'height', 5);
        $this->bindFilter('mass_actors', 'weight', 6);
        $this->bindFilter('mass_actors', 'haircolor', 7);
        $this->bindFilter('mass_actors', 'hairlength', 8);
        $this->bindFilter('mass_actors', 'wearsize', 9);
        $this->bindFilter('mass_actors', 'shoessize', 10);
    }
    
    protected function clearOldFilters()
    {
        $table = "{{catalog_filters}}";
        $this->truncateTable($table);
        
        $table = "{{catalog_filter_instances}}";
        $this->truncateTable($table);
    }
    
    protected function bindFilter($sectionName, $filterName, $order)
    {
        $table = "{{catalog_filter_instances}}";
        
        if ( ! $section = CatalogSection::model()->find('shortname = :shortname', array(':shortname' => $sectionName)) )
        {
            throw new CDbException($sectionName.' not found');
        }
        if ( ! $filter = CatalogFilter::model()->find('shortname = :shortname', array(':shortname' => $filterName)) )
        {
            throw new CDbException($filterName.' not found');
        }

        $this->insert($table, array(
            'sectionid' => $section->id,
            'filterid'  => $filter->id,
            'visible'   => 1,
            'order'     => $order
        ));
    }
    
    protected function installAllFilters()
    {
        $table = "{{catalog_filters}}";
        
        $this->insert($table, array(
            'shortname'    => 'gender',
            'widgetclass'  => 'QSearchFilterGender',
            'handlerclass' => 'QSearchHandlerGender',
            'name'         => 'Пол',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'height',
            'widgetclass'  => 'QSearchFilterHeight',
            'handlerclass' => 'QSearchHandlerHeight',
            'name'         => 'Рост',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'age',
            'widgetclass'  => 'QSearchFilterAge',
            'handlerclass' => 'QSearchHandlerAge',
            'name'         => 'Возраст',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'weight',
            'widgetclass'  => 'QSearchFilterWeight',
            'handlerclass' => 'QSearchHandlerWeight',
            'name'         => 'Вес',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'looktype',
            'widgetclass'  => 'QSearchFilterLookType',
            'handlerclass' => 'QSearchHandlerLookType',
            'name'         => 'Тип внешности',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'haircolor',
            'widgetclass'  => 'QSearchFilterHairColor',
            'handlerclass' => 'QSearchHandlerHairColor',
            'name'         => 'Цвет волос',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'eyecolor',
            'widgetclass'  => 'QSearchFilterEyeColor',
            'handlerclass' => 'QSearchHandlerEyeColor',
            'name'         => 'Цвет глаз',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'hairlength',
            'widgetclass'  => 'QSearchFilterHairLen',
            'handlerclass' => 'QSearchHandlerHairLen',
            'name'         => 'Длина волос',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'titsize',
            'widgetclass'  => 'QSearchFilterTitSize',
            'handlerclass' => 'QSearchHandlerTitSize',
            'name'         => 'Размер груди',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'physiquetype',
            'widgetclass'  => 'QSearchFilterPType',
            'handlerclass' => 'QSearchHandlerPType',
            'name'         => 'Телосложение',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'shoessize',
            'widgetclass'  => 'QSearchFilterShoesSize',
            'handlerclass' => 'QSearchHandlerShoesSize',
            'name'         => 'Размер обуви',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'nativecountryid',
            'widgetclass'  => 'QSearchFilterCountry',
            'handlerclass' => 'QSearchHandlerCountry',
            'name'         => 'Страна рождения',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'body',
            'widgetclass'  => 'QSearchFilterBody',
            'handlerclass' => 'QSearchHandlerBody',
            'name'         => 'Параметры тела',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'striptease',
            'widgetclass'  => 'QSearchFilterStrip',
            'handlerclass' => 'QSearchHandlerStrip',
            'name'         => 'Стриптиз',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'voicetimbre',
            'widgetclass'  => 'QSearchFilterVTimbre',
            'handlerclass' => 'QSearchHandlerVTimbre',
            'name'         => 'Тембр голоса',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'voicetype',
            'widgetclass'  => 'QSearchFilterVType',
            'handlerclass' => 'QSearchHandlerVType',
            'name'         => 'Тип вокала',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'instrument',
            'widgetclass'  => 'QSearchFilterMTool',
            'handlerclass' => 'QSearchHandlerMTool',
            'name'         => 'Музыкальный инструмент',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'mctype',
            'widgetclass'  => 'QSearchFilterMCType',
            'handlerclass' => 'QSearchHandlerMCType',
            'name'         => 'Специализация ведущего',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'modeltype',
            'widgetclass'  => 'QSearchFilterModel',
            'handlerclass' => 'QSearchHandlerModel',
            'name'         => 'Модель',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'name',
            'widgetclass'  => 'QSearchFilterName',
            'handlerclass' => 'QSearchHandlerName',
            'name'         => 'Имя',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'driver',
            'widgetclass'  => 'QSearchFilterDriver',
            'handlerclass' => 'QSearchHandlerDriver',
            'name'         => 'Водительские права',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'dancer',
            'widgetclass'  => 'QSearchFilterDancer',
            'handlerclass' => 'QSearchHandlerDancer',
            'name'         => 'Танцор',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'language',
            'widgetclass'  => 'QSearchFilterLang',
            'handlerclass' => 'QSearchHandlerLang',
            'name'         => 'Иностранный язык',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'sporttype',
            'widgetclass'  => 'QSearchFilterSport',
            'handlerclass' => 'QSearchHandlerSport',
            'name'         => 'Виды спорта',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'extremaltype',
            'widgetclass'  => 'QSearchFilterExtreme',
            'handlerclass' => 'QSearchHandlerExtreme',
            'name'         => 'Экстремальный спорт',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'musicuniversities',
            'widgetclass'  => 'QSearchFilterMUni',
            'handlerclass' => 'QSearchHandlerMUni',
            'name'         => 'Музыкальное образование',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'actoruniversities',
            'widgetclass'  => 'QSearchFilterAUni',
            'handlerclass' => 'QSearchHandlerAUni',
            'name'         => 'Театральное образование',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'tatoo',
            'widgetclass'  => 'QSearchFilterTatoo',
            'handlerclass' => 'QSearchHandlerTatoo',
            'name'         => 'Татуировки',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'theatres',
            'widgetclass'  => 'QSearchFilterTheatre',
            'handlerclass' => 'QSearchHandlerTheatre',
            'name'         => 'Работа в театре',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'wearsize',
            'widgetclass'  => 'QSearchFilterWearSize',
            'handlerclass' => 'QSearchHandlerWearSize',
            'name'         => 'Размер одежды',
        ));
        
        $this->insert($table, array(
            'shortname'    => 'playage',
            'widgetclass'  => 'QSearchFilterPlayAge',
            'handlerclass' => 'QSearchHandlerPlayAge',
            'name'         => 'Игровой возраст',
        ));
        
    }
}