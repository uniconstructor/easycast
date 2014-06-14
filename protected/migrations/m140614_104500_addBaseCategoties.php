<?php

class m140614_104500_addBaseCategoties extends CDbMigration
{
    public function safeUp()
    {
        $table = '{{categories}}';
        $this->alterColumn($table, 'description', 'varchar(4095) DEFAULT NULL');
        $this->alterColumn($table, 'type', "VARCHAR(50) NOT NULL DEFAULT 'system'");
        
        $this->insert($table, array(
            'parentid'    => '0',
            'type'        => 'root',
            'name'        => 'Корневой раздел',
            'description' => 'Служебный, неудаляемый раздел.',
        ));
        $this->insert($table, array(
            'parentid'    => '1',
            'type'        => 'system',
            'name'        => 'Разделы и вкладки каталога',
            'description' => 'Разделы каталога при просмотре анкет на сайте. Должны содержать возраст и статус в критериях.',
        ));
        $this->insert($table, array(
            'parentid'    => '1',
            'type'        => 'system',
            'name'        => 'Условия и разделы для поиска',
            'description' => 'Разделы для поисковых фильтров: они выводятся в выпадающем списке в форме поиска. Могут не содержать статус и возраст в критериях.',
        ));
        $this->insert($table, array(
            'parentid'    => '1',
            'type'        => 'system',
            'name'        => 'Группы заявок',
            'description' => 'Нужны для группировки заявок поданных на роль.',
        ));
        $this->insert($table, array(
            'parentid'    => '1',
            'type'        => 'system',
            'name'        => 'Наборы дополнительных вопросов для заявки',
            'description' => 'Позволяют группировать дополнительные поля, указываемые участником при подаче заявки.',
        ));
        $this->insert($table, array(
            'parentid'    => '1',
            'type'        => 'system',
            'name'        => 'Шаблоны создания анкет',
            'description' => 'Позволяют группировать поля анкеты, обязательные для подачи заявки или при регистрации на роль.',
        ));
        $this->insert($table, array(
            'parentid'    => '1',
            'type'        => 'system',
            'name'        => 'Шаблоны отображения анкет',
            'description' => 'Содержит набор вкладок с информацией анкеты. Позволяют настраивать внешний вид анкеты в заявке в зависимости от роли.',
        ));
        $this->insert($table, array(
            'parentid'    => '1',
            'type'        => 'system',
            'name'        => 'Вкладки анкеты',
            'description' => 'Каждая вкладка содержит в себе набор отображаемых полей в нужном порядке.',
        ));
        $this->insert($table, array(
            'parentid'    => '1',
            'type'        => 'system',
            'name'        => 'Категории тегов',
            'description' => 'Тегами можно (будет) помечать практически любые в системе, таким образом группируя их по любому признаку.',
        ));
        
        $this->insert($table, array(
            'parentid'    => '4',
            'type'        => 'sections',
            'name'        => 'Образы топ-модели',
            'description' => 'Нужны для группировки заявок проекта "Топ-модель".',
        ));
        $this->insert($table, array(
            'parentid'    => '5',
            'type'        => 'extrafields',
            'name'        => 'Вопросы для топ-модели от телеканала Ю',
            'description' => 'Хранит список дополнительных вопросов анкеты проекта "Топ-модель".',
        ));
        $this->insert($table, array(
            'parentid'    => '6',
            'type'        => 'userfields',
            'name'        => 'Форма анкеты топ-модели',
            'description' => 'Поля из нашей формы анкеты, заполняемые при подачи заявки на проект "Топ-модель".',
        ));
        $this->insert($table, array(
            'parentid'    => '7',
            'type'        => 'usertabs',
            'name'        => 'По умолчанию (вся информация)',
            'description' => 'Вся информация по анкете из нашей анкеты, разбитая по вкладкам',
        ));
        $this->insert($table, array(
            'parentid'    => '7',
            'type'        => 'usertabs',
            'name'        => 'Заявка топ-модели',
            'description' => 'Внешний вид анкеты заявки топ-модели',
        ));
        $this->insert($table, array(
            'parentid'    => '8',
            'type'        => 'userfields',
            'name'        => 'Основное',
            'description' => '',
        ));
        $this->insert($table, array(
            'parentid'    => '8',
            'type'        => 'userfields',
            'name'        => 'Умения и навыки',
            'description' => '',
        ));
        $this->insert($table, array(
            'parentid'    => '8',
            'type'        => 'userfields',
            'name'        => 'Фильмография',
            'description' => '',
        ));
        $this->insert($table, array(
            'parentid'    => '8',
            'type'        => 'userfields',
            'name'        => 'Награды',
            'description' => '',
        ));
        $this->insert($table, array(
            'parentid'    => '8',
            'type'        => 'userfields',
            'name'        => 'Контакты',
            'description' => '',
        ));
        $this->insert($table, array(
            'parentid'    => '8',
            'type'        => 'userfields',
            'name'        => 'Условия',
            'description' => '',
        ));
        $this->insert($table, array(
            'parentid'    => '9',
            'type'        => 'tags',
            'name'        => 'Маркеры для отбора заявок',
            'description' => 'Позволяют распределить поданые заявки по качеству: лучшние/средние/худшие',
        ));
        
        $table = '{{video}}';
        $this->addColumn($table, 'visible', 'tinyint(1) NOT NULL DEFAULT 1');
        $this->createIndex('idx_visible', $table, 'visible');
    }
}