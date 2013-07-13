<?php

/**
 * Установка всех разделов каталога
 */
class m130403_113500_installCatalogSections extends CDbMigration
{
    protected $MySqlOptions = "ENGINE=InnoDB CHARSET=utf8";

    /**
     * @var id модели Questionary в таблицe search_scope_models
     */
    const QID = 1;

    public function safeUp()
    {
        Yii::import('application.modules.catalog.CatalogModule');
        Yii::import('application.modules.questionary.QuestionaryModule');
        // Импортируем классы для работы с условиями поиска
        Yii::import(Yii::app()->getModule('catalog')->searchScopesPath.'.models.*');
        Yii::import(Yii::app()->getModule('catalog')->searchScopesPath.'.ESearchScopes');


        ////////////////////
        // Создаем таблиц //
        ////////////////////

        // Устанавливаем таблицу разделов каталога
        $table = "{{catalog_sections}}";
        $fields = array(
            "id" => "pk",
            "parentid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "scopeid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "name" => "varchar(128) NOT NULL",
            "shortname" => "varchar(128) NOT NULL",
            "lang" => "varchar(5) DEFAULT 'ru'",
            "galleryid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "content" => "enum('sections', 'users') NOT NULL",
            "order" => "int(6) UNSIGNED DEFAULT 0",
            "count" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "visible" => "tinyint(1) NOT NULL DEFAULT 1",
        );
        // Создаем таблицу и индексы к ней
        $this->createTable($table, $fields, $this->MySqlOptions);
        $this->_createIndexes($table, $fields);
        unset($table);
        unset($fields);


        // Устанавливаем таблицу вкладок для разделов
        $table = "{{catalog_tabs}}";
        $fields = array(
            "id" => "pk",
            "name" => "varchar(128) NOT NULL",
            "shortname" => "varchar(128) NOT NULL",
            "lang" => "varchar(5) DEFAULT 'ru'",
            "scopeid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
        );
        // Создаем таблицу и индексы к ней
        $this->createTable($table, $fields, $this->MySqlOptions);
        $this->_createIndexes($table, $fields);
        unset($table);
        unset($fields);


        // Устанавливаем таблицу привязки вкладок к разделам
        $table = "{{catalog_tab_instances}}";
        $fields = array(
            "id" => "pk",
            "sectionid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "parentid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "tabid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "newname" => "varchar(128) DEFAULT NULL",
            "lang" => "varchar(5) DEFAULT 'ru'",
            "visible" => "tinyint(1) NOT NULL DEFAULT 0",
        );
        // Создаем таблицу и индексы к ней
        $this->createTable($table, $fields, $this->MySqlOptions);
        $this->_createIndexes($table, $fields);
        unset($table);
        unset($fields);


        // Устанавливаем таблицу фильтров поиска
        $table = "{{catalog_filters}}";
        $fields = array(
            "id" => "pk",
            "field" => "varchar(128) NOT NULL",
            "name" => "varchar(255) DEFAULT NULL",
            "lang" => "varchar(5) DEFAULT 'ru'",
        );
        // Создаем таблицу и индексы к ней
        $this->createTable($table, $fields, $this->MySqlOptions);
        $this->_createIndexes($table, $fields);
        unset($table);
        unset($fields);

        // Устанавливаем таблицу привязки фильтров поиска к разделам каталога и форме поиска
        $table = "{{catalog_filter_instances}}";
        $fields = array(
            "id" => "pk",
            "sectionid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "filterid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "type" => "enum('filter', 'search') NOT NULL",
            "visible" => "tinyint(1) NOT NULL DEFAULT 1",
            "customvalues" => "tinyint(1) NOT NULL DEFAULT 0",
            "default" => "varchar(512) DEFAULT NULL",
        );
        // Создаем таблицу и индексы к ней
        $this->createTable($table, $fields, $this->MySqlOptions);
        $this->_createIndexes($table, $fields, array('default'));
        unset($table);
        unset($fields);

        // создаем запись о классе анкеты в таблице условий поиска
        $table = "{{search_scope_models}}";
        $this->insert($table, array('class' => 'Questionary'));
        unset($table);

        //////////////////////////////
        // Создаем вкладки каталога //
        //////////////////////////////
        
        // Общие вкладки каталога
        $table = "{{catalog_tabs}}";
        
        // "Вся база"
        // Условие поиска
        $allBaseScope = new SearchScope;
        $allBaseScope->type = CatalogModule::BASE_TAB_TYPE;
        $allBaseScope->name = 'Вся база';
        $allBaseScope->shortname = 'all_base';
        $allBaseScope->modelid = self::QID;
        $allBaseScope->save();
        
        $previousid = 0;
        // Добавим условия для категории: записи выводятся по рейтингу
        $previousid = $this->createRatingSortCondition($allBaseScope, $previousid);
        // Условие для сортировки по новизне (от новых к старым)
        $previousid = $this->createTimeSortCondition($allBaseScope, $previousid);
        // вкладка
        $allBaseTab = array('name'=>$allBaseScope->name, 'shortname'=>$allBaseScope->shortname, 'scopeid'=>$allBaseScope->id);
        $this->insert($table, $allBaseTab);
        //unset($scope);
        unset($previousid);
        
        
        // "Мужчины"
        // Условие поиска
        $menOnlyScope = new SearchScope;
        $menOnlyScope->type = CatalogModule::BASE_TAB_TYPE;
        $menOnlyScope->name = 'Мужчины';
        $menOnlyScope->shortname = 'men';
        $menOnlyScope->modelid = self::QID;
        $menOnlyScope->save();
        
        // Критерий поиска - только мужчины
        $previousid = 0;
        $condition = new ScopeCondition();
        $condition->scopeid = $menOnlyScope->id;
        $condition->type  = 'field';
        $condition->field = 'gender';
        $condition->comparison = 'equals';
        $condition->value = 'male';
        $condition->combine = 'and';
        $condition->previousid = $previousid;
        $condition->save();
        
        $previousid = $condition->id;
        // Условие сортировки: записи выводятся по рейтингу
        $previousid = $this->createRatingSortCondition($menOnlyScope, $previousid);
        // Условие для сортировки по новизне (от новых к старым)
        $previousid = $this->createTimeSortCondition($menOnlyScope, $previousid);
        // вкладка
        $menTab = array('name'=>'Мужчины', 'shortname'=>$menOnlyScope->shortname, 'scopeid'=>$menOnlyScope->id);
        $this->insert($table, $menTab);
        if (isset($condition)) unset($condition);
        unset($previousid);
        
        
        // "Женщины"
        // Условие поиска
        $scope = new SearchScope;
        $scope->type = CatalogModule::BASE_TAB_TYPE;
        $scope->name = 'Женщины';
        $scope->shortname = 'women';
        $scope->modelid = self::QID;
        $scope->save();
        
        // Критерий поиска - только женщины
        $previousid = 0;
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'field';
        $condition->field = 'gender';
        $condition->comparison = 'equals';
        $condition->value = 'female';
        $condition->combine = 'and';
        $condition->previousid = $previousid;
        $condition->save();
        
        $previousid = $condition->id;
        // Условие сортировки: записи выводятся по рейтингу
        $previousid = $this->createRatingSortCondition($scope, $previousid);
        // Условие для сортировки по новизне (от новых к старым)
        $previousid = $this->createTimeSortCondition($scope, $previousid);
        // вкладка
        $womenTab = array('name'=>'Женщины', 'shortname'=>$scope->shortname, 'scopeid'=>$scope->id);
        $this->insert($table, $womenTab);
        if (isset($condition)) unset($condition);
        unset($scope);
        unset($previousid);
        
        
        // "дети"
        // Условие поиска
        $scope = new SearchScope;
        $scope->type = CatalogModule::BASE_TAB_TYPE;
        $scope->name = 'Дети';
        $scope->shortname = 'children';
        $scope->modelid = self::QID;
        $scope->save();
        
        $previousid = 0;
        
        // Критерий поиска - только дети
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'field';
        $condition->field = 'birthdate';
        $condition->comparison = 'morethen';
        $condition->rawvalue = 1;
        $condition->value = "(UNIX_TIMESTAMP(NOW()) - (18 * 366 * 24 * 3600))";
        $condition->combine = 'and';
        $condition->previousid = $previousid;
        $condition->save();
        
        $previousid = $condition->id;
        unset($condition);
        
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'field';
        $condition->field = 'birthdate';
        $condition->comparison = 'lessthen';
        $condition->value = 'UNIX_TIMESTAMP(NOW())';
        $condition->combine = 'and';
        $condition->previousid = $previousid;
        $condition->save();
        
        $previousid = $condition->id;
        unset($condition);
        
        // Условие сортировки: записи выводятся по рейтингу
        $previousid = $this->createRatingSortCondition($scope, $previousid);
        // Условие для сортировки по новизне (от новых к старым)
        $previousid = $this->createTimeSortCondition($scope, $previousid);
        // вкладка
        $childrenTab = array('name'=>'Дети', 'shortname'=>$scope->shortname, 'scopeid'=>$scope->id);
        $this->insert($table, $childrenTab);
        
        if (isset($condition)) unset($condition);
        unset($scope);
        unset($previousid);
        
        unset($table);

        //////////////////////////////
        // Создаем разделы каталога //
        //////////////////////////////
        
        $table = "{{catalog_sections}}";
        
        // MAIN (безыменный раздел, который содержит все остальные разделы)
        // Нужен просто потому что дерево должно иметь корень
        // Критериев выборки не имеет, так как содержит только разделы
        
        $section = array(
            'id'        => 1,
            'name'      => 'Вся база',
            'shortname' => 'root_section',
            'scopeid'   => 0,
            'parentid'  => 0,
            'lang'      => 'ru',
            'content'   => 'sections',
            'order'     => 0,
        );
        
        $this->insert($table, $section);
        

        // Медийные актеры
        // Условие поиска
        $scope = new SearchScope;
        $scope->type = CatalogModule::BASE_TAB_TYPE;
        $scope->name = 'Медийные актеры';
        $scope->shortname = 'media_actors';
        $scope->modelid = self::QID;
        $scope->save();

        $previousid = 0;

        // Критерий поиска
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'field';
        $condition->field = 'ismediaactor';
        $condition->comparison = 'equals';
        $condition->value = '1';
        $condition->combine = 'and';
        $condition->previousid = $previousid;
        $condition->save();

        $previousid = $condition->id;
        unset($condition);

        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'sort';
        $condition->field = 'lastname';
        $condition->value = 'ASC';
        $condition->previousid = $previousid;
        $condition->save();

        $previousid = $condition->id;
        unset($condition);


        // Создаем раздел каталога
        $this->createSectionFromScope($scope);
        if (isset($scope)) unset($scope);
        if (isset($condition)) unset($condition);
        if (isset($previousid)) unset($previousid);

        // Модели
        // Условие поиска
        $scope = new SearchScope;
        $scope->type = CatalogModule::BASE_TAB_TYPE;
        $scope->name = 'Модели';
        $scope->shortname = 'models';
        $scope->modelid = self::QID;
        $scope->save();

        $previousid = 0;
        
        // Критерий поиска
        // модели
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'field';
        $condition->field = 'ismodel';
        $condition->comparison = 'equals';
        $condition->value = '1';
        $condition->combine = 'or';
        $condition->previousid = $previousid;
        $condition->save();
        
        $previousid = $condition->id;
        unset($condition);
        
        // фотомодели
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'field';
        $condition->field = 'isphotomodel';
        $condition->comparison = 'equals';
        $condition->value = '1';
        $condition->combine = 'or';
        $condition->previousid = $previousid;
        $condition->save();
        
        $previousid = $condition->id;
        unset($condition);
        
        // промо-модели
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'field';
        $condition->field = 'ispromomodel';
        $condition->comparison = 'equals';
        $condition->value = '1';
        $condition->combine = 'or';
        $condition->previousid = $previousid;
        $condition->save();
        
        $previousid = $condition->id;
        unset($condition);
        
        // сортировка по рейтингу
        $previousid = $this->createRatingSortCondition($scope, $previousid);
        // сортировка по новизне (от новых к старым)
        $previousid = $this->createTimeSortCondition($scope, $previousid);
        
        // Создаем раздел каталога
        $this->createSectionFromScope($scope);
        if (isset($scope)) unset($scope);
        if (isset($condition)) unset($condition);
        if (isset($previousid)) unset($previousid);

        // Профессиональные актеры
        // Условие поиска
        $scope = new SearchScope;
        $scope->type = CatalogModule::BASE_TAB_TYPE;
        $scope->name = 'Профессиональные актеры';
        $scope->shortname = 'professional_actors';
        $scope->modelid = self::QID;
        $scope->save();

        $previousid = 0;
        
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'field';
        $condition->field = 'isactor';
        $condition->comparison = 'equals';
        $condition->value = '1';
        $condition->combine = 'and';
        $condition->previousid = $previousid;
        $condition->save();
        
        $previousid = $condition->id;
        unset($condition);
        
        // сортировка по рейтингу
        $previousid = $this->createRatingSortCondition($scope, $previousid);
        // сортировка по новизне (от новых к старым)
        $previousid = $this->createTimeSortCondition($scope, $previousid);

        // Создаем раздел каталога
        $this->createSectionFromScope($scope);
        if (isset($scope)) unset($scope);
        if (isset($condition)) unset($condition);
        if (isset($previousid)) unset($previousid);

        // Дети
        // Условие поиска
        $scope = new SearchScope;
        $scope->type = CatalogModule::BASE_TAB_TYPE;
        $scope->name = 'Дети';
        $scope->shortname = 'children_section';
        $scope->modelid = self::QID;
        $scope->save();

        $previousid = 0;
        
        // Критерий поиска - только дети
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'field';
        $condition->field = 'birthdate';
        $condition->comparison = 'morethen';
        $condition->rawvalue = 1;
        $condition->value = "(UNIX_TIMESTAMP(NOW()) - (18 * 366 * 24 * 3600))";
        $condition->combine = 'and';
        $condition->previousid = $previousid;
        $condition->save();
        
        $previousid = $condition->id;
        unset($condition);
        
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'field';
        $condition->field = 'birthdate';
        $condition->comparison = 'lessthen';
        $condition->value = 'UNIX_TIMESTAMP(NOW())';
        $condition->combine = 'and';
        $condition->previousid = $previousid;
        $condition->save();
        
        $previousid = $condition->id;
        unset($condition);
        
        // сортировка по рейтингу
        $previousid = $this->createRatingSortCondition($scope, $previousid);
        // сортировка по новизне (от новых к старым)
        $previousid = $this->createTimeSortCondition($scope, $previousid);

        // Создаем раздел каталога
        $this->createSectionFromScope($scope);
        if (isset($scope)) unset($scope);
        if (isset($condition)) unset($condition);
        if (isset($previousid)) unset($previousid);

        // актеры-студенты и выпускники
        // Условие поиска
        $scope = new SearchScope;
        $scope->type = CatalogModule::BASE_TAB_TYPE;
        $scope->name = 'Актеры-студенты и выпускники';
        $scope->shortname = 'student_actors';
        $scope->modelid = self::QID;
        $scope->save();

        $previousid = 0;
        
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'field';
        $condition->field = 'isactor';
        $condition->comparison = 'equals';
        $condition->value = '1';
        $condition->combine = 'and';
        $condition->previousid = $previousid;
        $condition->save();
        
        $previousid = $condition->id;
        unset($condition);
        
        // сортировка по рейтингу
        $previousid = $this->createRatingSortCondition($scope, $previousid);
        // сортировка по новизне (от новых к старым)
        $previousid = $this->createTimeSortCondition($scope, $previousid);
        
        // Создаем раздел каталога
        $this->createSectionFromScope($scope);
        if (isset($scope)) unset($scope);
        if (isset($condition)) unset($condition);
        if (isset($previousid)) unset($previousid);

        // атлеты
        // Условие поиска
        $scope = new SearchScope;
        $scope->type = CatalogModule::BASE_TAB_TYPE;
        $scope->name = 'Атлеты';
        $scope->shortname = 'athletes';
        $scope->modelid = self::QID;
        $scope->save();

        $previousid = 0;
        
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'field';
        $condition->field = 'isathlete';
        $condition->comparison = 'equals';
        $condition->value = '1';
        $condition->combine = 'and';
        $condition->previousid = $previousid;
        $condition->save();
        
        $previousid = $condition->id;
        unset($condition);

        // сортировка по рейтингу
        $previousid = $this->createRatingSortCondition($scope, $previousid);
        // сортировка по новизне (от новых к старым)
        $previousid = $this->createTimeSortCondition($scope, $previousid);
        
        // Создаем раздел каталога
        $this->createSectionFromScope($scope);
        if (isset($scope)) unset($scope);
        if (isset($condition)) unset($condition);
        if (isset($previousid)) unset($previousid);

        // ведущие
        // Условие поиска
        $scope = new SearchScope;
        $scope->type = CatalogModule::BASE_TAB_TYPE;
        $scope->name = 'Ведущие';
        $scope->shortname = 'emcees';
        $scope->modelid = self::QID;
        $scope->save();

        $previousid = 0;
        
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'field';
        $condition->field = 'isemcee';
        $condition->comparison = 'equals';
        $condition->value = '1';
        $condition->combine = 'or';
        $condition->previousid = $previousid;
        $condition->save();
        
        $previousid = $condition->id;
        unset($condition);
        
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'field';
        $condition->field = 'istvshowmen';
        $condition->comparison = 'equals';
        $condition->value = '1';
        $condition->combine = 'or';
        $condition->previousid = $previousid;
        $condition->save();
        
        $previousid = $condition->id;
        unset($condition);

        // сортировка по рейтингу
        $previousid = $this->createRatingSortCondition($scope, $previousid);
        // сортировка по новизне (от новых к старым)
        $previousid = $this->createTimeSortCondition($scope, $previousid);
        
        // Создаем раздел каталога
        $this->createSectionFromScope($scope);
        if (isset($scope)) unset($scope);
        if (isset($condition)) unset($condition);
        if (isset($previousid)) unset($previousid);

        // певцы / певицы
        // Условие поиска
        $scope = new SearchScope;
        $scope->type = CatalogModule::BASE_TAB_TYPE;
        $scope->name = 'Певцы и певицы';
        $scope->shortname = 'singers';
        $scope->modelid = self::QID;
        $scope->save();

        $previousid = 0;
        
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'field';
        $condition->field = 'issinger';
        $condition->comparison = 'equals';
        $condition->value = '1';
        $condition->combine = 'and';
        $condition->previousid = $previousid;
        $condition->save();
        
        $previousid = $condition->id;
        unset($condition);

        // сортировка по рейтингу
        $previousid = $this->createRatingSortCondition($scope, $previousid);
        // сортировка по новизне (от новых к старым)
        $previousid = $this->createTimeSortCondition($scope, $previousid);
        
        // Создаем раздел каталога
        $this->createSectionFromScope($scope);
        if (isset($scope)) unset($scope);
        if (isset($condition)) unset($condition);
        if (isset($previousid)) unset($previousid);

        // музыканты
        // Условие поиска
        $scope = new SearchScope;
        $scope->type = CatalogModule::BASE_TAB_TYPE;
        $scope->name = 'Музыканты';
        $scope->shortname = 'musicians';
        $scope->modelid = self::QID;
        $scope->save();

        $previousid = 0;
        
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'field';
        $condition->field = 'ismusician';
        $condition->comparison = 'equals';
        $condition->value = '1';
        $condition->combine = 'and';
        $condition->previousid = $previousid;
        $condition->save();
        
        $previousid = $condition->id;
        unset($condition);

        // сортировка по рейтингу
        $previousid = $this->createRatingSortCondition($scope, $previousid);
        // сортировка по новизне (от новых к старым)
        $previousid = $this->createTimeSortCondition($scope, $previousid);
        
        // Создаем раздел каталога
        $this->createSectionFromScope($scope);
        if (isset($scope)) unset($scope);
        if (isset($condition)) unset($condition);
        if (isset($previousid)) unset($previousid);

        // танцоры
        // Условие поиска
        $scope = new SearchScope;
        $scope->type = CatalogModule::BASE_TAB_TYPE;
        $scope->name = 'Танцоры';
        $scope->shortname = 'dancers';
        $scope->modelid = self::QID;
        $scope->save();

        $previousid = 0;
        
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'field';
        $condition->field = 'isdancer';
        $condition->comparison = 'equals';
        $condition->value = '1';
        $condition->combine = 'and';
        $condition->previousid = $previousid;
        $condition->save();
        
        $previousid = $condition->id;
        unset($condition);

        // сортировка по рейтингу
        $previousid = $this->createRatingSortCondition($scope, $previousid);
        // сортировка по новизне (от новых к старым)
        $previousid = $this->createTimeSortCondition($scope, $previousid);
        
        // Создаем раздел каталога
        $this->createSectionFromScope($scope);
        if (isset($scope)) unset($scope);
        if (isset($condition)) unset($condition);
        if (isset($previousid)) unset($previousid);

        // Близнецы
        // Условие поиска
        $scope = new SearchScope;
        $scope->type = CatalogModule::BASE_TAB_TYPE;
        $scope->name = 'Близнецы';
        $scope->shortname = 'doubles';
        $scope->modelid = self::QID;
        $scope->save();

        $previousid = 0;
        
        // сортировка по рейтингу
        $previousid = $this->createRatingSortCondition($scope, $previousid);
        // сортировка по новизне (от новых к старым)
        $previousid = $this->createTimeSortCondition($scope, $previousid);
        
        // Создаем раздел каталога
        $this->createSectionFromScope($scope);
        if (isset($scope)) unset($scope);
        if (isset($condition)) unset($condition);
        if (isset($previousid)) unset($previousid);

        // Двойники
        // Условие поиска
        $scope = new SearchScope;
        $scope->type = CatalogModule::BASE_TAB_TYPE;
        $scope->name = 'Двойники';
        $scope->shortname = 'twins';
        $scope->modelid = self::QID;
        $scope->save();

        $previousid = 0;
        
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'field';
        $condition->field = 'istwin';
        $condition->comparison = 'equals';
        $condition->value = '1';
        $condition->combine = 'and';
        $condition->previousid = $previousid;
        $condition->save();
        
        $previousid = $condition->id;
        unset($condition);

        // сортировка по рейтингу
        $previousid = $this->createRatingSortCondition($scope, $previousid);
        // сортировка по новизне (от новых к старым)
        $previousid = $this->createTimeSortCondition($scope, $previousid);
        
        // Создаем раздел каталога
        $this->createSectionFromScope($scope);
        if (isset($scope)) unset($scope);
        if (isset($condition)) unset($condition);
        if (isset($previousid)) unset($previousid);

        // Непрофессиональные актеры
        // Условие поиска
        $scope = new SearchScope;
        $scope->type = CatalogModule::BASE_TAB_TYPE;
        $scope->name = 'Непрофессиональные актеры';
        $scope->shortname = 'nopro_actors';
        $scope->modelid = self::QID;
        $scope->save();

        $previousid = 0;
        
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'field';
        $condition->field = 'isamateuractor';
        $condition->comparison = 'equals';
        $condition->value = '1';
        $condition->combine = 'and';
        $condition->previousid = $previousid;
        $condition->save();
        
        $previousid = $condition->id;
        unset($condition);

        // сортировка по рейтингу
        $previousid = $this->createRatingSortCondition($scope, $previousid);
        // сортировка по новизне (от новых к старым)
        $previousid = $this->createTimeSortCondition($scope, $previousid);
        
        // Создаем раздел каталога
        $this->createSectionFromScope($scope);
        if (isset($scope)) unset($scope);
        if (isset($condition)) unset($condition);
        if (isset($previousid)) unset($previousid);

        // маленькие люди
        // Условие поиска
        $scope = new SearchScope;
        $scope->type = CatalogModule::BASE_TAB_TYPE;
        $scope->name = 'Маленькие люди';
        $scope->shortname = 'small_people';
        $scope->modelid = self::QID;
        $scope->save();

        $previousid = 0;
        
        // Критерий поиска - маленький рост
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'field';
        $condition->field = 'height';
        $condition->comparison = 'lessthen';
        $condition->value = "156";
        $condition->combine = 'and';
        $condition->previousid = $previousid;
        $condition->save();
        
        $previousid = $condition->id;
        unset($condition);
        
        // рост указан
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'field';
        $condition->field = 'height';
        $condition->comparison = 'isnull';
        $condition->value = null;
        $condition->combine = 'and';
        $condition->inverse = 1;
        $condition->previousid = $previousid;
        $condition->save();

        $previousid = $condition->id;
        unset($condition);
        
        // рост не нулевой
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'field';
        $condition->field = 'height';
        $condition->comparison = 'equals';
        $condition->value = 0;
        $condition->combine = 'and';
        $condition->inverse = 1;
        $condition->previousid = $previousid;
        $condition->save();
        
        $previousid = $condition->id;
        unset($condition);
        
        // сортировка по рейтингу
        $previousid = $this->createRatingSortCondition($scope, $previousid);
        // сортировка по новизне (от новых к старым)
        $previousid = $this->createTimeSortCondition($scope, $previousid);
        
        // Создаем раздел каталога
        $this->createSectionFromScope($scope);
        if (isset($scope)) unset($scope);
        if (isset($condition)) unset($condition);
        if (isset($previousid)) unset($previousid);

        // Статисты
        // Условие поиска
        $scope = new SearchScope;
        $scope->type = CatalogModule::BASE_TAB_TYPE;
        $scope->name = 'Статисты';
        $scope->shortname = 'statists';
        $scope->modelid = self::QID;
        $scope->save();

        $previousid = 0;
        
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'field';
        $condition->field = 'isstatist';
        $condition->comparison = 'equals';
        $condition->value = '1';
        $condition->combine = 'and';
        $condition->previousid = $previousid;
        $condition->save();
        
        $previousid = $condition->id;
        unset($condition);

        // сортировка по рейтингу
        $previousid = $this->createRatingSortCondition($scope, $previousid);
        // сортировка по новизне (от новых к старым)
        $previousid = $this->createTimeSortCondition($scope, $previousid);
        
        $this->createSectionFromScope($scope);
        if (isset($scope)) unset($scope);
        if (isset($condition)) unset($condition);
        if (isset($previousid)) unset($previousid);

        // Артисты массовых сцен
        // Условие поиска
        $scope = new SearchScope;
        $scope->type = CatalogModule::BASE_TAB_TYPE;
        $scope->name = 'Артисты массовых сцен';
        $scope->shortname = 'mass_actors';
        $scope->modelid = self::QID;
        $scope->save();

        $previousid = 0;
        
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'field';
        $condition->field = 'ismassactor';
        $condition->comparison = 'equals';
        $condition->value = '1';
        $condition->combine = 'and';
        $condition->previousid = $previousid;
        $condition->save();
        
        $previousid = $condition->id;
        unset($condition);

        // сортировка по рейтингу
        $previousid = $this->createRatingSortCondition($scope, $previousid);
        // сортировка по новизне (от новых к старым)
        $previousid = $this->createTimeSortCondition($scope, $previousid);
        
        // Создаем раздел каталога
        $this->createSectionFromScope($scope);
        if (isset($scope)) unset($scope);
        if (isset($condition)) unset($condition);
        if (isset($previousid)) unset($previousid);
        
        unset($table);

        
    }

    /**
     * Create indexes for all fields in the table
     * @param string $table - the table name
     * @param array $fields - table fields
     *                         example: array( "fieldname1" => "fieldtype1", "fieldname2" => "fieldtype2", ... )
     * @param array $excluded - not indexed fields
     *                           example: array("fieldname1", "fieldname2", "fieldname3", ...)
     * @param string $idxPrefix - index name prefix (default is "idx_")
     *
     * @return null
     */
    protected function _createIndexes($table, $fields, $excluded=array(), $idxPrefix="idx_")
    {
        // gather all field names
        $fieldNames = array_keys($fields);

        // exclude not needed fields from index
        // ("id" is already primary key, so we never need to create additional index for it)
        $noIndex = CMap::mergeArray(array("id"), $excluded);

        $indexedFields = array_diff($fieldNames, $noIndex);

        foreach ( $indexedFields as $field )
        {
            $this->createIndex($idxPrefix.$field, $table, $field);
        }
    }

    /**
     * Создать раздел анкеты по условию поиска
     * @param SearchScope $scope
     */
    protected function createSectionFromScope($scope)
    {
        $table = "{{catalog_sections}}";

        $section = array(
            'name'      => $scope->name,
            'shortname' => $scope->shortname,
            'scopeid'   => $scope->id,
            'parentid'  => 1,
            'lang'      => 'ru',
            'content'   => 'users',
            'order'     => 0,
        );

        $this->insert($table, $section);
    }

    protected function createRatingSortCondition($scope, $previousid=0)
    {
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'sort';
        $condition->field = 'rating';
        $condition->value = 'DESC';
        //$condition->combine = 'and';
        $condition->previousid = $previousid;
        $condition->save();

        return $condition->id;
    }

    protected function createTimeSortCondition($scope, $previousid=0)
    {
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'sort';
        $condition->field = 'timecreated';
        $condition->value = 'DESC';
        //$condition->combine = 'and';
        $condition->previousid = $previousid;
        $condition->save();

        return $condition->id;
    }
    
    protected function createActiveStatusCondition($scope, $activeStatusScope, $previousid=0)
    {
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'scope';
        $condition->value = $activeStatusScope->id;
        $condition->combine = 'and';
        $condition->previousid = $previousid;
        $condition->save();
    
        return $condition->id;
    }

    protected function creareAllowedComparison($field, $excludedTypes)
    {
        $allTypes = array('equals', 'startswith', 'endswith', 'contains', 'morethen', 'lessthen', 'in', 'isnull', 'isempty', 'isset');
        $types = array_diff($allTypes, $excludedTypes);

        $comparisonType = new AllowedComparisonType();
        $comparisonType->model = 'Questionary';
        $comparisonType->field = $field;
        $comparisonType->fieldlabel = QuestionaryModule::t($field.'_label');
        $comparisonType->types = $types;
        $comparisonType->save();
    }
}

// Мы будем применять различные условия поиска к анкете.
// Для этого запишем, какие условия поиска для каких полей допустимо применять
// (это также пригодится в будущем при отображении пользовательской формы поиска)
/*
 firstname
lastname
middlename
birthdate
gender
timecreated
timefilled
timemodified
height
weight
wearsizemin
wearsizemax
shoessize
city
cityid
mobilephone
homephone
addphone
vkprofile
looktype
haircolor
eyecolor
physiquetype
isactor
hasfilms
isemcee
isparodist
istwin
ismodel
titsize
chestsize
waistsize
hipsize
isdancer
hasawards
isstripper
striptype
striplevel
issinger
singlevel
ismusician
issportsman
isextremal
isathlete
hasskills
hastricks
haslanuages
wantsbusinesstrips
hasinshurancecard
inshurancecardnum
hasforeignpassport
passportexpires
addressid
policyagreed
status
encrypted
isphotomodel
ispromomodel
iscoloredhair
newhaircolor
rating
countryid
salary
hastatoo
galleryid

////////////////////////////////////
        // Привязываем вкладки к разделам //
        ////////////////////////////////////



        // Вкладки главной страницы каталога


        // Медийные актеры

        // Модели

        // Профессиональные актеры

        // Дети

        // актеры-студенты и выпускники

        // атлеты

        // ведущие

        // певцы / певицы

        // музыканты

        // танцоры

        // Близнецы

        // Двойники

        // Непрофессиональные актеры

        // маленькие люди

        // Статисты

        // Артисты массовых сцен


// Создаем всегда работающее условие поиска - отображать только активные анкеты
        $activeStatusScope = new SearchScope;
        $activeStatusScope->type = 'catalog|active_status';
        $activeStatusScope->name = 'Только активные анкеты';
        $activeStatusScope->shortname = 'status_active';
        $activeStatusScope->modelid = self::QID;
        $activeStatusScope->save();
        
        $previousid = 0;
        $condition = new ScopeCondition();
        $condition->scopeid = $activeStatusScope->id;
        $condition->type  = 'field';
        $condition->field = 'status';
        $condition->comparison = 'equals';
        $condition->value = 'active';
        $condition->combine = 'and';
        $condition->previousid = $previousid;
        $condition->save();
        unset($condition);


*/