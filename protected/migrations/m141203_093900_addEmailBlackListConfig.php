<?php

class m141203_093900_addEmailBlackListConfig extends EcMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        // исправляем id пользовательского списка проектов в настройке
        $typesConfig = $this->dbConnection->createCommand()->select('*')->
            from('{{config}}')->where("name='projectTypesBlackList' AND objectid=0")->queryRow();
        // обновляем название и описание для настройки оповещений
        $description  = 'Укажите типы проектов на которые нам следует вас приглашать. '; 
        $description .= 'Отключите приглашения на те съемки в которых вы не планируете участвовать. ';
        $columns = array(
            'title'           => 'Настройки приглашений (тип проекта)',
            'description'     => $description,
            'userlistid'      => 0,
            'allowuservalues' => 0,
        );
        $this->update('{{config}}', $columns, "name='projectTypesBlackList'");
        
        // обновляем id списков для настройки "Отключенные email-адреса"
        $emailBlackList = $this->dbConnection->createCommand()->select('*')->from('{{config}}')->
            where("name='emailBlackList' AND objectid=0 AND objecttype='system'")->queryRow();
        $this->update('{{config}}', array(
            'userlistid'  => 30,
            'title'       => 'Отключенные email-адреса',
            'description' => 'На адреса в этом списке не будут отправляться никакие письма',
            'allowuservalues' => 1,
        ), "id={$emailBlackList['id']}");
    }
}