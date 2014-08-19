<?php

class m140805_111300_installSystemConfig extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $categoriesTable = "{{categories}}";
        $configTable     = "{{config}}";
        $instanceTable   = "{{config_instances}}";
        $defaultsTable   = "{{config_values}}";
        
        $baseCategories  = $this->dbConnection->createCommand()->select()->
            from($categoriesTable)->where("parentid=1")->queryAll();
        
        // создаем системную настройку для каждой базовой категории
        $configDefaults = array(
            'catalog.sections.category'     => 2,
            'search.sections.category'      => 3,
            'vacancy.sections.category'     => 4,
            'extrafield.sections.category'  => 5,
            'registration.wizards.category' => 6,
            'questionary.views.category'    => 7,
            'questionary.tabs.category'     => 8,
            'tags.category'                 => 9,
        );
        
        // Разделы и вкладки каталога
        $this->dbConnection->createCommand()->insert($configTable, array(
            'name'        => 'catalog.sections.category',
            'title'       => 'Основная категория для разделов каталога',
            'description' => 'Хранит все разделы каталога доступные в каталоге',
            'minvalues'   => 1,
            'maxvalues'   => 1,
            'type'        => 'text',
            'timecreated' => time(),
        ));
        // Условия и разделы для поиска
        $this->dbConnection->createCommand()->insert($configTable, array(
            'name'        => 'search.sections.category',
            'title'       => 'Корневая категория для разделов поиска',
            'description' => '',
            'minvalues'   => 1,
            'maxvalues'   => 1,
            'type'        => 'text',
            'timecreated' => time(),
        ));
        // Группы заявок
        $this->dbConnection->createCommand()->insert($configTable, array(
            'name'        => 'vacancy.sections.category',
            'title'       => 'Корневая категория для групп по которым распределяются поданые заявки в процессе отбора на роль',
            'description' => '',
            'minvalues'   => 1,
            'maxvalues'   => 1,
            'type'        => 'text',
            'timecreated' => time(),
        ));
        // Наборы дополнительных вопросов для заявки
        $this->dbConnection->createCommand()->insert($configTable, array(
            'name'        => 'extrafield.sections.category',
            'title'       => 'Категория для списков доп. вопросов ролей',
            'description' => '',
            'minvalues'   => 1,
            'maxvalues'   => 1,
            'type'        => 'text',
            'timecreated' => time(),
        ));
        // Шаблоны создания анкет
        $this->dbConnection->createCommand()->insert($configTable, array(
            'name'        => 'registration.wizards.category',
            'title'       => 'Категория для шаблонов создания анкет',
            'description' => '',
            'minvalues'   => 1,
            'maxvalues'   => 1,
            'type'        => 'text',
            'timecreated' => time(),
        ));
        // Шаблоны отображения анкет
        $this->dbConnection->createCommand()->insert($configTable, array(
            'name'        => 'questionary.views.category',
            'title'       => 'Корневая категория для шаблонов отображения анкет',
            'description' => '',
            'minvalues'   => 1,
            'maxvalues'   => 1,
            'type'        => 'text',
            'timecreated' => time(),
        ));
        // Вкладки анкеты
        $this->dbConnection->createCommand()->insert($configTable, array(
            'name'        => 'questionary.tabs.category',
            'title'       => 'Категория для наборов вкладок анкеты',
            'description' => 'Из этой категории берутся все варианты вкладок с информацией анкеты',
            'minvalues'   => 1,
            'maxvalues'   => 1,
            'type'        => 'text',
            'timecreated' => time(),
        ));
        // Категории тегов
        $this->dbConnection->createCommand()->insert($configTable, array(
            'name'        => 'tags.category',
            'title'       => 'Корневая категория в которой хранятся все списки тегов на сайте',
            'description' => 'Планируется что тегами можно будет помечать любые объекты в системе, а не только участников',
            'minvalues'   => 1,
            'maxvalues'   => 1,
            'type'        => 'text',
            'timecreated' => time(),
        ));
        
        // назначаем всем настройкам уровень "системный"
        $systemConfig  = $this->dbConnection->createCommand()->select()->from($configTable)->queryAll();
        
        foreach ( $systemConfig as $config )
        {
            $this->dbConnection->createCommand()->insert($instanceTable, array(
                'configid'    => $config['id'],
                'objecttype'  => 'system',
                'objectid'    => 0,
                'timecreated' => time(),
            ));
            // назначаем значения по умолчанию
            $this->dbConnection->createCommand()->insert($defaultsTable, array(
                'objecttype'  => 'config',
                'objectid'    => $config['id'],
                'type'        => 'string',
                'value'       => $configDefaults[$config['name']],
                'timecreated' => time(),
                'default'     => 1,
            ));
        }
        
        // Дополнительные настройки для поиска в каталоге и его разделах:
        
        // настройка: скрыть/показать раздел каталога на сайте
        $this->dbConnection->createCommand()->insert($configTable, array(
            'name'        => 'catalog.section.visible',
            'title'       => 'Отображать ли раздел каталога на сайте?',
            'description' => '',
            'minvalues'   => 0,
            'maxvalues'   => 1,
            'type'        => 'checkbox',
            'timecreated' => time(),
        ));
        // по умолчанию: показать
        $showSectionConfig = $this->dbConnection->createCommand()->select()->from($configTable)->
            where("name='catalog.section.visible'")->queryRow();
        $this->dbConnection->createCommand()->insert($defaultsTable, array(
            'objecttype'  => 'config',
            'objectid'    => $showSectionConfig['id'],
            'type'        => 'string',
            'value'       => 1,
            'timecreated' => time(),
            'default'     => 1,
        ));
        
        // настройка: включить/отключить поиск по разделу на сайте
        $this->dbConnection->createCommand()->insert($configTable, array(
            'name'        => 'search.section.enabed',
            'title'       => 'Разрешить ли обычным пользователям искать по этому списку?',
            'description' => '',
            'minvalues'   => 0,
            'maxvalues'   => 1,
            'type'        => 'checkbox',
            'timecreated' => time(),
        ));
        // по умолчанию: включить
        $enableSectionConfig = $this->dbConnection->createCommand()->select()->from($configTable)->
            where("name='search.section.enabed'")->queryRow();
        $this->dbConnection->createCommand()->insert($defaultsTable, array(
            'objecttype'  => 'easylist',
            'objectid'    => $enableSectionConfig['id'],
            'type'        => 'string',
            'value'       => 1,
            'timecreated' => time(),
            'default'     => 1,
        ));
    }
}