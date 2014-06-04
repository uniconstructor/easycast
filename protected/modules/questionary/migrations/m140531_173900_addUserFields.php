<?php

class m140531_173900_addUserFields extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table  = "{{q_user_fields}}";
        
        // поля анкеты
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
            'privatecomment', 'wearsize'
        );
        foreach ( $fields as $name )
        {
            $this->insert($table, array('name' => $name, 'storage' => 'questionary'));
        }
        unset($fields);
        
        // поля модели User
        $fields = array('email', 'policyagreed');
        foreach ( $fields as $name )
        {
            $this->insert($table, array('name' => $name, 'storage' => 'questionary.user'));
        }
        unset($fields);
        
        // условия съемки
        $fields = array('salary', 'wantsbusinesstrips', 'hasforeignpassport', 'passportexpires', 
            'isnightrecording', 'istoplessrecording', 'isfreerecording', 'custom'
        ); 
        foreach ( $fields as $name )
        {
            $this->insert($table, array('name' => $name, 'storage' => 'questionary.recordingconditions'));
        }
        unset($fields);
        
        // сложные значения
        $fields = array('addchars', 'films', 'emceelist', 'parodistlist', 'twinlist', 'modelschools',
            'modeljobs', 'photomodeljobs', 'promomodeljobs', 'dancetypes', 'awards', 'vocaltypes',
            'voicetimbres', 'instruments', 'sporttypes', 'extremaltypes',
            'tricks', 'skills', 'languages', 'tvshows', 'theatres', 'video', 'photo', 'actoruniversities', 
            'musicuniversities'
        );
        foreach ( $fields as $name )
        {
            $this->insert($table, array('name' => $name, 'storage' => 'questionary'));
        }
        unset($fields);
    }
}