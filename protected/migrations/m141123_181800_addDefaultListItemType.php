<?php

class m141123_181800_addDefaultListItemType extends EcMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        // стандартизируем все оригиналы элементов
        $condition = "objecttype = 'item' OR ( objecttype = 'EasyListItem' AND objectid = id )";
        $columns   = array(
            'objecttype'  => '__item__',
            'objectId'    => 0,
            'objectfield' => null,
        );
        $this->update('{{easy_list_items}}', $columns, $condition);
        // изменяем тип элемента по умолчанию в соответствии с константой
        $this->alterColumn('{{easy_list_items}}', 'objecttype', "varchar(50) NOT NULL DEFAULT '__item__'");
        
        ///////////////////////////////////////////////////////////////////////
        // удаляем все мероприятия, которые ссылаются на несуществующие проекты
        $events = $this->dbConnection->createCommand()->select('*')->
            from('{{project_events}}')->queryAll();
        foreach ( $events as $event )
        {
            $project = $this->dbConnection->createCommand()->select('*')->
                from('{{projects}}')->where("id=".$event['projectid'])->queryRow();
            if ( ! $project )
            {// мероприятие ссылается на несуществующий проект
                // удаляем само мероприятие
                $this->delete('{{project_events}}', 'id='.$event['id']);
                // удаляем возможные ссылки на него
                $this->update('{{project_events}}', array('parentid' => 0), 'parentid='.$event['id']);
                // удаляем возможные роли
                $vacancies = $this->dbConnection->createCommand()->select('*')->
                    from('{{event_vacancies}}')->where('eventid='.$event['id'])->queryAll();
                foreach ( $vacancies as $vacancy )
                {
                    $this->delete('{{event_vacancies}}', 'id='.$vacancy['id']);
                    $this->delete('{{project_members}}', 'vacancyid='.$vacancy['id']);
                }
                // приглашения
                $this->delete('{{event_invites}}', 'eventid='.$event['id']);
            }
        }
        
        $this->update('{{config}}', array('name' => 'emailBanner'), "name='emailBannerUrl'");
    }
}