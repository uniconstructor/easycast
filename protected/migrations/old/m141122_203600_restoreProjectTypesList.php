<?php

class m141122_203600_restoreProjectTypesList extends EcMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        // создаем список для типов проектов
        $this->insert("{{easy_lists}}", array(
            'id'            => 1,
            'name'          => 'Все типы проекта',
            'description'   => 'Все проекты на сайте используют этот список для своего типа',
            'triggerupdate' => 'manual',
            'unique'        => 1,
        ));
        // запоминаем id списка с типами - он дальше везде понадобится
        $listId = $this->dbConnection->lastInsertID;
        
        // добавляем в список все существующие типы проектов
        $sortOrder    = 1;
        $projectTypes = array(
            array(
                'easylistid'  => $listId,
                'name'        => 'Фотореклама',
                'value'       => 'ad',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Видеореклама',
                'value'       => 'videoad',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Художественный фильм',
                'value'       => 'film',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Документальный фильм',
                'value'       => 'documentary',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Сериал',
                'value'       => 'series',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Телепроект',
                'value'       => 'tvshow',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Показ',
                'value'       => 'expo',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Промо-акция',
                'value'       => 'promo',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Флешмоб',
                'value'       => 'flashmob',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Видеоклип',
                'value'       => 'video',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Видеоролик',
                'value'       => 'videoclip',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Реалити-шоу',
                'value'       => 'realityshow',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Докуреалити',
                'value'       => 'docureality',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Короткометражный фильм',
                'value'       => 'shortfilm',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Конференция',
                'value'       => 'conference',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Концерт',
                'value'       => 'concert',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Театральная постановка',
                'value'       => 'theatreperfomance',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Мюзикл',
                'value'       => 'musical',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Корпоратив',
                'value'       => 'corporate',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Фестиваль',
                'value'       => 'festival',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Онлайн-кастинг',
                'value'       => 'onlinecasting',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
        );
        // запоминаем какие id каким типам проекта соответствуют
        $itemIds = array();
        foreach ( $projectTypes as $projectType )
        {
            $this->insert("{{easy_list_items}}", $projectType);
            $itemIds[$projectType['value']] = $this->dbConnection->lastInsertID;
        }
        unset($sortOrder);
        
        // добавляем поле id типа проекта чтобы список типов проекта стал настраиваемым
        $this->addColumn('{{projects}}', 'typeid', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
        $this->createIndex('idx_typeid', '{{projects}}', 'typeid');
        $this->refreshTableSchema('{{projects}}');
        
        //////////////////////////////////////////
        // изменяем хранение поля "тип" для всех проектов
        $pCondition = "(name = 'type') AND (objecttype = 'Project') AND (objectid = 0)";
        // делаем id списка проектов системной настройкой
        $newColumns = array(
            'name'       => 'projectTypesListId',
            'objecttype' => 'system',
            'objectid'   => 0,
            'easylistid' => $listId,
            'valuetype'  => 'EasyList',
            'valuefield' => 'id',
            'valueid'    => $listId,
        );
        $this->update('{{config}}', $newColumns, $pCondition);
        
        // удаляем старые настройки "тип проекта"
        $deleteCondition = "(name = 'type') AND (objecttype = 'Project')";
        $this->dbConnection->createCommand()->delete('{{config}}', $deleteCondition);
        
        // получаем все ранее созданные проекты
        $projects = $this->dbConnection->createCommand()->select('id,type')->
            from('{{projects}}')->queryAll();
        // переносим данные о типе каждого проекта
        foreach ( $projects as $project )
        {
            // определяем id элемента списка который хранит нужный тип проекта
            $typeId = $itemIds[$project['type']];
            $this->update('{{projects}}', array('typeid' => $typeId), 'id='.$project['id']);
        }
        
        ///////////////////////////////////////////////////////////////////
        // настройки для фильтрации приглашений привязываем к модели анкеты
        $tblCondition = "(name = 'projectTypesBlackList') AND (objecttype = 'system')";
        $this->update('{{config}}', array(
            'objecttype' => 'Questionary',
            'type'       => 'listbox',
            'title'      => 'Участие проектах (по типу проекта)',
        ), $tblCondition);
        $pblCondition = "(name = 'projectsBlackList') AND (objecttype = 'system')";
        $this->update('{{config}}', array(
            'objecttype' => 'Questionary',
            'type'       => 'listbox',
        ), $pblCondition);
        $pwlCondition = "(name = 'projectsWhiteList') AND (objecttype = 'system')";
        $this->update('{{config}}', array(
            'objecttype' => 'Questionary',
            'type'       => 'listbox',
        ), $pwlCondition);
        
        /////////////////////////////////////////////////////////////
        // данные перенесены, теперь можно удалить поле "тип проекта"
        $this->dropColumn('{{projects}}', 'type');
        
        // добавляем в таблицу настроек поле для связи настройки со схемой документа,
        // чтобы можно было указывать собственную структуру формы для каждой настройки
        $this->addColumn('{{config}}', 'schemaid', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
        $this->createIndex('idx_schemaid', '{{config}}', 'schemaid');
        // разрешить/запретить пользователям вводить собственные значения настройки помимо стандартных
        $this->addColumn('{{config}}', 'allowuservalues', 'tinyint(1) UNSIGNED NOT NULL DEFAULT 0');
        $this->createIndex('idx_allowuservalues', '{{config}}', 'allowuservalues');
        $this->refreshTableSchema('{{config}}');
        // разрешаем свои значения тем настройкам в которы уже предусмотрен список для них
        $this->update('{{config}}', array('allowuservalues' => '1'), 'userlistid > 0');
    }
}