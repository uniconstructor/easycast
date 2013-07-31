<?php

/**
 * Устаревший плагин, отображавший испольщуемые условия поиска
 * @deprecated
 * @todo удалить при рефакторинге
 */
class ScopesList extends CWidget
{
    /**
     * @var SearchScope - условие вкладки или раздела в таблице SearchScopes 
     */
    public $scope;
    
    public function run()
    {
        Yii::import('application.extensions.ESearchScopes.models.*');
        if ( ! $this->scope )
        {
            echo 'Не заданы';
            return;
        }
        $this->displayScope($this->scope);
    }
    
    protected function displayScope($scope, $combine=null)
    {
        echo "<h4>{$combine} Группа условий  (name:{$scope->name} shortname:{$scope->shortname} type:{$scope->type})</h4>";
        echo '<ul>';
        foreach ( $scope->scopeConditions as $condition )
        {
            if ( $condition->type == 'scope' )
            {
                $childScope = SearchScope::model()->findByPk($condition->value);
                $this->displayScope($childScope);
            }else
          {
                $this->displayCondition($condition);
            }
        }
        echo '</ul>';
    }
    
    protected function displayCondition($condition)
    {
        $value = $condition->value;
        echo "<li>type:{$condition->type} | field:{$condition->field} | value:{$value} | combine:{$condition->combine}</li>";
    }
}