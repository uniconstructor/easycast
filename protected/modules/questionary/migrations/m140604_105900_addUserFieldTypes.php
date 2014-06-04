<?php

class m140604_105900_addUserFieldTypes extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{q_user_fields}}";
        $this->addColumn($table, 'type', "varchar(255) NOT NULL DEFAULT 'text'");
        $this->createIndex('idx_type', $table, 'type');
        
        ////////////////////////////////
        $fields = $this->dbConnection->createCommand()->select()->from($table)->queryAll();
        
        foreach ( $fields as $field )
        {
            $columns = array('type' => $this->getFieldType($field['name']));
            $this->update($table, $columns, "`id` = {$field['id']}");
        }
        
        $this->delete($table, "`name` = 'status'");
    }
    
    /**
     * 
     * @param string $name
     * @return string
     */
    protected function getFieldType($name)
    {
        $types = array(
            // поля анкеты
            'firstname' => 'text', 
            'lastname' => 'text', 
            'middlename' => 'text', 
            'birthdate' => 'date', 
            'gender' => 'toggle', 
            'height' => 'slider', 
            'weight' => 'slider', 
            'shoessize' => 'select', 
            'cityid' => 'city', 
            'mobilephone' => 'phone', 
            'homephone' => 'phone', 
            'addphone' => 'phone', 
            'vkprofile' => 'url', 
            'looktype' => 'select', 
            'haircolor' => 'select', 
            'eyecolor' => 'select', 
            'physiquetype' => 'select', 
            'isactor' => 'badge', 
            'hasfilms' => 'badge', 
            'isemcee' => 'badge', 
            'isparodist' => 'badge', 
            'istwin' => 'badge', 
            'ismodel' => 'badge', 
            'titsize' => 'select', 
            'chestsize' => 'slider', 
            'waistsize' => 'slider', 
            'hipsize' => 'slider', 
            'isdancer' => 'badge', 
            'hasawards' => 'badge', 
            'isstripper' => 'badge', 
            'striptype' => 'select', 
            'striplevel' => 'select', 
            'issinger' => 'badge', 
            'singlevel' => 'select', 
            'ismusician' => 'badge', 
            'issportsman' => 'badge', 
            'isextremal' => 'badge', 
            'isathlete' => 'badge', 
            'hasskills' => 'badge', 
            'hastricks' => 'badge', 
            'haslanuages' => 'badge',
            'isphotomodel' => 'badge', 
            'ispromomodel' => 'badge', 
            'fbprofile' => 'url', 
            'okprofile' => 'url', 
            'isamateuractor' => 'badge', 
            'istvshowmen' => 'badge', 
            'isstatist' => 'badge', 
            'ismassactor' => 'badge', 
            'nativecountryid' => 'country', 
            'currentcountryid' => 'country',
            'playage' => 'slider', 
            'hairlength' => 'select', 
            'istheatreactor' => 'badge', 
            'ismediaactor' => 'badge', 
            'wearsize' => 'select', 
            // поля модели User
            'email' => 'email', 
            'policyagreed' => 'checkbox',
            // условия съемки
            'salary' => 'text', 
            'wantsbusinesstrips' => 'toggle', 
            'hasforeignpassport' => 'toggle', 
            'isnightrecording' => 'toggle', 
            'istoplessrecording' => 'toggle', 
            'isfreerecording' => 'toggle', 
            'custom' => 'textarea', 
            'passportexpires' => 'date', 
            // сложные значения
            'addchars' => 'activity', 
            'actoruniversities' => 'grid', 
            'films' => 'grid', 
            'emceelist' => 'activity', 
            'parodistlist' => 'activity', 
            'twinlist' => 'activity', 
            'modelschools' => 'activity',
            'modeljobs' => 'activity', 
            'photomodeljobs' => 'activity', 
            'promomodeljobs' => 'activity', 
            'dancetypes' => 'activity', 
            'awards' => 'grid', 
            'vocaltypes' => 'activity',
            'voicetimbres' => 'activity', 
            'instruments' => 'activity', 
            'musicuniversities' => 'grid', 
            'sporttypes' => 'activity', 
            'extremaltypes' => 'activity',
            'tricks' => 'activity', 
            'skills' => 'activity', 
            'languages' => 'activity', 
            'tvshows' => 'grid', 
            'theatres' => 'grid', 
            'video' => 'grid', 
            'photo' => 'gallery',
        );
        if ( isset($types[$name]) )
        {
            return $types[$name];
        }
        return 'text';
    }
}