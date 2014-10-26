<?php

class m141026_083200_fixNotificationMigration extends EcMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{config}}";
        $this->alterColumn($table, 'valuetype', "VARCHAR(50) DEFAULT NULL");
        $this->alterColumn($table, 'valuefield', "VARCHAR(50) DEFAULT NULL");
        
        $this->refreshTableSchema($table);
        
        $clearValueColumns = array(
            'valuetype'  => null,
            'valuefield' => null,
            'valueid'    => 0,
        );
        $condition = "objecttype='ProjectEvent' AND objectid>0 AND objectid <> 484 AND name='newInviteMailText'";
        $this->update($table, $clearValueColumns, $condition);
        
        $condition = "objecttype='ProjectEvent' AND objectid>0 AND name='customGreeting'";
        $this->update($table, $clearValueColumns, $condition);
        
        $condition = "objecttype='ProjectEvent' AND objectid>0 AND name='countdownTime'";
        $this->update($table, $clearValueColumns, $condition);
    }
}