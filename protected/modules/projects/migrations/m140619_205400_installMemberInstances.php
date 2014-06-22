<?php

class m140619_205400_installMemberInstances extends CDbMigration
{
    public function safeUp()
    {
        $membersTable = '{{project_members}}';
        $topModelMembers = $this->dbConnection->createCommand()->select()->from($membersTable)->
            where('vacancyid=600')->queryAll();
        
        $sectionInstancesTable = '{{catalog_section_instances}}';
        $sectionInstances = $this->dbConnection->createCommand()->select()->from($sectionInstancesTable)->
            where(array('and', 'objectid=600', "objecttype='vacancy'"))->queryAll();
        
        $instancesTable = '{{member_instances}}';
        foreach ( $topModelMembers as $member )
        {
            foreach ( $sectionInstances as $sectionInstance )
            {
                $this->insert($instancesTable, array(
                    'objecttype' => 'section_instance',
                    'objectid'   => $sectionInstance['id'],
                    'memberid'   => $member['id'],
                    'sourcetype' => 'system',
                    'sourceid'   => 0,
                    'status'     => 'active',
                    'linktype'   => 'nolink',
                ));
            }
            
        }
    }
}