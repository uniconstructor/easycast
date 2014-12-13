<?php

class m141124_231500_addEasyListItemParentId extends EcMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        // в связи с новой политикой хранения настроек
        // удаляем все настройки анкеты кроме корневых
        $this->delete('{{config}}', "objecttype='Questionary' AND objectid > 0");
        
        // добавляем более удобный способ ссылаться на оригинал элемента списка при копировании списков
        $this->addColumn('{{easy_list_items}}', 'parentid', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_parentid', '{{easy_list_items}}', 'parentid');
        $this->refreshTableSchema('{{easy_list_items}}');
        
        // проставляем ссылки на элементы списка используя новые поля 
        $items = $this->dbConnection->createCommand()->select('*')->
            from('{{easy_list_items}}')->where("objecttype='EasyListItem'")->queryAll();
        foreach ( $items as $item )
        {
            $columns = array(
                'objecttype'  => '__item__',
                'objectid'    => 0,
                'objectfield' => null,
            );
            if ( $item['objectid'] != $item['id'] )
            {
                $columns['parentid'] = $item['objectid'];
            }
            $this->update('{{easy_list_items}}', $columns, "id=".$item['id']);
            unset($columns);
        }
        
        $condition1   = "easylistid = valueid AND valuetype='EasyList' AND easylistid > 0";
        $configItems1 = $this->dbConnection->createCommand()->select('*')->
            from('{{config}}')->where($condition1)->queryAll();
        foreach ( $configItems1 as $config )
        {
            if ( stristr($config['name'], 'List') )
            {
                $this->update('{{config}}', array('easylistid' => 0), "id=".$config['id']);
                continue;
            }
            $oldSelectedList = $this->dbConnection->createCommand()->select('*')->
                from('{{easy_lists}}')->where('id='.$config['easylistid'])->queryRow();
            $this->insert("{{easy_lists}}", array(
                'name'          => $oldSelectedList['name'],
                'description'   => $oldSelectedList['description'],
                'triggerupdate' => 'manual',
                'triggerdelete' => 'manual',
                'unique'        => 1,
            ));
            // запоминаем id нового списка для значений
            $selectedListId = $this->dbConnection->lastInsertID;
            // копируем все элементы из старого списка в новый
            $this->copyListItems($config['easylistid'], $selectedListId);
        }
    }
}