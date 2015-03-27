<?php

class m150322_101200_initMetadata extends CDbMigration
{
    /**
     * @see parent::safeUp()
     */
    public function safeUp()
    {
        // регистрируем модели пока формы еще не созданы
        $table   = "{{ar_models}}";
        $arModels = array(
            array('ArModel', 'models', time(), 0, 'Метаданные AR-модели', 'Системная информация о структуре записей'),
            array('ArRelation', 'relations', time(), 0, 'Связь между моделями', 'Настройки для создания одной связи между двумя моделями'),
            array('ArRule', 'rules', time(), 0, 'Правило проверки данных для полей модели', 'Привязывается к одному или нескольким полям модели и позволяет настраивать проверку или фильтрацию вводимых данных'),
            array('ArTemplate', 'templates', time(), 0, 'Шаблон разметки', 'Текст шаблона веб-страницы (HTML+PHP+Twig). Содержит структуру веб-страницы или небольшую часть такой разметки (например виджет). Максимальный размер 64кб. Шаблоны можно вкладывать друг в друга.'),
            array('ArWidget', 'widgets', time(), 0, 'Виджет с настраиваемой разметкой', 'Виджет, внешний вид которого можно редактировать. Можно создавать свои виджеты и вкладывать внутрь них уже существующие.'),
            array('ArPointer', 'pointers', time(), 0, 'Указатель на модель данных', 'Служебный объект'),
            array('ArAttribute', 'attributes', time(), 0, 'Дополнительное поле объекта', 'Контейнер для хранения данных со сложной структурой. Служебный объект.'),
            array('ArMetaLink', 'meta_links', time(), 0, 'Связь между двумя моделями', 'Позволяет связать между собой два любых объекта любого типа. Также позволяет связывать между собой поля объектов и значения внутри полей - в любой комбинации.'),
            array('ArValueJson', 'value_json', time(), 0, 'Значение поля: json-массив', 'Служебный объект для хранения значений.Служебный объект для хранения значений.'),
            array('ArValueInt', 'value_int', time(), 0, 'Значение поля: целое число', 'Служебный объект для хранения значений.Служебный объект для хранения значений.'),
            array('ArValueString', 'value_string', time(), 0, 'Значение поля: строка (до 255 символов)', 'Служебный объект для хранения значений.Служебный объект для хранения значений.'),
            array('ArValueText', 'value_text', time(), 0, 'Значение поле: большой текст (до 64кб)', 'Служебный объект для хранения значений.Служебный объект для хранения значений.'),
            array('ArValueBoolean', 'value_boolean', time(), 0, 'Значение поля: да/нет', 'Служебный объект для хранения значений.Служебный объект для хранения значений.'),
            array('ArValueFloat', 'value_float', time(), 0, 'Значение поля: дробное число', 'Служебный объект для хранения значений.'),
            array('ArForm', 'forms', time(), 0, 'Форма', 'Форма для ввода данных. Состоит из полей ввода или других виджетов. Может быть собрана автоматически под объект или настроена вручную.'),
            array('ArFormField', 'form_fields', time(), 0, 'Поле формы', 'Виджет одного поля формы: например текстовая строка или выпадающий список. Хранит информацию о том в каком порядке какие поля в каких формах находятся.'),
            array('ArEvent', 'events', time(), 0, 'Системное событие', 'Этот объект хранит настройки для каждого типа события: название, описание и назначение, используемый событием PHP-класс.'),
            array('ArEventListener', 'events_listeners', time(), 0, 'Получатель события', 'Объект, отслеживающий возникновение событий. Он реагирует на системные события в зависимости от их типа совершая различные действия.'),
            array('ArEventLauncher', 'events_launchers', time(), 0, 'Источник события', 'Объект-источник запускающий системное событие (launcher) в ответ на действия, пользователя изменения в таблицах и т. д.'),
        );
        $arModelIds = array();
        foreach ( $arModels as $arModel )
        {
            $arModelData = array(
                'model'        => $arModel[0],
                'table'        => $arModel[1],
                'timecreated'  => $arModel[2],
                'timemodified' => $arModel[3],
                'title'        => $arModel[4],
                'description'  => $arModel[5],
            );
            $this->insert($table, $arModelData);
            $arModelIds[$arModelData['model']] = $this->getDbConnection()->lastInsertID;
        }
        unset($table);
        
        // добавляем связи между классами моделей чтобы можно было извлекать записи при помощи API Yii
        $table   = "{{ar_relations}}";
        $relations = array(
            array(
                'modelid'       => $arModelIds['ArWidget'],
                'name'          => 'template',
                'type'          => CActiveRecord::BELONGS_TO,
                'fkdata'        => 'templateid',
                'relatedmodel'  => 'ArTemplate',
                'title'         => 'Шаблон разметки виджета',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArWidget'],
                'name'          => 'configdata',
                'type'          => CActiveRecord::BELONGS_TO,
                'fkdata'        => 'configdataid',
                'relatedmodel'  => 'ArValueJson',
                'title'         => 'Требуемые виджетом данные',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArRelation'],
                'name'          => 'model',
                'type'          => CActiveRecord::BELONGS_TO,
                'fkdata'        => 'modelid',
                'relatedmodel'  => 'ArModel',
                'title'         => 'Тип объекта к которому относится связь',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArRule'],
                'name'          => 'model',
                'type'          => CActiveRecord::BELONGS_TO,
                'fkdata'        => 'modelid',
                'relatedmodel'  => 'ArModel',
                'title'         => 'Тип объекта относится правило',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArPointer'],
                'name'          => 'model',
                'type'          => CActiveRecord::BELONGS_TO,
                'fkdata'        => 'modelid',
                'relatedmodel'  => 'ArModel',
                'title'         => 'Тип объекта на который указывает ссылка',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArAttribute'],
                'name'          => 'parent',
                'type'          => CActiveRecord::BELONGS_TO,
                'fkdata'        => 'parentid',
                'relatedmodel'  => 'ArAttribute',
                'title'         => 'Свойство-контейнер',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArAttribute'],
                'name'          => 'children',
                'type'          => CActiveRecord::HAS_MANY,
                'fkdata'        => 'parentid',
                'relatedmodel'  => 'ArAttribute',
                'title'         => 'Дочерние свойства',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArMetaLink'],
                'name'          => 'sourcepointer',
                'type'          => CActiveRecord::BELONGS_TO,
                'fkdata'        => 'sourcepointerid',
                'relatedmodel'  => 'ArPointer',
                'title'         => 'Источник ссылки',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArMetaLink'],
                'name'          => 'targetpointer',
                'type'          => CActiveRecord::BELONGS_TO,
                'fkdata'        => 'targetpointerid',
                'relatedmodel'  => 'ArPointer',
                'title'         => 'Назначение ссылки',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArFormField'],
                'name'          => 'form',
                'type'          => CActiveRecord::BELONGS_TO,
                'fkdata'        => 'formid',
                'relatedmodel'  => 'ArForm',
                'title'         => 'Форма',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArEventListener'],
                'name'          => 'event',
                'type'          => CActiveRecord::BELONGS_TO,
                'fkdata'        => 'eventid',
                'relatedmodel'  => 'ArEvent',
                'title'         => 'Отслеживаемое событие',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArEventListener'],
                'name'          => 'launcher',
                'type'          => CActiveRecord::BELONGS_TO,
                'fkdata'        => 'launcherid',
                'relatedmodel'  => 'ArModel',
                'title'         => 'Отслеживаемый тип объекта',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArEventListener'],
                'name'          => 'model',
                'type'          => CActiveRecord::BELONGS_TO,
                'fkdata'        => 'modelid',
                'relatedmodel'  => 'ArModel',
                'title'         => 'Тип listener-объекта',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArEventLauncher'],
                'name'          => 'event',
                'type'          => CActiveRecord::BELONGS_TO,
                'fkdata'        => 'eventid',
                'relatedmodel'  => 'ArEvent',
                'title'         => 'Запускаемое событие',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArEventListener'],
                'name'          => 'model',
                'type'          => CActiveRecord::BELONGS_TO,
                'fkdata'        => 'modelid',
                'relatedmodel'  => 'ArModel',
                'title'         => 'Тип launcher-объекта',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
        );
        foreach ( $relations as $relation )
        {
            $this->insert($table, $relation);
        }
    }
}

