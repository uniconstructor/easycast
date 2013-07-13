<?php
/**
 * Устанавливает таблицу, в которой хранятся все пункты главного меню
 * Устанавливает стандартные пункты для заказчика и пользователя
 * 
 * Пункты главного меню хранятся в базе, для того чтобы туда можно было добавлять собственные пункты
 */
class m130111_020500_installMainMenu extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
    protected $tableName = '{{menu_items}}';

    public function safeUp()
    {
        $fields = array(
            "id" => "pk",
            "type" => "varchar(20) NOT NULL",
            "label" => "varchar(127) NOT NULL",
            "relpath" => "varchar(255) DEFAULT NULL",
            "fullpath" => "varchar(255) DEFAULT NULL",
            "custom" => "tinyint(1) DEFAULT 1 NOT NULL",
            "newwindow" => "tinyint(1) DEFAULT 1 NOT NULL",
            "order" => "int(11) NOT NULL DEFAULT 0",
            "pictureid" => "int(11) UNSIGNED  DEFAULT NULL",
            "visible" => "tinyint(1) DEFAULT 1 NOT NULL",
            );

        $this->createTable($this->tableName, $fields, $this->MySqlOptions);

        $fieldNames = array_keys($fields);
        unset($fieldNames[0]);

        foreach ( $fieldNames as $field )
        {
            $this->createIndex('idx_'.$field, $this->tableName, $field);
        }
        
        // Устанавливаем стандартные пункты меню
        $items = array();
        // Меню заказчика
        // Срочный заказ
        $items[] = array(
            'type'      => 'customer',
            'label'     => 'fast_order',
            'relpath'   => '#',
            'custom'    => '0',
            'newwindow' => '0',
            'order'     => '0',
            'visible'   => '1'
        );
        // База актеров
        $items[] = array(
            'type'      => 'customer',
            'label'     => 'catalog',
            'relpath'   => 'catalog',
            'custom'    => '0',
            'newwindow' => '0',
            'order'     => '1',
            'visible'   => '1'
        );
        // поиск
        $items[] = array(
            'type'      => 'customer',
            'label'     => 'search',
            'relpath'   => 'catalog/search',
            'custom'    => '0',
            'newwindow' => '0',
            'order'     => '2',
            'visible'   => '1'
        );
        // База специалистов
        $items[] = array(
            'type'      => 'customer',
            'label'     => 'specialist_base',
            'relpath'   => 'specialistBase',
            'custom'    => '0',
            'newwindow' => '0',
            'order'     => '3',
            'visible'   => '1'
        );
        // Мой выбор
        $items[] = array(
            'type'      => 'customer',
            'label'     => 'my_choice',
            'relpath'   => 'catalog/myChoice',
            'custom'    => '0',
            'newwindow' => '0',
            'order'     => '4',
            'visible'   => '1'
        );
        // online кастинг
        $items[] = array(
            'type'      => 'customer',
            'label'     => 'online_casting',
            'relpath'   => 'onlineCasting',
            'custom'    => '0',
            'newwindow' => '0',
            'order'     => '5',
            'visible'   => '1'
        );
        // галерея
        $items[] = array(
            'type'      => 'customer',
            'label'     => 'gallery',
            'relpath'   => 'gallery',
            'custom'    => '0',
            'newwindow' => '0',
            'order'     => '6',
            'visible'   => '1'
        );
        // проекты
        $items[] = array(
            'type'      => 'customer',
            'label'     => 'projects',
            'relpath'   => 'projects',
            'custom'    => '0',
            'newwindow' => '0',
            'order'     => '7',
            'visible'   => '1'
        );
        // Команда
        $items[] = array(
            'type'      => 'customer',
            'label'     => 'team',
            'relpath'   => 'team',
            'custom'    => '0',
            'newwindow' => '0',
            'order'     => '8',
            'visible'   => '1'
        );
        // Наши услуги
        $items[] = array(
            'type'      => 'customer',
            'label'     => 'services',
            'relpath'   => 'site/page/view/services',
            'custom'    => '0',
            'newwindow' => '0',
            'order'     => '9',
            'visible'   => '1'
        );
        
        // Меню участника
        // расписание съемок
        $items[] = array(
            'type'      => 'user',
            'label'     => 'action_timetable',
            'relpath'   => 'calendar',
            'custom'    => '0',
            'newwindow' => '0',
            'order'     => '0',
            'visible'   => '1'
        );
        // проекты
        $items[] = array(
            'type'      => 'user',
            'label'     => 'projects',
            'relpath'   => 'projects',
            'custom'    => '0',
            'newwindow' => '0',
            'order'     => '1',
            'visible'   => '1'
        );
        // галерея
        $items[] = array(
            'type'      => 'user',
            'label'     => 'gallery',
            'relpath'   => 'gallery',
            'custom'    => '0',
            'newwindow' => '0',
            'order'     => '2',
            'visible'   => '1'
        );
        // новости
        $items[] = array(
            'type'      => 'user',
            'label'     => 'news',
            'relpath'   => 'news',
            'custom'    => '0',
            'newwindow' => '0',
            'order'     => '3',
            'visible'   => '1'
        );
        // форум
        $items[] = array(
            'type'      => 'user',
            'label'     => 'forum',
            'relpath'   => 'forum',
            'custom'    => '0',
            'newwindow' => '0',
            'order'     => '4',
            'visible'   => '1'
        );

        foreach ($items as $item)
        {
            $this->insert($this->tableName, $item);
        }
    }
}
