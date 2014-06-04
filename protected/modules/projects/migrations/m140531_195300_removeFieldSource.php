<?php

class m140531_195300_removeFieldSource extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{extra_field_instances}}";
        $this->dropColumn($table, 'fieldsource');
    }
}