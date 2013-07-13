<?php

/**
 * Миграция, исправляющая выборку разделов каталога
 */
class m130524_091500_fixCatalogSections extends CDbMigration
{
    public function safeUp()
    {
        Yii::import('application.modules.catalog.CatalogModule');
        Yii::import('application.modules.catalog.models.*');
        Yii::import('application.extensions.ESearchScopes.models.*');
        Yii::import('application.extensions.ESearchScopes.ESearchScopes');
        
        // раздел "дети" //
        $section = CatalogSection::model()->find("`shortname` = 'children_section'");
        // Удаляем старое, неработающее условие поиска по детям
        $scope = SearchScope::model()->findByPk($section->scopeid);
        foreach ( $scope->scopeConditions as $condition )
        {
            $condition->delete();
        }
        // создаем новое условие
        $lastId = $this->createChildrenCondition($scope);
        $lastId = $this->createRatingSortCondition($scope, $lastId);
        $lastId = $this->createTimeSortCondition($scope, $lastId);
        unset($section);
        unset($scope);
        unset($lastId);
        
        // вкладка "дети" //
        $tab = CatalogTab::model()->find("`shortname` = 'children'");
        // Удаляем старое, неработающее условие поиска по детям
        $scope = SearchScope::model()->findByPk($tab->scopeid);
        foreach ( $scope->scopeConditions as $condition )
        {
            $condition->delete();
        }
        // создаем новое условие
        $lastId = $this->createChildrenCondition($scope);
        $lastId = $this->createRatingSortCondition($scope, $lastId);
        $lastId = $this->createTimeSortCondition($scope, $lastId);
        unset($section, $tab);
        unset($scope);
        unset($lastId);
        
        // актеры-студенты и выпускники //
        $section = CatalogSection::model()->find("`shortname` = 'student_actors'");
        // Удаляем старое, неработающее условие поиска
        $scope = SearchScope::model()->findByPk($section->scopeid);
        foreach ( $scope->scopeConditions as $condition )
        {
            $condition->delete();
        }
        // создаем новое условие
        $lastId = $this->createStudentCondition($scope);
        $lastId = $this->createRatingSortCondition($scope, $lastId);
        $lastId = $this->createTimeSortCondition($scope, $lastId);
        unset($section);
        unset($scope);
        unset($lastId);
        
        // близнецы //
        $section = CatalogSection::model()->find("`shortname` = 'doubles'");
        // Удаляем старое, неработающее условие поиска
        $scope = SearchScope::model()->findByPk($section->scopeid);
        foreach ( $scope->scopeConditions as $condition )
        {
            $condition->delete();
        }
        // создаем новое условие
        $lastId = $this->createTwinsCondition($scope);
        $lastId = $this->createRatingSortCondition($scope, $lastId);
        $lastId = $this->createTimeSortCondition($scope, $lastId);
        unset($section);
        unset($scope);
        unset($lastId);
        
        // Маленькие люди //
        $section = CatalogSection::model()->find("`shortname` = 'small_people'");
        // Удаляем старое, неработающее условие поиска
        $scope = SearchScope::model()->findByPk($section->scopeid);
        foreach ( $scope->scopeConditions as $condition )
        {
            $condition->delete();
        }
        // создаем новое условие
        $lastId = $this->createSmallPeopleCondition($scope);
        $lastId = $this->createRatingSortCondition($scope, $lastId);
        $lastId = $this->createTimeSortCondition($scope, $lastId);
        unset($section);
        unset($scope);
        unset($lastId);
    }
    
    protected function createChildrenCondition($scope, $previousid=0)
    {
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        // 18 * 366 * 24 * 3600 = 569203200
        // 569203200 - это 18 лет в секундах
        // Мы не пишем математические вычисления внутрь SQL-запроса
        // иначе они будут производится для каждой строки, а это замедлит поиск
        $condition->field = '`birthdate` >= (CAST(UNIX_TIMESTAMP() AS SIGNED) - 569203200) AND (`birthdate` IS NOT NULL)';
        // Никаких значений в условие подставлять не нужно
        $condition->value = null;
        $condition->type  = 'condition';
        $condition->previousid = $previousid;
        $condition->combine = 'and';
        $condition->save();
        
        return $condition->id;
    }
    
    protected function createStudentCondition($scope, $previousid=0)
    {
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->with = 'actoruniversities';
        $condition->jointype = 'INNER JOIN';
        $condition->type  = 'condition';
        // Считаем актерами-выпускниками только тех кто закончил 3 ВУЗ года назад или меньше
        // 94867200 - это 3 года в секундах
        // Мы не пишем математические вычисления внутрь SQL-запроса
        // иначе они будут производится для каждой строки, а это замедлит поиск
        $condition->field = '`isactor` = 1 AND `actoruniversities`.`timeend` >= (CAST(UNIX_TIMESTAMP() AS SIGNED) - 94867200)';
        // Никаких значений в условие подставлять не нужно
        $condition->value = null;
        $condition->previousid = $previousid;
        $condition->combine = 'and';
        $condition->save();
        
        return $condition->id;
    }
    
    protected function createTwinsCondition($scope, $previousid=0)
    {
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->with  = 'addchars';
        $condition->jointype = 'INNER JOIN';
        $condition->type  = 'condition';
        $condition->field = "`addchars`.`value` IN ('twins', 'doubles', 'triples')";
        // Никаких значений в условие подставлять не нужно
        $condition->value = null;
        $condition->previousid = $previousid;
        $condition->combine = 'and';
        $condition->save();
        
        return $condition->id;
        
    }
    
    protected function createSmallPeopleCondition($scope, $previousid=0)
    {
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'condition';
        // 569203200 - это 18 лет в секундах
        // Мы не пишем математические вычисления внутрь SQL-запроса
        // иначе они будут производится для каждой строки, а это замедлит поиск
        // Считаем маленькими людьми всех кто при возрасте > 18 не достиг роста больше 150
        $condition->field = '`birthdate` <= (CAST(UNIX_TIMESTAMP() AS SIGNED) - 569203200) AND (`birthdate` IS NOT NULL) AND `height` < 150 AND `height` > 0 ';
        // Никаких значений в условие подставлять не нужно
        $condition->value = null;
        $condition->previousid = $previousid;
        $condition->combine = 'and';
        $condition->save();
        
        return $condition->id;
    }
    
    protected function createRatingSortCondition($scope, $previousid=0)
    {
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type  = 'sort';
        $condition->field = 'rating';
        $condition->value = 'DESC';
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
        $condition->previousid = $previousid;
        $condition->save();
    
        return $condition->id;
    }
}