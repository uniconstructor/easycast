<?php

class m140826_100000_clearBrokenInstances extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $memberInstances = $this->dbConnection->createCommand()->select('id,objectid')->
            from("{{member_instances}}")->where("objecttype='section_instance'")->queryAll();
        foreach ( $memberInstances as $memberInstance )
        {
            $catalogSectionInstance = $this->dbConnection->createCommand()->select('id')->
                from("{{catalog_section_instances}}")->where("id=".$memberInstance['objectid'])->queryRow();
            if ( ! $catalogSectionInstance )
            {// ссылка на битую запись вкладки каталога - удаляем ее
                $this->dbConnection->createCommand()->
                    delete("{{member_instances}}", "id=".$memberInstance['id'].
                        " OR (objecttype='section_instance' AND objectid=".$memberInstance['objectid'].")");
            }
        }
    }
}