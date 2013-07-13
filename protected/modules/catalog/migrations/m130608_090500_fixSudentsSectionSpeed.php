<?php

/**
 * Миграция, исправляющая скорость открытия раздела "актеры-студенты и выпускники"
 */
class m130608_090500_fixSudentsSectionSpeed extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        Yii::import('application.modules.catalog.CatalogModule');
        Yii::import('application.modules.catalog.models.*');
        Yii::import('application.extensions.ESearchScopes.models.*');
        Yii::import('application.extensions.ESearchScopes.ESearchScopes');
        
        $section = CatalogSection::model()->find("`shortname` = 'student_actors'");
        // Удаляем старое,   медленное условие поиска
        $scope = SearchScope::model()->findByPk($section->scopeid);
        foreach ( $scope->scopeConditions as $condition )
        {
            $condition->delete();
        }
        
        // составляем запрос для поиска анкет
        $criteria = new CDbCriteria();
        /*$criteria->addColumnCondition(array(
            'isactor' => 1,
            'status'  => 'active',
        ));*/
        $criteria->compare('isactor', 1);
        $criteria->compare('status', 'active');
        $criteria->addCondition("`actoruniversities`.`timeend` >= (CAST(UNIX_TIMESTAMP() AS SIGNED) - 94867200)");
        
        $criteria->with = array(
            'actoruniversities' => array(
                'joinType'  => 'INNER JOIN',
                //'condition' => "`actoruniversities`.`timeend` >= (CAST(UNIX_TIMESTAMP() AS SIGNED) - 94867200)",
            ),
        );
        $criteria->order = "`rating` DESC, `timecreated` DESC";
        
        // Создаем само условие поиска
        $condition = new ScopeCondition();
        $condition->scopeid = $scope->id;
        $condition->type = 'serialized';
        $condition->value = serialize($criteria);
        $condition->combine = 'and';
        $condition->save();
    }
}