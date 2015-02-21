<?php

/**
 * AR-модель с настраиваемой структурой
 *
 * @see https://github.com/LimeSurvey/LimeSurvey/blob/master/application/models/LSActiveRecord.php
 * @see http://api.limesurvey.org/classes/SurveyDynamic.html
 */
class EntityActiveRecord extends CActiveRecord
{
    protected static $_entityName = '';
    
    /**
     * Returns the static model of Settings table
     *
     * @static
     * @access public
     * @param int $surveyid
     * @return SurveyDynamic
     */
    public static function model($sid = NULL)
    {
        $refresh = false;
        if (!is_null($sid)) {
            self::sid($sid);
            $refresh = true;
        }
    
        $model = parent::model(__CLASS__);
    
        //We need to refresh if we changed sid
        if ($refresh === true) $model->refreshMetaData();
        return $model;
    }
    
    /**
     * Modified version that default to do the same as the original, but allows via a
     * third parameter to retrieve the result as array instead of active records.
     * This
     * solves a joining problem. Usage via findAllAsArray method
     *
     * Performs the actual DB query and populates the AR objects with the query result.
     * This method is mainly internally used by other AR query methods.
     *
     * @param CDbCriteria $criteria the query criteria
     * @param boolean $all whether to return all data
     * @return mixed the AR objects populated with the query result
     * @since 1.1.7
     */
    protected function query($criteria, $all = false, $asAR = true)
    {
        if ( $asAR === true )
        {
            return parent::query($criteria, $all);
        }else
        {
            $this->beforeFind();
            $this->applyScopes($criteria);
            if ( !$all )
            {
                $criteria->limit = 1;
            }
            
            $command = $this->getCommandBuilder()->createFindCommand($this->getTableSchema(), $criteria);
            // For debug, this command will get you the generated sql:
            // echo $command->getText();
            
            return $all ? $command->queryAll() : $command->queryRow();
        }
    }

    /**
     * Finds all active records satisfying the specified condition but returns them as array
     *
     * See {@link find()} for detailed explanation about $condition and $params.
     *
     * @param mixed $condition query condition or criteria.
     * @param array $params parameters to be bound to an SQL statement.
     * @return array list of active records satisfying the specified condition. An empty array is returned if none is found.
     */
    public function findAllAsArray($condition = '', $params = array())
    {
        Yii::trace(get_class($this) . '.findAll()', 'system.db.ar.CActiveRecord');
        $criteria = $this->getCommandBuilder()->createCriteria($condition, $params);
        return $this->query($criteria, true, false); // Notice the third parameter 'false'
    }

    /**
     * Return the max value for a field
     *
     * This is a convenience method, that uses the primary key of the model to
     * retrieve the highest value.
     *
     * @param string $field The field that contains the Id, when null primary key is used if it is a single field
     * @param boolean $forceRefresh Don't use value from static cache but always requery the database
     * @return false int
     */
    public function getMaxId($field = null, $forceRefresh = false)
    {
        static $maxIds = array();
        
        if ( is_null($field) )
        {
            $primaryKey = $this->getMetaData()->tableSchema->primaryKey;
            if ( is_string($primaryKey) )
            {
                $field = $primaryKey;
            }else
            {
                // Composite key, throw a warning to the programmer
                throw new Exception(sprintf('Table %s has a composite primary key, please explicitly state what field you need the max value for.', $this->tableName()));
            }
        }
        
        if ( $forceRefresh || !array_key_exists($field, $maxIds) )
        {
            $maxId = $this->dbConnection->createCommand()
                ->select('MAX(' . $this->dbConnection->quoteColumnName($field) . ')')
                ->from($this->tableName())
                ->queryScalar();
            
            // Save so we can reuse in the same request
            $maxIds[$field] = $maxId;
        }
        
        return $maxIds[$field];
    }

    /**
     *
     * @todo This should also be moved to the behavior at some point.
     *       This method overrides the parent in order to raise PluginEvents for Bulk delete operations.
     *      
     *       Filter Criteria are wrapped into a CDBCriteria instance so we have a single instance responsible for holding the filter criteria
     *       to be passed to the PluginEvent,
     *       this also enables us to pass the fully configured CDBCriteria instead of the original Parameters.
     *      
     *       See {@link find()} for detailed explanation about $condition and $params.
     * @param array $attributes list of attribute values (indexed by attribute names) that the active records should match.
     *        An attribute value can be an array which will be used to generate an IN condition.
     * @param mixed $condition query condition or criteria.
     * @param array $params parameters to be bound to an SQL statement.
     * @return integer number of rows affected by the execution.
     */
    public function deleteAllByAttributes($attributes, $condition = '', $params = array())
    {
        $builder = $this->getCommandBuilder();
        $table = $this->getTableSchema();
        $criteria = $builder->createColumnCriteria($table, $attributes, $condition, $params);
        $this->dispatchPluginModelEvent('before' . get_class($this) . 'DeleteMany', $criteria);
        $this->dispatchPluginModelEvent('beforeModelDeleteMany', $criteria);
        return parent::deleteAllByAttributes(array(), $criteria, array());
    }
}