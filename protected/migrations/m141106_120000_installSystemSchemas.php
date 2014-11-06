<?php

class m141106_120000_installSystemSchemas extends EcMigration
{
    /**
     * @var array
     */
    private $defaultSchemaActions = array(
        ''
    );
    private $defaultSchemaActions = '/admin/admin/create';
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        /////////////////////////////////////////////////////////////////////
        // создание схемы: редактирование одного поля формы для поля схемы
        
        
    }
    
    public function createSystemSchema()
    {
        // создаем форму
        $form = array(
            'title'            => 'Схема документа: параметры формы',
            'description'      => '',
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
        $schema = $this->createSchemaObject($schema);
        
        // добавляем поля для новой схемы
        $extraFields = array(
            'title' => array(
                'name'  => 'title',
                'type'  => 'text',
                'title' => 'Заголовок формы',
            ),
            'description' => array(
                'name'  => 'description',
                'type'  => 'html5editor',
                'title' => 'Описание формы',
            ),
            'action' => array(
                'name'  => 'action',
                'type'  => 'text',
                'title' => 'Адрес для обработки данных формы',
            ),
            'ajaxvalidation' => array(
                'name'  => 'ajaxvalidation',
                'type'  => 'BOOL',
                'title' => 'Включить AJAX-проверку формы',
            ),
            'clientvalidation' => array(
                'name'  => 'clientvalidation',
                'type'  => 'BOOL',
                'title' => 'Заголовок формы',
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
        $this->createSchemaFields($extraFields, $form['id'], $formFields);
        
        
        
        // сохраняем схему
        
        // создаем настройку для служебной схемы
        
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
        
        if ( ! $extraFields )
        {
            return $schema;
        }
        return $this->createSchemaFields($extraFields, $template['formid'], $formFields);
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
            'type'             => 'text',
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
                $formField = $this->createFormFieldFromExtraField($formFields[$extraField['name']], $formId);
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
            'description'      => '',
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
        $this->insert("{{flexible_form_items}}", $formField);
        $formField['id'] = $this->dbConnection->lastInsertID;
        
        return $formField;
    }
    
    /**
     * Создать поле формы по полю схемы
     * 
     * @param  array $extraField - данные модели ExtraField (поле схемы) полным набором полей
     * @param  bool  $save - сразу же сохранить в базу результат
     * @return array
     */
    protected function createFormFieldFromExtraField($extraField, $formId=0, $save=true)
    {
        $type = 'text';
        if ( $extraField['optionslistid'] )
        {
            $type = 'select';
        }
        $formField = array(
            'objecttype'       => 'FlexibleForm',
            'objectid'         => $formId,
            'type'             => 'text',
            'name'             => $extraField['name'],
            'label'            => $extraField['title'],
            'description'      => $extraField['description'],
            'widget'           => 'TbFormInputElement',
            'ajaxvalidation'   => 1,
            'clientvalidation' => 1,
            'append'           => null,
            'prepend'          => null,
            'hint'             => null,
            'timecreated'      => time(),
        );
        if ( $formId AND $save )
        {// сохраняем поле формы для редактирования одного поля схемы
            $this->insert("{{flexible_form_items}}", $formField);
            $formField['id'] = $this->dbConnection->lastInsertID;
        }
        return $formField;
    }
}