<?php

/**
 * This behavior allows you to construct and use complex SQL conditions from the UI,
 * using "ESearchScopeConstructor" widget
 */
class ESearchScopeBehavior extends CActiveRecordBehavior
{
    /** 
     * @var string Model attribute name to store created scope
     *             (used if model has only one scope)
     */
    public $idAttribute;
    
    /**
     * @param int $id - coud be not specified, if "idAttribute" is set
     * @return SearchScope|null
     */
    public function getSearchScope($id=null)
    {
        if ( ! $id AND $field = $this->idAttribute )
        {
            $id = $this->owner->$field;
        }
        return SearchScope::model()->findByPk($id);
    }
    
    /**
     * @param string $name
     * @return SearchScope|null
     */
    public function getSearchScopeByShortName($name)
    {
        
    }
    
    /**
     * Get all search scopes, belonging to this model
     * Used if model has many scopes
     * @param CDbCriteria $criteria - criteria for scopes.
     *                                If not specified - all child scopes will be extracted
     * @return array - records from "search_scopes" table, matching criteria
     */
    public function getSearchScopesByCriteria($criteria=null)
    {
        $internalCriteria = new CDbCriteria();
        $internalCriteria->addCondition('parentid = '.$this->owner->id);
        
        if ( $criteria instanceof CDbCriteria )
        {
            $criteria->mergeWith($internalCriteria);
        }else
       {
           $criteria = $internalCriteria;
        }
        
        return SearchScope::model()->find($criteria);
    }
    
    /**
     * Select needed records, using previosly saved scope
     * Don't forget to import searched model class before it!
     * 
     * @param SearchScope|int $scope - search scope object or id
     * @return array of active records or null
     */
    public function getRecordsByScope($scope)
    {
        $scope = $this->getScopeObject($scope);
        $criteria = $this->getCriteriaByScope($scope);
        
        return $modelClass::model()->find($criteria);
    }
    
    /**
     * @param SearchScope|int $scope - search scope object or id
     * @return SearchScope
     */
    protected function getScopeObject($scope)
    {
        if ( ! ( $scope instanceof SearchScope ) )
        {
            $scope = SearchScope::model()->findByPk($scope);
        }
        
        return $scope;
    }
    
    /**
     * Get DB criteria 
     * @param SearchScope|int $scope - search scope object or id
     * @return CDbCriteria - constructed criteria to select records from DB 
     */
    public function getCriteriaByScope($scope)
    {
        $scope = $this->getScopeObject($scope);
        
        return $scope->constructCriteria();
    }
}