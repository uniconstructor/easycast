<?php

/**
 * Эта миграция окончательно исправляет проблемы с отображением разделов, для отображения которых
 * нужно производить запросы по 2 и более таблицам (разделы "близнецы" и "актеры-выпускники")
 */
class m130620_084800_newComplexSectionsCriteria extends CDbMigration
{
    public function safeUp()
    {
        Yii::import('application.modules.catalog.CatalogModule');
        Yii::import('application.modules.catalog.models.*');
        Yii::import('application.extensions.ESearchScopes.models.*');
        Yii::import('application.extensions.ESearchScopes.ESearchScopes');
        
        /// Студенты и выпускники ///
        
        $section = CatalogSection::model()->find("`shortname` = 'student_actors'");
        // Удаляем старое, неправильное условие поиска
        $scope = SearchScope::model()->findByPk($section->scopeid);
        foreach ( $scope->scopeConditions as $condition )
        {
            $condition->delete();
        }
        
        // составляем критерий (условие поиска) для анкет
        $criteria = new CDbCriteria();
        $criteria->with = array('actoruniversities');
        // без этого флага не работает реляционный запрос
        $criteria->together = true;
        $criteria->compare('isactor', 1);
        $criteria->compare('status', 'active');
        // 94867200 - это последние 3 года
        $criteria->addCondition("`actoruniversities`.`timeend` >= (CAST(UNIX_TIMESTAMP() AS SIGNED) - 94867200)");
        $criteria->order = "`t`.`rating` DESC, `t`.`timecreated` DESC";
        
        // Создаем само условие поиска
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type = 'serialized';
        $condition->value = serialize($criteria);
        $condition->combine = 'and';
        $condition->save();
        
        $section->visible = 1;
        $section->save();
        
        unset($criteria);
        unset($condition);
        unset($section);
        unset($scope);
        
        
        /// Близнецы ///
        
        $section = CatalogSection::model()->find("`shortname` = 'doubles'");
        // Удаляем старое, неправильное условие поиска
        $scope = SearchScope::model()->findByPk($section->scopeid);
        foreach ( $scope->scopeConditions as $condition )
        {
            $condition->delete();
        }
        
        // составляем критерий (условие поиска) для анкет
        $criteria = new CDbCriteria();
        $criteria->with = array('addchars');
        // без этого флага не работает реляционный запрос
        $criteria->together = true;
        $criteria->compare('status', 'active');
        
        $values = implode("', '", array('twins', 'doubles', 'triples'));
        $criteria->addCondition("`addchars`.`value` IN ('{$values}')");
        $criteria->order = "`t`.`rating` DESC, `t`.`timecreated` DESC";
        
        // Создаем само условие поиска
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type = 'serialized';
        $condition->value = serialize($criteria);
        $condition->combine = 'and';
        $condition->save();
        
        $section->visible = 1;
        $section->save();
    }
}