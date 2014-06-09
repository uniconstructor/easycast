<?php

class m140531_200600_addTopModelFields extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        // добавляем поля, которых нет в анкете и которые прикрепляются к заявке на участие
        // они понадобятся один раз
        $table  = "{{extra_fields}}";
        $fields = array(
            array(
                'name'        => 'tm_projects',
                'type'        => 'textarea',
                'label'       => 'Перечислите проекты, в которых вам довелось принимать участие',
                'description' => '',
            ),
            array(
                'name'        => 'tm_about',
                'type'        => 'textarea',
                'label'       => 'Расскажите несколько слов о себе',
                'description' => '',
            ),
            array(
                'name'        => 'tm_address',
                'type'        => 'textarea',
                'label'       => 'Домашний адрес',
                'description' => '',
            ),
            array(
                'name'        => 'tm_birdhplace',
                'type'        => 'text',
                'label'       => 'Место рождения',
                'description' => '',
            ),
            array(
                'name'        => 'tm_diseases',
                'type'        => 'textarea',
                'label'       => 'Есть ли у вас хронические заболевания или расстройства? (перечислить)',
                'description' => '',
            ),
            array(
                'name'        => 'tm_marrige',
                'type'        => 'text',
                'label'       => 'Семейное положение',
                'description' => '',
            ),
            array(
                'name'        => 'tm_children',
                'type'        => 'textarea',
                'label'       => 'Есть ли у вас дети?',
                'description' => 'В случае положительного ответа, укажите имя, возраст, с кем живет ваш ребенок, и на кого вы планируете оставить его на время участия в проекте?',
            ),
            array(
                'name'        => 'tm_relationships',
                'type'        => 'text',
                'label'       => 'Есть ли у вас молодой человек?',
                'description' => '',
            ),
            array(
                'name'        => 'tm_workplace',
                'type'        => 'textarea',
                'label'       => 'Место работы',
                'description' => 'Если в настоящий момент вы не работаете, опишите свой прошлый опыт работы, если таковой был',
            ),
            array(
                'name'        => 'tm_modelhistory',
                'type'        => 'textarea',
                'label'       => 'Был ли у вас опыт съемок в конкурсах красоты или прочих конкурсах, связанных с модельным бизнесом?',
                'description' => 'В случае положительного ответа, укажите названия конкурсов. Если такого опыта не было впишите "нет".',
            ),
            array(
                'name'        => 'tm_parentwork',
                'type'        => 'textarea',
                'label'       => 'Кем работают ваши родители?',
                'description' => '',
            ),
            array(
                'name'        => 'tm_parentrelationships',
                'type'        => 'textarea',
                'label'       => 'Кратко опишите свои отношения с родителями',
                'description' => '',
            ),
            array(
                'name'        => 'tm_whyme',
                'type'        => 'textarea',
                'label'       => 'Чем, по вашему мнению, вы можете быть интересны проекту?',
                'description' => '',
            ),
            array(
                'name'        => 'tm_top5',
                'type'        => 'textarea',
                'label'       => 'Опишите 5 своих самых главных качеств',
                'description' => '',
            ),
            array(
                'name'        => 'tm_madact',
                'type'        => 'textarea',
                'label'       => 'Какой, по вашему мнению, самый безумный поступок в жизни вы совершали?',
                'description' => 'Опишите поступок из своей жизни, которым гордитесь и поступок, которого стыдитесь',
            ),
            array(
                'name'        => 'tm_hobby',
                'type'        => 'textarea',
                'label'       => 'Есть ли в вашей жизни какое-либо пристрастие/хобби/увлечение?',
                'description' => '',
            ),
            array(
                'name'        => 'tm_emotions',
                'type'        => 'textarea',
                'label'       => 'Что может вызвать у вас наибольшую радость? Что может причинить боль и страдания?',
                'description' => '',
            ),
            array(
                'name'        => 'tm_fears',
                'type'        => 'textarea',
                'label'       => 'Есть ли у вас какие-либо фобии или страхи? Опишите их.',
                'description' => '',
            ),
            array(
                'name'        => 'tm_victoryinfo',
                'type'        => 'textarea',
                'label'       => 'Что для вас означает победа в шоу «Топ-модель по-русски»?',
                'description' => '',
            ),
            array(
                'name'        => 'tm_purpose',
                'type'        => 'textarea',
                'label'       => 'Ради чего/кого вы готовы бороться за победу в шоу?',
                'description' => '',
            ),
            array(
                'name'        => 'tm_money',
                'type'        => 'textarea',
                'label'       => 'В случае получения денежного приза на что вы потратите деньги?',
                'description' => '',
            ),
            array(
                'name'        => 'tm_victoryact',
                'type'        => 'textarea',
                'label'       => 'На что вы готовы пойти ради победы в шоу?',
                'description' => '',
            ),
            array(
                'name'        => 'tm_in_a_mirror',
                'type'        => 'textarea',
                'label'       => 'Опишите каким человеком вы видите себя через 10 лет?',
                'description' => 'Внешние, внутренние качества, социальный статус',
            ),
            array(
                'name'        => 'tm_celebrities',
                'type'        => 'textarea',
                'label'       => 'Чье мнение из известных людей (представителей мира моды или шоу-бизнеса) является для вас авторитетом?',
                'description' => '',
            ),
            array(
                'name'        => 'tm_hero',
                'type'        => 'textarea',
                'label'       => 'Есть ли в вашей жизни кумир, на которого вы хотели бы равняться?',
                'description' => 'С кем из известных личностей вы мечтали бы познакомиться?',
            ),
            array(
                'name'        => 'tm_wantedchanges',
                'type'        => 'textarea',
                'label'       => 'Что в себе вы хотели бы поменять?',
                'description' => 'Внешние черты и внутренние качества',
            ),
            array(
                'name'        => 'tm_radicalchanges',
                'type'        => 'textarea',
                'label'       => 'Готовы ли вы на кардинальное изменение внешности в процессе съемок проекта?',
                'description' => '(Стрижка, покраска)',
            ),
            array(
                'name'        => 'tm_naked',
                'type'        => 'text',
                'label'       => 'Готовы ли вы сниматься обнаженной, если этого потребует проект/заказчик?',
                'description' => '',
            ),
            array(
                'name'        => 'tm_friends',
                'type'        => 'textarea',
                'label'       => 'Как ваши родители/друзья отреагировали на то, что вы решили принять участие в проекте «Топ-модель по-русски»?',
                'description' => '',
            ),
            array(
                'name'        => 'tm_education',
                'type'        => 'textarea',
                'label'       => 'Образование',
                'description' => '',
            ),
            array(
                'name'        => 'tm_speciality',
                'type'        => 'textarea',
                'label'       => 'Профессия',
                'description' => '',
            ),
        );
        foreach ( $fields as $field )
        {
            $this->insert($table, $field);
        }
        unset($fields);
    }
}