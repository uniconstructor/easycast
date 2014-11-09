<?php

class m141106_120000_installSystemSchemas extends EcMigration
{
    /**
     * @var array
     *           update
     *           delete
     *           create
     */
    private $defaultSchemaActions = array(
        'create' => '/admin/documentSchema/create',
        'update' => '/admin/documentSchema/update',
        'delete' => '/admin/documentSchema/delete',
    );
    
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        /////////////////////////////////////////////////////////////////////
        // создание схемы: редактирование одного поля формы для поля схемы
        $this->createSystemSchema();
    }
    
    public function createSystemSchema()
    {
        // создаем форму
        $form = array(
            'title'            => 'Форма служебной схемы документа: параметры одного поля формы',
            'description'      => 'Служебная форма, не изменяйте ее структуру.',
            'action'           => '',
            'ajaxvalidation'   => 1,
            'clientvalidation' => 0,
            'displaytype'      => 'vertical',
        );
        $form = $this->createSchemaForm($form);
        
        // создаем новое описание схемы
        $schema = array(
            'type'        => 'DocumentSchemaFieldFormStructure', // да - покороче никак
            'title'       => 'Форма настройки формы схемы',
            'description' => 'Служебная схема. Описывает структуру формы для настройки 
                внешнего вида редактора схемы. Да, трудно объяснить нормальным языком что делает эта 
                штука - просто не трогайте ее. :)',
            'formid'      => $form['id'],
        );
        // сохраняем схему, привязав к ней форму
        $schema = $this->createSchemaObject($schema);
        
        // добавляем поля для новой схемы
        $extraFields = array(
            'title' => array(
                'name'  => 'title',
                'type'  => 'string',
                'title' => 'Заголовок формы',
            ),
            'description' => array(
                'name'  => 'description',
                'type'  => 'string',
                'title' => 'Описание формы',
            ),
            'action' => array(
                'name'  => 'action',
                'type'  => 'string',
                'title' => 'Адрес для обработки данных формы (относительный)',
            ),
            'ajaxvalidation' => array(
                'name'  => 'ajaxvalidation',
                'type'  => 'bool',
                'title' => 'Включить AJAX-проверку формы',
            ),
            'clientvalidation' => array(
                'name'  => 'clientvalidation',
                'type'  => 'bool',
                'title' => 'Включить проверку на стороне пользователя',
            ),
        );
        // указываем тип поля формы для каждого
        $formFields = array(
            'title' => array(
                'name' => 'title',
                'type' => 'text',
            ),
            'description' => array(
                'name' => 'description',
                'type' => 'html5editor',
            ),
            'action' => array(
                'name' => 'action',
                'type' => 'text',
            ),
            'ajaxvalidation' => array(
                'name' => 'ajaxvalidation',
                'type' => 'toggle',
            ),
            'clientvalidation' => array(
                'name' => 'clientvalidation',
                'type' => 'toggle',
            ),
        );
        // сохраняем поля схемы вместе со структурой формы
        $this->createSchemaFields($extraFields, $form['id'], $formFields);
        
        // создаем настройку для служебной схемы
        $schemaConfig = array(
            'name'         => 'DocumentSchemaFieldFormStructureId',
            'title'        => 'Системная настройка: id схемы для внутренней структуры документа',
            'description'  => 'Это системная настройка, не изменяйте ее',
            'type'         => 'text',
            'minvalues'    => 0,
            'maxvalues'    => 0,
            'objecttype'   => 'system',
            'objectid'     => 0,
            'easylistid'   => 0,
            'valuetype'    => 'DocumentSchema',
            'valuefield'   => 'id',
            'valueid'      => $schema['id'],
        );
        $schemaConfig['id'] = $this->createConfig($schemaConfig);
    }
    
    /**
     * Создать форму для схемы (без полей)
     * 
     * @param  array $form - данные для создания модели FlexibleForm-формы
     * @return array - массив с данными формы
     */
    protected function createSchemaForm($form)
    {
        $template = array(
            'title'            => '',
            'description'      => '',
            'action'           => '',
            'ajaxvalidation'   => 1,
            'clientvalidation' => 0,
            'displaytype'      => 'vertical',
            'timecreated'      => time(),
        );
        $form = CMap::mergeArray($template, $form);
        
        $this->insert("{{flexible_forms}}", $form);
        $form['id'] = $this->dbConnection->lastInsertID;
        
        return $form;
    }
    
    /**
     * Создать объект схемы документа (+ доп. данные)
     * 
     * @param  array $schema - данные модели схемы
     * @param  array $extraFields
     * @param  array $formFields
     * @return int id добавленой схемы
     */
    protected function createSchemaObject($schema, $extraFields=array(), $formFields=array())
    {
        // создаем схему из шаблона
        $template = array(
            'type'         => null,
            'title'        => '',
            'description'  => '',
            'formid'       => 0,
            'freebasetype' => null,
            'timecreated'  => time(),
        );
        $schema = CMap::mergeArray($template, $schema);
        // сохраняем запись
        $this->insert("{{document_schemas}}", $schema);
        $schema['id'] = $this->dbConnection->lastInsertID;
        
        if ( $extraFields )
        {// сразу же создаем доп. поля если нужно
            $this->createSchemaFields($extraFields, $template['formid'], $formFields);
        }
        return $schema;
    }
    
    /**
     * Добавить поля в схему документа (+ создать поля формы по данным полей схемы)
     * 
     * @param  array $extraFields - 
     * @param  int   $formId - id формы к которой будут прикреплены созданные поля
     *                         если этот параметр не передан - создания списка полей формы не произойдет
     * @param  array|bool|null $formFields - шаблоны для создания элементов формы: ключом
     *                                       в этом массиве обязательно должно быть название (name)
     *                                       из модели поля схемы (ExtraField)
     * @return array дополненный массив $extraFields, который содержит id вставленных элементов
     */
    protected function createSchemaFields($extraFields, $formId=0, $formFields=array())
    {
        $newExtraFields = array();
        $newFormFields  = array();
        $extraFieldTemplate = array(
            'name'             => '',
            'type'             => 'string',
            'title'            => '',
            'description'      => '',
            'rules'            => null,
            'freebaseproperty' => '',
            'optionslistid'    => 0,
            'parentid'         => 0,
            'formfieldid'      => 0,
            'timecreated'      => time(),
        );
        foreach ( $extraFields as $key => $extraField )
        {
            $extraField = CMap::mergeArray($extraFieldTemplate, $extraField);
            if ( $formId AND isset($formFields[$extraField['name']]) )
            {// поле формы может быть создано по данным модели
                // получаем значения по умолчанию для будущего поля формы из каждого значения поля схемы
                $formFieldData = $formFields[$extraField['name']];
                $formField     = $this->createFormFieldFromExtraField($extraField, $formId, $formFieldData);
                $newFormFields[$key] = $formField;
                // связываем созданное поле формы с полем схемы
                $extraField['formfieldid'] = $formField['id'];
            }
            // сохраняем новое поле схемы
            $this->insert("{{extra_fields}}", $extraField);
            $extraField['id']     = $this->dbConnection->lastInsertID;
            // собираем готовые поля вместе
            $newExtraFields[$key] = $extraField;
        }
        return $newExtraFields;
    }
    
    /**
     * Добавить поля формы в схему документа
     *
     * @param  array $formFields - создать схему для модели по полям модели
     * @return array дополненный массив $formFields, который содержит id вставленных элементов
     */
    protected function createSchemaFormFields($formFields, $formId=0)
    {
        $template = array(
            'objecttype'       => 'FlexibleForm',
            'objectid'         => 0,
            'type'             => 'text',
            'name'             => '',
            'label'            => '',
            'hint'             => '',
            'widget'           => 'TbFormInputElement',
            'ajaxvalidation'   => 1,
            'clientvalidation' => 1,
            'append'           => null,
            'prepend'          => null,
            'hint'             => null,
            'timecreated'      => time(),
        );
        foreach ( $formFields as $formField )
        {
            if ( ! isset($formField['objectid']) OR ! $formField['objectid'] )
            {
                if ( ! $formField['objectid'] = $formId )
                {
                    throw new CException('Form id for schema not set');
                }
            }
            $formField = CMap::mergeArray($template, $formField);
        }
        // сохраняем фрагмент новый формы в базу
        $this->insert("{{flexible_form_fields}}", $formField);
        $formField['id'] = $this->dbConnection->lastInsertID;
        
        return $formField;
    }
    
    /**
     * Создать поле формы по полю схемы
     * 
     * @param  array $extraField - данные модели ExtraField (поле схемы) полным набором полей
     * @param  int   $formId     - id формы к которой прикрепляются созданные поля
     * @param  array $formField  - данные модели FlexibleFormField
     * @param  bool  $save       - сразу же сохранить в базу результат
     * @return array
     */
    protected function createFormFieldFromExtraField($extraField, $formId=0, $formField=array(), $save=true)
    {
        $type = 'text';
        if ( isset($extraField['optionslistid']) AND $extraField['optionslistid'] )
        {
            $type = 'select';
        }
        $formFieldTemplate = array(
            'objecttype'       => 'FlexibleForm',
            'objectid'         => $formId,
            'type'             => $type,
            'name'             => $extraField['name'],
            'label'            => $extraField['title'],
            'hint'             => $extraField['description'],
            'widget'           => 'TbFormInputElement',
            'ajaxvalidation'   => 1,
            'clientvalidation' => 1,
            'append'           => null,
            'prepend'          => null,
            'hint'             => null,
            'timecreated'      => time(),
        );
        $formField = CMap::mergeArray($formFieldTemplate, $formField);
        
        if ( $formId AND $save )
        {// сохраняем поле формы для редактирования одного поля схемы
            $this->insert("{{flexible_form_fields}}", $formField);
            $formField['id'] = $this->dbConnection->lastInsertID;
        }
        return $formField;
    }
}
