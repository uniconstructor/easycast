<?php

class m130821_172200_DeleteBrokenUsers extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        Yii::import('application.modules.questionary.models.*');
        Yii::import('application.modules.user.models.User');
        
        // ищем пользователнй без анкеты и удаляем
        $criteria = new CDbCriteria();
        //$criteria->select = 'id, user';
        $criteria->select = 'id';
        $criteria->with = array('user', 'recordingconditions');
        $questionaries = Questionary::model()->findAll($criteria);
        //echo count($questionaries);
        foreach ( $questionaries as $questionary )
        {
            if ( ! $questionary->user )
            {
                $questionary->delete();
                echo "\n{$questionary->id}|no Questionary, deleted\n";
            }
            if ( ! $questionary->recordingconditions )
            {// восстанавливаем условия участния в съемках если они вдруг отвалились
                $recordingConditions = new QRecordingConditions();
                $recordingConditions->questionaryid = $questionary->id;
                $recordingConditions->save();
                echo "\n{$questionary->id}|recordingconditions recovered\n";
            }
        }
        unset($criteria);
        
        // ищем анкеты без пользователей и удаляем
        $criteria = new CDbCriteria();
        //$criteria->select = 'id, questionary';
        $criteria->select = 'id';
        $criteria->with = array('questionary');
        $users = User::model()->findAll($criteria);
        //echo count($users);
        foreach ( $users as $user )
        {
            if ( ! $user->questionary )
            {
                $user->delete();
                echo "\n{$user->id}|no Questionary, deleted\n";
            }
        }
        
        // заодно очистим старые логи
        $table = 'YiiLog';
        $this->truncateTable($table);
    }
}