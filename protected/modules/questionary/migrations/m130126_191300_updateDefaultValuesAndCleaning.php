<?php

/**
 * Переносит все значения по умолчанию в таблицу activity_types
 * Добавляет поле для перевода и языка
 * Убирает устаревшие таблицы и поля
 */
class m130126_191300_updateDefaultValuesAndCleaning extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';

    public function safeUp()
    {
        $questionaryTable = Yii::app()->getModule('questionary')->questionaryTable;
        $typesTable = '{{q_activity_types}}';
        $oldComplexValuesTable ='{{questionary_complex_values}}';
        
        $this->dropTable($oldComplexValuesTable);
        
        $this->dropIndex('idx_mainpictureid', $questionaryTable);
        $this->dropColumn($questionaryTable, 'mainpictureid');
        
        $this->addColumn($typesTable, 'translation', "VARCHAR(255) DEFAULT NULL");
        $this->addColumn($typesTable, 'language', "VARCHAR(5) DEFAULT NULL");
        
        $this->createIndex('idx_translation', $typesTable, 'translation');
        $this->createIndex('idx_language', $typesTable, 'language');
        
        
        // переносим все языковые строки в базу
        Yii::import('application.modules.questionary.models.QActivityType');
        Yii::import('application.modules.questionary.models.Questionary');
        Yii::import('application.modules.questionary.QuestionaryModule');
        $language = 'ru';
        $values = array();

        // типы внешности
        $name = 'looktype';
        $translations = array(
        'slavonic' => 'Славянский',
        'east' => 'Восточный',
        'semitic' => 'Семитский',
        'caucasian' => 'Кавказский',
        'asian' => 'Азиатский',
        'african' => 'Африканский',
        'baltic' => 'Прибалтийский',
        'medit' => 'Средиземноморский',
        'european' => 'Европейский',);
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
        
        // цвет волос
        $name = 'haircolor';
        $translations = array(
        'brunet' => 'Брюнет',
        'fair' => 'Русый',
        'blond' => 'Блондин',
        'ginger' => 'Рыжий',
        'brown' => 'Шатен',
        'ashen' => 'Пепельный',
        'hoar' => 'Седой',
        'hairless' => 'Лысый',
        'darkfair' => 'Темно-русый',);
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
                        
        
        // цвет глаз
        $name = 'eyecolor';
        $translations = array(
        'gray' => 'Серый',
        'azure' => 'Голубой',
        'blue' => 'Синий',
        'green' => 'Зеленый',
        'hazel' => 'Карий',
        'yellow' => 'Желтый',
        'grayazure' => 'Серо-голубой',
        'graygreen' => 'Серо-зеленый',
        'hazelgreen' => 'Каре-зеленый',);
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
        
        // телосложение
        $name = 'physiquetype';
        $translations = array(
        'slim' => 'Худощавое',
        'model' => 'Модельное',
        'normal' => 'Нормальное',
        'sport' => 'Спортивное',
        'dense' => 'Плотное',
        'verydense' => 'Очень плотное',);
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
        
        // дополнительные характеристики
        $name = 'addchar';
        $translations = array(
        'twins' => 'Близнецы',
        'doubles' => 'Двойняшки',
        'triples' => 'Тройняшки',);
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
        
        // виды танца
        $name = 'dancetype';
        $translations = array(
        'classic' => 'Классический',
        'modern' => 'Современный',
        'russian' => 'Русско-народный',
        'jazzmodern' => 'Джаз-модерн',
        'step' => 'Чечетка',
        'tango' => 'Танго',
        'waltz' => 'Вальс',
        'latina' => 'Латиноамеринанский',
        'breakdance' => 'Брейк-данс',);
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
        
        // стриптиз
        $name = 'striptype';
        $translations = array(
        'topless' => 'Топлесс',
        'full' => 'Полностью',);
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
        
        // тип вокала
        $name = 'vocaltype';
        $translations = array(
        'classic' => 'Классический',
        'folk' => 'Народный',
        'pop' => 'Эстрадный',
        'jazz' => 'Джазовый',
        'bard' => 'Бардовская песня',);
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
        
        // тембр голоса
        $name = 'voicetimbre';
        $translations = array(
        'thenor' => 'Тенор',
        'alt' => 'Альт',
        'baritone' => 'Баритон',
        'soprano' => 'Сопрано',
        'mezzosoprano' => 'Меццо-сопрано',
        'bass' => 'Бас',);
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
        
        // музыкальные инструменты
        $name = 'instrument';
        $translations = array(
        'piano' => 'Пианино',
        'guitar' => 'Гитара',
        'drums' => 'Барабаны',
        'balalayka' => 'Балалайка',
        'viola' => 'Скрипка',
        'harp' => 'Арфа',
        'cello' => 'Виолончель',
        'contrabass' => 'Контрабас',
        'tube' => 'Труба',
        'flute' => 'Флейта',
        'sax' => 'Саксофон',
        'accordeon' => 'Аккордеон',
        'bayan' => 'Баян',
        'gijack' => 'Гиджак',
        'kemancha' => 'Кеманча',
        'goosli' => 'Гусли',
        'clavesin' => 'Клавесин',
        'litavrs' => 'Литавры',
        'merinbelen' => 'Ксилофон',
        'buben' => 'Бубен',
        'termrnvox' => 'Терменвокс',
        'emiriton' => 'Эмиритон',
        'organ' => 'Орган',
        'balotin' => 'Балотин',
        'valtorn' => 'Валторна',
        'clarnet' => 'Кларнет',
        'castanietos' => 'Кастаньеты',
        'darbuca' => 'Дарбука',
        'dijereedoo' => 'Диджериду',
        'celesta' => 'Челеста',
        'bells' => 'Колокольчики',
        'spoons' => 'Ложки',
        'marakas' => 'Маракас',
        'electroguitar' => 'Электрогитара',
        'bassguitar' => 'Бас-гитара',
        );
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
        
        // виды спорта
        $name = 'sporttype';
        $translations = array(
        'swimming' => 'Плавание',
        'fencing' => 'Фехтование',
        'gymnastics' => 'Художественная гимнастика',
        'hockey' => 'Хоккей',
        'acrobatics' => 'Акробатика',
        'riding' => 'Верховая езда',
        'football' => 'Футбол',
        'basketball' => 'Баскетбол',
        'figureskating' => 'Фигурное катание',
        'tennis' => 'Теннис',
        'rolls' => 'Ролики',
        'wrestling' => 'Борьба',
        'clumbing' => 'Скалолазание',);
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
        
        // экстремальные виды спорта
        $name = 'extremaltype';
        $translations = array(
        'skateboarding' => 'Скейтбординг',
        'snowboard' => 'Сноуборд',
        'bike' => 'Мотоспорт',
        'rolls' => 'Агресс. ролики',
        'wakeboard' => 'Вейкборд',
        'rally' => 'Ралли',
        'basejump' => 'Бейс-джамп',
        'parachute' => 'Прыжки с парашютом',
        'stant' => 'Стант',
        'parkour' => 'Паркур',
        'surfing' => 'Серфинг',
        'bmx' => 'BMX',
        'kite' => 'Кайт',);
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
        
        // Умения, навыки
        $name = 'skill';
        $translations = array(
        'cardriving' => 'Вождение автомобиля',
        'bikedriving' => 'Вождение мотоцикла',);
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
        
        // иностранные языки
        $name = 'language';
        $translations = array(
        'english' => 'Английский',
        'french' => 'Французкий',
        'german' => 'Немецкий',
        'spanish' => 'Испанский',
        'italian' => 'Итальянский',
        'hebrew' => 'Иврит',);
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
        
        // уровень владения
        $name = 'level';
        $translations = array(
        'amateur' => 'Любитель',
        'professional' => 'Профессионал',);
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
        
        $name = 'singlevel';
        $translations = array(
        'amateur' => 'Любитель',
        'professional' => 'Профессионал',);
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
        
        $name = 'striplevel';
        $translations = array(
        'amateur' => 'Любитель',
        'professional' => 'Профессионал',);
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
        
        $name = 'languagelevel';
        $translations = array(
        'professional' => 'Свободно',
        'amateur' => 'Читать и объясняться',);
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
        
        // пол
        $name = 'gender';
        $translations = array(
        'male'   => 'Мужской',
        'female' => 'Женский',);
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
        
        // размер одежды
        $name = 'wearsize';
        $translations = array(
            Questionary::WEARSIZE_MIN => QuestionaryModule::t('less').' 36',
            '36-38' => '36-38',
            '38-40' => '38-40',
            '40-42' => '40-42',
            '42-44' => '42-44',
            '44-46' => '44-46',
            '46-48' => '46-48',
            '48-50' => '48-50',
            '50-52' => '50-52',
            '52-54' => '52-54',
            '54-56' => '54-56',
            Questionary::WEARSIZE_MAX => QuestionaryModule::t('more').' 56',
        );
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
        
        // Размер обуви
        $name = 'shoessize';
        $translations = array(
            Questionary::SHOESSIZE_MIN => QuestionaryModule::t('less').' 36',
            '36' => '36',
            '37' => '37',
            '38' => '38',
            '39' => '39',
            '40' => '40',
            '41' => '41',
            '42' => '42',
            '43' => '43',
            '44' => '44',
            '45' => '45',
            Questionary::SHOESSIZE_MAX => QuestionaryModule::t('more').' 45',
        );
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
        
        // Размер груди
        $name = 'titsize';
        $translations = array(
            'AA' => '0 (AA)',
            'A' => '1 (A)',
            'B' => '2 (B)',
            'C' => '3 (C)',
            'D' => '4 (D)',
            'DD' => '5 (DD)',
        );
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
        
        $name = 'rating';
        $translations = array(
            0 => Yii::t('coreMessages', 'no'),
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
        );
        foreach ( $translations as $value=>$translation )
        {
            $values[] = array('name'=>$name,'value'=>$value,'translation'=>$translation,'language'=>$language);
        }
        unset($translations);
        
        // Очищаем все старые значения
        $this->truncateTable($typesTable);
        
        foreach ($values as $value)
        {
            $this->insert($typesTable, $value);
        }
    }
}