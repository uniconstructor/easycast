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
        $defaultsTable   = "{{config_defaults}}";
        
        $baseCategories  = $this->dbConnection->createCommand()->select()->
            from($categoriesTable)->where("parentid=1")->queryAll();
        
        // создаем системную настройку для каждой базовой категории
        $configDefaults = array(
            'catalog.sections.rootCategory' => 2,
            'catalog.search.rootCategory' => 3,
            'admin.vacancy.sections.rootCategory' => 4,
            'admin.vacancy.extraFields.rootCategory' => 5,
            'admin.vacancy.registration.templates.rootCatrgory' => 6,
            'questionary.view.templates.rootCatrgory' => 7,
            'questionary.view.tabs.rootCategory' => 8,
            'admin.tags.rootCategory' => 9,
        );
        // Разделы и вкладки каталога
        $this->dbConnection->createCommand()->insert($configTable, array(
            'name'        => 'catalog.sections.rootCategory',
            'title'       => 'Корневая категория для разделов каталога',
            'description' => 'Хранит все разделы каталога доступные в каталоге',
            'minvalues'   => 1,
            'maxvalues'   => 1,
            'type'        => 'text',
            'timecreated' => time(),
        ));
        // Условия и разделы для поиска
        $this->dbConnection->createCommand()->insert($configTable, array(
            'name'        => 'catalog.search.rootCategory',
            'title'       => 'Корневая категория для разделов поиска',
            'description' => '',
            'minvalues'   => 1,
            'maxvalues'   => 1,
            'type'        => 'text',
            'timecreated' => time(),
        ));
        // Группы заявок
        $this->dbConnection->createCommand()->insert($configTable, array(
            'name'        => 'admin.vacancy.sections.rootCategory',
            'title'       => 'Корневая категория для групп по которым распределяются поданые заявки в процессе отбора на роль',
            'description' => '',
            'minvalues'   => 1,
            'maxvalues'   => 1,
            'type'        => 'text',
            'timecreated' => time(),
        ));
        // Наборы дополнительных вопросов для заявки
        $this->dbConnection->createCommand()->insert($configTable, array(
            'name'        => 'admin.vacancy.extraFields.rootCategory',
            'title'       => 'Корневая категория для разделов каталога',
            'description' => '',
            'minvalues'   => 1,
            'maxvalues'   => 1,
            'type'        => 'text',
            'timecreated' => time(),
        ));
        // Шаблоны создания анкет
        $this->dbConnection->createCommand()->insert($configTable, array(
            'name'        => 'admin.vacancy.registration.templates.rootCatrgory',
            'title'       => 'Корневая категория для разделов каталога',
            'description' => '',
            'minvalues'   => 1,
            'maxvalues'   => 1,
            'type'        => 'text',
            'timecreated' => time(),
        ));
        // Шаблоны отображения анкет
        $this->dbConnection->createCommand()->insert($configTable, array(
            'name'        => 'questionary.view.templates.rootCatrgory',
            'title'       => 'Корневая категория для шаблонов отображения анкет',
            'description' => '',
            'minvalues'   => 1,
            'maxvalues'   => 1,
            'type'        => 'text',
            'timecreated' => time(),
        ));
        // Вкладки анкеты
        $this->dbConnection->createCommand()->insert($configTable, array(
            'name'        => 'questionary.view.tabs.rootCategory',
            'title'       => 'Категория для наборов вкладок анкеты',
            'description' => 'Из этой категории берутся все варианты вкладок с информацией анкеты',
            'minvalues'   => 1,
            'maxvalues'   => 1,
            'type'        => 'text',
            'timecreated' => time(),
        ));
        // Категории тегов
        $this->dbConnection->createCommand()->insert($configTable, array(
            'name'        => 'admin.tags.rootCategory',
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
                'type'        => 'text',
                'value'       => $configDefaults[$config['name']],
                'timecreated' => time(),
            ));
        }
    }
}