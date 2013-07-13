<?php

/**
 * Установка таблиц для всех сложных значений
 * @author frost
 *
 */
class m121203_090000_installComplexValuesTables extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';

    public function safeUp()
    {
        if ( ! Yii::app()->getModule('questionary') ) {
            echo "\n\nAdd to console.php :\n"
            ."'modules'=>array(\n"
            ."...\n"
            ."    'questionary'=>array(\n"
            ."        ... # copy settings from main config\n"
            ."    ),\n"
            ."...\n"
            ."),\n"
            ."\n";
            return false;
        }
        
        /////////////////////////////////
        // Таблица "виды деятельности" //
        /////////////////////////////////
        
        $table = '{{q_activities}}';
        
        $fields = array(
            "id" => "pk",
            "questionaryid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            // тип характеристики или возможности пользователя (вид спорта, тип вокала, тембр голоса... и т. п.)
            "type" => "varchar(32) DEFAULT NULL",
            // значение характеристики (если оно стандартное)
            "value" => "varchar(32) DEFAULT NULL",
            // значение характеристики, заданное пользователем (если оно нестандартное. При этом value=custom)
            "uservalue" => "varchar(128) DEFAULT NULL",
            // уровень владения навыком (только для умений и навыков)
            "level" => "varchar(20) DEFAULT NULL",
            "timestart" => "int(11) UNSIGNED DEFAULT NULL",
            "timeend" => "int(11) UNSIGNED DEFAULT NULL",
            "timecreated" => "int(11) UNSIGNED DEFAULT NULL",
        );
        
        $this->createTable($table, $fields, $this->MySqlOptions);
        
        // получаем все имена полей чтобы потом создать индексы по каждому из них
        foreach ( $fields as $name => $type )
        {
            $this->createIndex('idx_'.$name, $table, $name);
        }
        
        /////////////////////////////
        // Типы видов деятельности //
        /////////////////////////////
        
        // Используется для генерации значений по умолчанию в select-списках
        
        $table = '{{q_activity_types}}';
        
        $fields = array(
            "id" => "pk",
            "name" => "varchar(20) DEFAULT NULL",
            "value" => "varchar(32) DEFAULT NULL",
        );
        
        $this->createTable($table, $fields, $this->MySqlOptions);
        
        // получаем все имена полей чтобы потом создать индексы по каждому из них
        foreach ( $fields as $name => $type )
        {
            $this->createIndex('idx_'.$name, $table, $name);
        }
        
        /////////////////
        // ВУЗы анкеты //
        /////////////////
        
        // Все ВУЗы в которых учился пользователь (привязка ВУЗа к анкете)
        
        $table = '{{q_university_instances}}';
        
        $fields = array(
            "id" => "pk",
            "questionaryid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "universityid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timestart" => "int(11) UNSIGNED DEFAULT NULL",
            "timeend" => "int(11) UNSIGNED DEFAULT NULL",
            "workshop" => "varchar(255) DEFAULT NULL",
            "timecreated" => "int(11) UNSIGNED DEFAULT NULL",
        );
        
        $this->createTable($table, $fields, $this->MySqlOptions);
        
        // получаем все имена полей чтобы потом создать индексы по каждому из них
        foreach ( $fields as $name => $type )
        {
            $this->createIndex('idx_'.$name, $table, $name);
        }
        
        //////////
        // ВУЗы //
        //////////
        
        $table = '{{q_universities}}';
        
        $fields = array(
            "id" => "pk",
            "type" => "enum('theatre', 'music') DEFAULT NULL",
            "name" => "varchar(128) DEFAULT NULL",
            "link" => "varchar(255) DEFAULT NULL",
        );
        
        $this->createTable($table, $fields, $this->MySqlOptions);
        
        // получаем все имена полей чтобы потом создать индексы по каждому из них
        foreach ( $fields as $name => $type )
        {
            $this->createIndex('idx_'.$name, $table, $name);
        }
        
        //////////////////
        // Фильмография //
        //////////////////
        
        // Фильмы в которых снимался актер (привязка фильма к анкете)
        
        $table = '{{q_film_instances}}';
        
        $fields = array(
            "id" => "pk",
            "questionaryid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "filmid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "role" => "varchar(128) DEFAULT NULL",
            "timecreated" => "int(11) UNSIGNED DEFAULT NULL",
        );
        
        $this->createTable($table, $fields, $this->MySqlOptions);
        
        // получаем все имена полей чтобы потом создать индексы по каждому из них
        foreach ( $fields as $name => $type )
        {
            $this->createIndex('idx_'.$name, $table, $name);
        }
        
        ////////////////////
        // Список фильмов //
        ////////////////////
        
        // Список всех фильмов всех актероы
        
        $table = '{{q_films}}';
        
        $fields = array(
            "id" => "pk",
            "externalid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "name" => "varchar(128) DEFAULT NULL",
            "date" => "int(11) UNSIGNED DEFAULT NULL",
            "director" => "varchar(128) DEFAULT NULL",
            "pictureid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
        );
        
        $this->createTable($table, $fields, $this->MySqlOptions);
        
        // получаем все имена полей чтобы потом создать индексы по каждому из них
        foreach ( $fields as $name => $type )
        {
            $this->createIndex('idx_'.$name, $table, $name);
        }
        
        ////////////////////////////
        // Звания, призы, награды //
        ////////////////////////////
        
        $table = '{{q_awards}}';
        
        $fields = array(
            "id" => "pk",
            "questionaryid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "name" => "varchar(128) DEFAULT NULL",
            "nomination" => "varchar(255) DEFAULT NULL",
            "countryid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "date" => "int(11) UNSIGNED DEFAULT NULL",
            "timecreated" => "int(11) UNSIGNED DEFAULT NULL",
            "timemodified" => "int(11) UNSIGNED DEFAULT NULL",
        );
        
        $this->createTable($table, $fields, $this->MySqlOptions);
        
        // получаем все имена полей чтобы потом создать индексы по каждому из них
        foreach ( $fields as $name => $type )
        {
            $this->createIndex('idx_'.$name, $table, $name);
        }
        
        //////////////////////////////////////////////
        // Установка изначальных значений в таблицы //
        //////////////////////////////////////////////
        
        // типы видов деятельности, навыков или характеристик
        $table = '{{q_activity_types}}';
        
        // дополнительные характеристики
        $addChars = array();
        $addChars[] = array('name' => 'addchar', 'value' => 'twins');
        $addChars[] = array('name' => 'addchar', 'value' => 'doubles');
        $addChars[] = array('name' => 'addchar', 'value' => 'triples');
        
        foreach ($addChars as $value)
        {
            $this->insert($table, $value);
        }
        
        // виды танца
        $danceTypes = array();
        $danceTypes[] = array('name' => 'dancetype', 'value' => 'classic');
        $danceTypes[] = array('name' => 'dancetype', 'value' => 'modern');
        $danceTypes[] = array('name' => 'dancetype', 'value' => 'russian');
        $danceTypes[] = array('name' => 'dancetype', 'value' => 'jazzmodern');
        $danceTypes[] = array('name' => 'dancetype', 'value' => 'step');
        $danceTypes[] = array('name' => 'dancetype', 'value' => 'tango');
        $danceTypes[] = array('name' => 'dancetype', 'value' => 'waltz');
        $danceTypes[] = array('name' => 'dancetype', 'value' => 'latina');
        $danceTypes[] = array('name' => 'dancetype', 'value' => 'breakdance');
        
        foreach ($danceTypes as $value)
        {
            $this->insert($table, $value);
        }
        
        // виды вокала
        $vocalTypes = array();
        $vocalTypes[] = array('name' => 'vocaltype', 'value' => 'classic');
        $vocalTypes[] = array('name' => 'vocaltype', 'value' => 'folk');
        $vocalTypes[] = array('name' => 'vocaltype', 'value' => 'pop');
        $vocalTypes[] = array('name' => 'vocaltype', 'value' => 'jazz');
        $vocalTypes[] = array('name' => 'vocaltype', 'value' => 'bard');
        
        foreach ($vocalTypes as $value)
        {
            $this->insert($table, $value);
        }
        
        // тембры голоса
        $voiceTimbres = array();
        $voiceTimbres[] = array('name' => 'voicetimbre', 'value' => 'thenor');
        $voiceTimbres[] = array('name' => 'voicetimbre', 'value' => 'alt');
        $voiceTimbres[] = array('name' => 'voicetimbre', 'value' => 'baritone');
        $voiceTimbres[] = array('name' => 'voicetimbre', 'value' => 'soprano');
        $voiceTimbres[] = array('name' => 'voicetimbre', 'value' => 'mezzosoprano');
        $voiceTimbres[] = array('name' => 'voicetimbre', 'value' => 'bass');
        
        foreach ($voiceTimbres as $value)
        {
            $this->insert($table, $value);
        }
        
        // музыкальные инструменты
        $instruments = array();
        $instruments[] = array('name' => 'instrument', 'value' => 'piano');
        $instruments[] = array('name' => 'instrument', 'value' => 'guitar');
        $instruments[] = array('name' => 'instrument', 'value' => 'drums');
        $instruments[] = array('name' => 'instrument', 'value' => 'balalayka');
        $instruments[] = array('name' => 'instrument', 'value' => 'viola');
        $instruments[] = array('name' => 'instrument', 'value' => 'harp');
        $instruments[] = array('name' => 'instrument', 'value' => 'cello');
        $instruments[] = array('name' => 'instrument', 'value' => 'contrabass');
        $instruments[] = array('name' => 'instrument', 'value' => 'tube');
        $instruments[] = array('name' => 'instrument', 'value' => 'flute');
        $instruments[] = array('name' => 'instrument', 'value' => 'sax');
        $instruments[] = array('name' => 'instrument', 'value' => 'accordeon');
        $instruments[] = array('name' => 'instrument', 'value' => 'bayan');
        
        foreach ($instruments as $value)
        {
            $this->insert($table, $value);
        }
        
        // виды спорта
        $sports = array();
        $sports[] = array('name' => 'sporttype', 'value' => 'swimming');
        $sports[] = array('name' => 'sporttype', 'value' => 'fencing');
        $sports[] = array('name' => 'sporttype', 'value' => 'gymnastics');
        $sports[] = array('name' => 'sporttype', 'value' => 'hockey');
        $sports[] = array('name' => 'sporttype', 'value' => 'acrobatics');
        $sports[] = array('name' => 'sporttype', 'value' => 'riding');
        $sports[] = array('name' => 'sporttype', 'value' => 'football');
        $sports[] = array('name' => 'sporttype', 'value' => 'basketball');
        $sports[] = array('name' => 'sporttype', 'value' => 'figureskating');
        $sports[] = array('name' => 'sporttype', 'value' => 'tennis');
        $sports[] = array('name' => 'sporttype', 'value' => 'rolls');
        $sports[] = array('name' => 'sporttype', 'value' => 'wrestling');
        $sports[] = array('name' => 'sporttype', 'value' => 'clumbing');
        
        foreach ($sports as $value)
        {
            $this->insert($table, $value);
        }
        
        // экстремальные виды спорта
        $extremalSports = array();
        $extremalSports[] = array('name' => 'extremaltype', 'value' => 'skateboarding');
        $extremalSports[] = array('name' => 'extremaltype', 'value' => 'snowboard');
        $extremalSports[] = array('name' => 'extremaltype', 'value' => 'bike');
        $extremalSports[] = array('name' => 'extremaltype', 'value' => 'rolls');
        $extremalSports[] = array('name' => 'extremaltype', 'value' => 'wakeboard');
        $extremalSports[] = array('name' => 'extremaltype', 'value' => 'rally');
        $extremalSports[] = array('name' => 'extremaltype', 'value' => 'basejump');
        $extremalSports[] = array('name' => 'extremaltype', 'value' => 'parachute');
        $extremalSports[] = array('name' => 'extremaltype', 'value' => 'stant');
        $extremalSports[] = array('name' => 'extremaltype', 'value' => 'parkour');
        $extremalSports[] = array('name' => 'extremaltype', 'value' => 'surfing');
        $extremalSports[] = array('name' => 'extremaltype', 'value' => 'bmx');
        $extremalSports[] = array('name' => 'extremaltype', 'value' => 'kite');
        
        foreach ($extremalSports as $value)
        {
            $this->insert($table, $value);
        }
        
        // умения и навыки
        $skills = array();
        $skills[] = array('name' => 'skill', 'value' => 'cardriving');
        $skills[] = array('name' => 'skill', 'value' => 'bikedriving');
        
        foreach ($skills as $value)
        {
            $this->insert($table, $value);
        }
        
        // иностранные языки
        $languages = array();
        $languages[] = array('name' => 'language', 'value' => 'english');
        $languages[] = array('name' => 'language', 'value' => 'french');
        $languages[] = array('name' => 'language', 'value' => 'german');
        $languages[] = array('name' => 'language', 'value' => 'spanish');
        $languages[] = array('name' => 'language', 'value' => 'italian');
        $languages[] = array('name' => 'language', 'value' => 'hebrew');
        
        foreach ($languages as $value)
        {
            $this->insert($table, $value);
        }
    }
}