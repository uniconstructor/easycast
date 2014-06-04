<?php

class m140604_064300_addExternalToFields extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{q_user_fields}}";
        $this->addColumn($table, 'external', "tinyint(1) DEFAULT NULL");
        $this->createIndex('idx_external', $table, 'external');
        
        $this->addColumn($table, 'multiple', "tinyint(1) DEFAULT NULL");
        $this->createIndex('idx_multiple', $table, 'multiple');
        
        ///////////////////////////////////////////////////////////////////////
        
        $fields = array(
            'firstname', 'lastname', 'middlename', 'birthdate', 'gender', 'height',
            'weight', 'shoessize', 'cityid', 'mobilephone', 'homephone',
            'addphone', 'vkprofile', 'looktype', 'haircolor', 'eyecolor', 'physiquetype',
            'isactor', 'hasfilms', 'isemcee', 'isparodist', 'istwin', 'ismodel', 'titsize',
            'chestsize', 'waistsize', 'hipsize', 'isdancer', 'hasawards', 'isstripper', 'striptype',
            'striplevel', 'issinger', 'singlevel', 'ismusician', 'issportsman', 'isextremal',
            'isathlete', 'hasskills', 'hastricks', 'haslanuages',
            'status', 'isphotomodel', 'ispromomodel', 'rating',
            'fbprofile', 'okprofile', 'isamateuractor', 'istvshowmen',
            'isstatist', 'ismassactor', 'playagemin',
            'playagemax', 'hairlength', 'istheatreactor', 'ismediaactor',
            'privatecomment', 'wearsize', 'currentcountryid',
        );
        $update = array('external' => 0, 'multiple' => 0);
        foreach ( $fields as $name )
        {
            $condition = "`name` = :name";
            $params    = array(':name' => $name);
            $this->update($table, $update, $condition, $params);
        }
        unset($fields, $update, $condition, $params);
        
        // поля модели User
        $fields = array('email', 'policyagreed');
        $update = array('external' => 1, 'multiple' => 0);
        foreach ( $fields as $name )
        {
            $condition = "(`name` = :name) AND (`storage` = 'questionary.user')";
            $params    = array(':name' => $name);
            $this->update($table, $update, $condition, $params);
        }
        unset($fields, $update, $condition, $params);
        
        // условия съемки
        $fields = array('salary', 'wantsbusinesstrips', 'hasforeignpassport', 'passportexpires',
            'isnightrecording', 'istoplessrecording', 'isfreerecording', 'custom',
        );
        $update = array('external' => 1, 'multiple' => 0);
        foreach ( $fields as $name )
        {
            $condition = "(`name` = :name) AND (`storage` = 'questionary.recordingconditions')";
            $params    = array(':name' => $name);
            $this->update($table, $update, $condition, $params);
        }
        unset($fields, $update, $condition, $params);
        
        // остальные множественные значения которые хранятся в других таблицах
        $fields = array('addchars', 'films', 'emceelist', 'parodistlist', 'twinlist', 'modelschools',
            'modeljobs', 'photomodeljobs', 'promomodeljobs', 'dancetypes', 'awards', 'vocaltypes',
            'voicetimbres', 'instruments', 'sporttypes', 'extremaltypes',
            'tricks', 'skills', 'languages', 'tvshows', 'theatres', 'video', 'photo', 'actoruniversities',
            'musicuniversities',
        );
        $update = array('external' => 1, 'multiple' => 1);
        foreach ( $fields as $name )
        {
            $condition = "`name` = :name";
            $params    = array(':name' => $name);
            $this->update($table, $update, $condition, $params);
        }
        unset($fields, $update, $condition, $params);
        
        $deleteConditions = array('in', 'name', array('privatecomment', 'rating', 'playagemin', 'playagemax'));
        $this->delete($table, $deleteConditions);
    }
}