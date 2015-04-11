<?php

class m150322_101200_initMetadata extends EcMigration
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
            array('ArAttribute', 'attributes', time(), 0, 'Свойство', 'Произвольное свойство которое можно добавить к любому объекту. Также может являться контейнером для хранения других свойств (то есть у свойств могут быть свойства и так далее). Служебный объект.'),
            array('ArModelAttribute', 'model_attributes', time(), 0, 'Дополнительное свойство объекта', 'Связывает свойство с AR-моделью. Служебный объект.'),
            array('ArAttributeValue', 'attribute_values', time(), 0, 'Значение свойства объекта', 'Связывает тип объекта, id объекта, свойство объекта, тип значения объекта и само значение. Служебный объект.'),
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
            //array('ArEventLauncher', 'events_launchers', time(), 0, 'Источник события', 'Объект-источник запускающий системное событие (launcher) в ответ на действия, пользователя изменения в таблицах и т. д.'),
            array('ArEntity', 'entities', time(), 0, 'Объект произвольной структуры (сущность)', 'Объект в котором может содержатся любой набор свойств любой структуры. Служебный объект.'),
            array('ArSystemScript', 'system_scripts', time(), 0, 'Системный скрипт', 'Служебный объект'),
            array('ArController', 'controllers', time(), 0, 'Контроллер приложения', 'Служебный объект'),
            array('ArControllerAction', 'controller_actions', time(), 0, 'Действие контроллера приложения', 'Служебный объект'),
        );
        $arModelIds = array();
        foreach ( $arModels as $arModel )
        {
            $arModelData = array(
                'model'         => $arModel[0],
                'table'         => $arModel[1],
                'timecreated'   => $arModel[2],
                'timemodified'  => $arModel[3],
                'title'         => $arModel[4],
                'description'   => $arModel[5],
                'system'        => 1,
                'defaultformid' => 0,
            );
            $this->insert($table, $arModelData);
            $arModelIds[$arModelData['model']] = $this->getDbConnection()->lastInsertID;
        }
        unset($table);
        
        // добавляем связи между классами моделей чтобы можно было извлекать записи при помощи API Yii
        $table   = "{{ar_relations}}";
        $relations = array(
            // model
            array(
                'modelid'       => $arModelIds['ArRelation'],
                'name'          => 'defaultForm',
                'type'          => CActiveRecord::BELONGS_TO,
                'fk0'           => 'defaultformid',
                'relatedmodel'  => 'ArForm',
                'title'         => 'Форма, по-умолчанию используемая для создания и редактирования всех объектов этого типа',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArModel'],
                'name'          => 'carmaRelations',
                'type'          => CActiveRecord::HAS_MANY,
                'fk0'           => 'modelid',
                'relatedmodel'  => 'ArRelation',
                'title'         => 'Связи модели [relations()] определенные в базе данных плагином Carma', // Кармические связи, да :)
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArModel'],
                'name'          => 'carmaRules',
                'type'          => CActiveRecord::HAS_MANY,
                'fk0'           => 'modelid',
                'relatedmodel'  => 'ArRule',
                'title'         => 'Правила проверки данных для полей модели [rules()] определенные в базе данных плагином Carma',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArModel'],
                'name'          => 'expectedInAttributes',
                'type'          => CActiveRecord::HAS_MANY,
                'fk0'           => 'valuetypemodelid',
                'relatedmodel'  => 'ArAttribute',
                'title'         => 'Все дополнительные свойства в которых этот объект используется как ожидаемый тип значения',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArModel'],
                'name'          => 'relatedValues',
                'type'          => CActiveRecord::HAS_MANY,
                'fk0'           => 'valuemodelid',
                'relatedmodel'  => 'ArAttributeValue',
                'title'         => 'Все значения всех дополнительных свойств связанные с любым объектом этого типа',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArModel'],
                'name'          => 'arEventListeners',
                'type'          => CActiveRecord::HAS_MANY,
                'fk0'           => 'listenerid',
                'relatedmodel'  => 'ArEventListener',
                'title'         => 'Отслеживаемые события',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            //array(
            //    'modelid'       => $arModelIds['ArModel'],
            //    'name'          => 'arEventLaunchers',
            //    'type'          => CActiveRecord::HAS_MANY,
            //    'fk0'           => 'listenerid',
            //    'relatedmodel'  => 'ArEventLauncher',
            //    'title'         => 'Генерируемые события',
            //    'description'   => '',
            //    'timecreated'   => time(),
            //    'timemodified'  => 0,
            //),
            array(
                'modelid'       => $arModelIds['ArModel'],
                'name'          => 'arEntities',
                'type'          => CActiveRecord::HAS_MANY,
                'fk0'           => 'modelid',
                'relatedmodel'  => 'ArEntity',
                'title'         => 'Объекты с произвольной структурой данных [Entity] использующие эту модель',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArModel'],
                'name'          => 'arModelAttributes',
                'type'          => CActiveRecord::HAS_MANY,
                'fk0'           => 'modelid',
                'condition'     => '`arModelAttributes`.`objectid` = 0',
                'relatedmodel'  => 'ArModelAttribute',
                'title'         => 'Все дополнительные свойства прикрепленные к каждому объекту этого типа',
                'description'   => 'Могут использоваться как обычные поля в объекте. К любому объекту можно добавить неограниченное количество дополнительных свойств. У объекта не может быть двух свойств с одинаковым именем [name].',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArModel'],
                'name'          => 'incomingPointers',
                'type'          => CActiveRecord::HAS_MANY,
                'fk0'           => 'recordid',
                'condition'     => "`incomingPointers`.`modelid` = {$arModelIds['ArModel']}",
                'relatedmodel'  => 'ArPointer',
                'title'         => 'Указатели ссылающиеся на эту модель',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            // relation
            array(
                'modelid'       => $arModelIds['ArRelation'],
                'name'          => 'model',
                'type'          => CActiveRecord::BELONGS_TO,
                'fk0'           => 'modelid',
                'relatedmodel'  => 'ArModel',
                'title'         => 'Тип объекта к которому относится связь',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            // rule
            array(
                'modelid'       => $arModelIds['ArRule'],
                'name'          => 'model',
                'type'          => CActiveRecord::BELONGS_TO,
                'fk0'           => 'modelid',
                'relatedmodel'  => 'ArModel',
                'title'         => 'Тип объекта относится правило',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            // template
            // widget
            array(
                'modelid'       => $arModelIds['ArWidget'],
                'name'          => 'template',
                'type'          => CActiveRecord::BELONGS_TO,
                'fk0'           => 'templateid',
                'relatedmodel'  => 'ArTemplate',
                'title'         => 'Шаблон разметки виджета',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArWidget'],
                'name'          => 'configData',
                'type'          => CActiveRecord::BELONGS_TO,
                'fk0'           => 'configdataid',
                'relatedmodel'  => 'ArValueJson',
                'title'         => 'Требуемые виджетом данные',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            // pointer
            array(
                'modelid'       => $arModelIds['ArPointer'],
                'name'          => 'model',
                'type'          => CActiveRecord::BELONGS_TO,
                'fk0'           => 'modelid',
                'relatedmodel'  => 'ArModel',
                'title'         => 'Тип объекта на который указывает ссылка',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            // attribute
            array(
                'modelid'       => $arModelIds['ArAttribute'],
                'name'          => 'parent',
                'type'          => CActiveRecord::BELONGS_TO,
                'fk0'           => 'parentid',
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
                'fk0'           => 'parentid',
                'relatedmodel'  => 'ArAttribute',
                'title'         => 'Дочерние свойства',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArAttribute'],
                'name'          => 'valueTypeModel',
                'type'          => CActiveRecord::BELONGS_TO,
                'fk0'           => 'valuetypemodelid',
                'relatedmodel'  => 'ArModel',
                'title'         => 'Ожидаемый тип значения для свойства',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArAttribute'],
                'name'          => 'attributeValues',
                'type'          => CActiveRecord::HAS_MANY,
                'fk0'           => 'attributeid',
                'relatedmodel'  => 'ArAttributeValue',
                'title'         => 'Указатели на значения',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            // model attribute
            array(
                'modelid'       => $arModelIds['ArModelAttribute'],
                'name'          => 'model',
                'type'          => CActiveRecord::BELONGS_TO,
                'fk0'           => 'modelid',
                'relatedmodel'  => 'ArModel',
                'title'         => 'Тип объекта к которому прикрепляется свойство',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArModelAttribute'],
                'name'          => 'arAttribute',
                'type'          => CActiveRecord::BELONGS_TO,
                'fk0'           => 'attributeid',
                'relatedmodel'  => 'ArAttribute',
                'title'         => 'Cвойство объекта',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            // attribute value
            array(
                'modelid'       => $arModelIds['ArAttributeValue'],
                'name'          => 'arAttribute',
                'type'          => CActiveRecord::BELONGS_TO,
                'fk0'           => 'attributeid',
                'relatedmodel'  => 'ArAttribute',
                'title'         => 'Cвойство объекта',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArAttributeValue'],
                'name'          => 'valueTypeModel',
                'type'          => CActiveRecord::BELONGS_TO,
                'fk0'           => 'valuemodelid',
                'relatedmodel'  => 'ArModel',
                'title'         => 'Ожидаемый тип значения для свойства',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            // meta link
            array(
                'modelid'       => $arModelIds['ArMetaLink'],
                'name'          => 'linkPointer',
                'type'          => CActiveRecord::BELONGS_TO,
                'fk0'           => 'linkpointerid',
                'relatedmodel'  => 'ArPointer',
                'title'         => 'Медиатор (тип или контекст связи между объектами)',
                'description'   => 'Задает информацию о том в каком качестве (и вообще зачем) один объект связывается со вторым',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArMetaLink'],
                'name'          => 'sourcePointer',
                'type'          => CActiveRecord::BELONGS_TO,
                'fk0'           => 'sourcepointerid',
                'relatedmodel'  => 'ArPointer',
                'title'         => 'Источник ссылки (что связываем)',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArMetaLink'],
                'name'          => 'targetPointer',
                'type'          => CActiveRecord::BELONGS_TO,
                'fk0'           => 'targetpointerid',
                'relatedmodel'  => 'ArPointer',
                'title'         => 'Назначение ссылки (с чем связываем)',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            // value json
            // value int
            // value string
            // value text
            // value boolean
            // value float
            // form
            array(
                'modelid'       => $arModelIds['ArForm'],
                'name'          => 'fields',
                'type'          => CActiveRecord::HAS_MANY,
                'fk0'           => 'formid',
                'relatedmodel'  => 'ArFormField',
                'title'         => 'Поля формы',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            // form field
            array(
                'modelid'       => $arModelIds['ArFormField'],
                'name'          => 'form',
                'type'          => CActiveRecord::BELONGS_TO,
                'fk0'           => 'formid',
                'relatedmodel'  => 'ArForm',
                'title'         => 'Форма',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            // event
            array(
                'modelid'       => $arModelIds['ArEvent'],
                'name'          => 'listeners',
                'type'          => CActiveRecord::HAS_MANY,
                'fk0'           => 'eventid',
                'relatedmodel'  => 'ArEventListener',
                'title'         => 'Объекты отслеживающие это событие',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArEvent'],
                'name'          => 'launchers',
                'type'          => CActiveRecord::HAS_MANY,
                'fk0'           => 'eventid',
                'relatedmodel'  => 'ArEventLauncher',
                'title'         => 'Объекты запускающие это событие',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            // event listener
            array(
                'modelid'       => $arModelIds['ArEventListener'],
                'name'          => 'event',
                'type'          => CActiveRecord::BELONGS_TO,
                'fk0'           => 'eventid',
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
                'fk0'           => 'launcherid',
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
                'fk0'           => 'listenerid',
                'relatedmodel'  => 'ArModel',
                'title'         => 'Тип объекта-наблюдателя',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            // entity
            array(
                'modelid'       => $arModelIds['ArEntity'],
                'name'          => 'parent',
                'type'          => CActiveRecord::BELONGS_TO,
                'fk0'           => 'parentid',
                'relatedmodel'  => 'ArEntity',
                'title'         => 'Объект от которого наследуется структура полей',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArEntity'],
                'name'          => 'children',
                'type'          => CActiveRecord::HAS_MANY,
                'fk0'           => 'parentid',
                'relatedmodel'  => 'ArEntity',
                'title'         => 'Объекты, наследующие структуру полей',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArEntity'],
                'name'          => 'model',
                'type'          => CActiveRecord::BELONGS_TO,
                'fk0'           => 'modelid',
                'relatedmodel'  => 'ArModel',
                'title'         => 'Тип объекта',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            // controller actions
            array(
                'modelid'       => $arModelIds['ArControllerAction'],
                'name'          => 'arController',
                'type'          => CActiveRecord::BELONGS_TO,
                'fk0'           => 'controllerid',
                'relatedmodel'  => 'ArControll',
                'title'         => 'Контроллер',
                'description'   => '',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            array(
                'modelid'       => $arModelIds['ArController'],
                'name'          => 'arActions',
                'type'          => CActiveRecord::HAS_MANY,
                'fk0'           => 'controllerid',
                'relatedmodel'  => 'ArControllerAction',
                'title'         => 'Действия контроллера',
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
// event launcher
/*array(
    'modelid'       => $arModelIds['ArEventLauncher'],
    'name'          => 'event',
    'type'          => CActiveRecord::BELONGS_TO,
    'fk0'           => 'eventid',
    'relatedmodel'  => 'ArEvent',
    'title'         => 'Запускаемое событие',
    'description'   => '',
    'timecreated'   => time(),
    'timemodified'  => 0,
),
array(
    'modelid'       => $arModelIds['ArEventLauncher'],
    'name'          => 'model',
    'type'          => CActiveRecord::BELONGS_TO,
    'fk0'           => 'launcherid',
    'relatedmodel'  => 'ArModel',
    'title'         => 'Тип объекта запускающего событие',
    'description'   => '',
    'timecreated'   => time(),
    'timemodified'  => 0,
),*/