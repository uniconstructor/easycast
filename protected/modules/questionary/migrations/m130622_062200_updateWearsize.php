<?php

class m130622_062200_updateWearsize extends CDbMigration
{
    public function up()
    {
        Yii::app()->getModule('questionary');
        Yii::import('application.modules.questionary.models.*');
        Yii::import('application.modules.questionary.models.complexValues.*');
        $table = Yii::app()->getModule('questionary')->questionaryTable;
        
        $this->addColumn($table, 'wearsize', 'varchar(16) DEFAULT NULL');
        $this->refreshTableSchema($table);
        
        $criteria = new CDbCriteria();
        $criteria->addCondition('`wearsizemin` IS NOT NULL AND `wearsizemax` IS NOT NULL');
        $criteria->select = array('id', 'wearsizemin', 'wearsizemax', 'wearsize');
        $users = Questionary::model()->findAll($criteria);
        
        foreach ( $users as $user )
        {
            if ( $user->wearsizemin == $user->wearsizemax )
            {
                $wearsize = $user->wearsizemin;
            }else
           {
                $wearsize = $user->wearsizemin.'-'.$user->wearsizemax;
            }
            
            $this->update($table, array('wearsize' => $wearsize), 'id='.$user->id);
        }
        
        $this->dropColumn($table, 'wearsizemin');
        $this->dropColumn($table, 'wearsizemax');
        
        
        $this->createIndex('idx_wearsize', $table, 'wearsize');
    }
}