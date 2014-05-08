<?php

class m140504_060900_addBadgesAndFields extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{q_activity_types}}";
        
        $badges = array();
        $badges[] = array('name' => 'badge', 'value' => 'isactor', 'translation' => 'Актер', 'language' => 'ru');
        $badges[] = array('name' => 'badge', 'value' => 'isemcee', 'translation' => 'Ведущий', 'language' => 'ru');
        $badges[] = array('name' => 'badge', 'value' => 'isparodist', 'translation' => 'Пародист', 'language' => 'ru');
        $badges[] = array('name' => 'badge', 'value' => 'istwin', 'translation' => 'Двойник', 'language' => 'ru');
        $badges[] = array('name' => 'badge', 'value' => 'ismodel', 'translation' => 'Модель', 'language' => 'ru');
        $badges[] = array('name' => 'badge', 'value' => 'isdancer', 'translation' => 'Танцор', 'language' => 'ru');
        $badges[] = array('name' => 'badge', 'value' => 'isstripper', 'translation' => 'Стриптиз', 'language' => 'ru');
        $badges[] = array('name' => 'badge', 'value' => 'issinger', 'translation' => 'Вокал', 'language' => 'ru');
        $badges[] = array('name' => 'badge', 'value' => 'ismusician', 'translation' => 'Музыкант', 'language' => 'ru');
        $badges[] = array('name' => 'badge', 'value' => 'issportsman', 'translation' => 'Спортсмен', 'language' => 'ru');
        $badges[] = array('name' => 'badge', 'value' => 'isextremal', 'translation' => 'Экстремал', 'language' => 'ru');
        $badges[] = array('name' => 'badge', 'value' => 'isathlete', 'translation' => 'Атлет', 'language' => 'ru');
        $badges[] = array('name' => 'badge', 'value' => 'hastricks', 'translation' => 'Каскадер', 'language' => 'ru');
        $badges[] = array('name' => 'badge', 'value' => 'isphotomodel', 'translation' => 'Фотомодель', 'language' => 'ru');
        $badges[] = array('name' => 'badge', 'value' => 'ispromomodel', 'translation' => 'Промо-модель', 'language' => 'ru');
        $badges[] = array('name' => 'badge', 'value' => 'isamateuractor', 'translation' => 'Непрофессиональный актер', 'language' => 'ru');
        $badges[] = array('name' => 'badge', 'value' => 'istvshowmen', 'translation' => 'Телеведущий', 'language' => 'ru');
        $badges[] = array('name' => 'badge', 'value' => 'isstatist', 'translation' => 'Статист', 'language' => 'ru');
        $badges[] = array('name' => 'badge', 'value' => 'ismassactor', 'translation' => 'Артист массовых сцен', 'language' => 'ru');
        $badges[] = array('name' => 'badge', 'value' => 'istheatreactor', 'translation' => 'Актер театра', 'language' => 'ru');
        $badges[] = array('name' => 'badge', 'value' => 'ismediaactor', 'translation' => 'Медийный актер', 'language' => 'ru');
        
        foreach ( $badges as $badge )
        {
            $this->insert($table, $badge);
        }
    }
}