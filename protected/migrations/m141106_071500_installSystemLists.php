<?php

class m141106_071500_installSystemLists extends EcMigration
{
    private $_inputTypes = array(
        // standard fields
        'text' => 'textFieldRow',
        'hidden' => 'hiddenField',
        'password' => 'passwordFieldRow',
        'textarea' => 'textAreaRow',
        'file' => 'fileFieldRow',
        'radio' => 'radioButtonRow',
        'checkbox' => 'checkBoxRow',
        'listbox' => 'listBoxRow',
        'dropdownlist' => 'dropDownListRow',
        'checkboxlist' => 'checkBoxListRow',
        'radiolist' => 'radioButtonListRow',
        'url' => 'urlFieldRow',
        'email' => 'emailFieldRow',
        'number' => 'numberFieldRow',
        'range' => 'rangeFieldRow',
        'date' => 'dateFieldRow',
        'time' => 'timeFieldRow',
        'tel' => 'telFieldRow',
        'search' => 'searchFieldRow',
        // extended fields
        'toggle' => 'toggleButtonRow',
        'datepicker' => 'datePickerRow',
        'daterange' => 'dateRangeRow',
        'timepicker' => 'timePickerRow',
        'datetimepicker' => 'dateTimePickerRow',
        'select2' => 'select2Row',
        'redactor' => 'redactorRow',
        'html5editor' => 'html5EditorRow',
        'markdowneditor' => 'markdownEditorRow',
        'ckeditor' => 'ckEditorRow',
        'typeahead' => 'typeAheadRow',
        'maskedtext' => 'maskedTextFieldRow',
        'colorpicker' => 'colorPickerRow',
        //'captcha' => 'captchaRow',
        'pass' => 'passFieldRow'
    );
    
    private $_inputLabels = array(
        // standard fields
        'text' => 'Текст',
        'hidden' => 'Скрытое поле',
        'password' => 'Пароль',
        'textarea' => 'Абзац текста',
        'file' => 'Файл',
        'radio' => 'Переключатель',
        'checkbox' => 'Галочка',
        'listbox' => 'Выбор нескольких значений списка',
        'dropdownlist' => 'Выпадающий список',
        'checkboxlist' => 'Список галочек',
        'radiolist' => 'Список переключателей',
        'url' => 'Ссылка (URL)',
        'email' => 'email',
        'number' => 'Число',
        'range' => 'Диапазон',
        'date' => 'Дата',
        'time' => 'Время',
        'tel' => 'Телефон',
        'search' => 'Поиск',
        // extended fields
        'toggle' => 'Переключатель да/нет',
        'datepicker' => 'Дата (с календарем)',
        'daterange' => 'Диапазон дат (два календаря)',
        'timepicker' => 'Время (bootstrap)',
        'datetimepicker' => 'Дата и время',
        'select2' => 'select2 (усовершенствованый выпадающий список)',
        'redactor' => 'Текстовый редактор',
        'html5editor' => 'Текстовый редактор (на базе html5)',
        'markdowneditor' => 'Текстовый редактор (для markdown-разметки)',
        'ckeditor' => 'ckEditorRow',
        'typeahead' => 'Текст с автодополнением',
        'maskedtext' => 'Ввод с маской (для телефонов, номеров карт и т. д.)',
        'colorpicker' => 'Выбор цвета',
        //'captcha' => 'captchaRow',
        'pass' => 'Ввод пароля (bootstrap)'
    );
    
    private $_configTypes = array(
        'select'      => 'dropdownlist',
        'date'        => 'datepicker',
        'datetime'    => 'datetimepicker',
        'combodate'   => 'datetimepicker',
        'checklist'   => 'checkboxlist',
        'multiselect' => 'listbox',
    );
    
    
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        // добавляем возможность произвольной сортировки полей
        $this->addColumn("{{flexible_form_fields}}", 'sortorder', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
        $this->createIndex('idx_sortorder', "{{flexible_form_fields}}", 'sortorder');
        $this->addColumn("{{flexible_form_fields}}", 'type', "VARCHAR(255) NOT NULL default 'text'");
        $this->createIndex('idx_type', "{{flexible_form_fields}}", 'type');
        
        /////////////////////////////////////////////////////////////////////
        // создаем список стандартных блоков для конструктора форм
        $inputsListItems = array();
        $inputsList = array(
            'name'           => 'Стандартные блоки конструктора форм',
            'description'    => 'Содержит стандартные фрагменты формы - такие как 
                поля ввода тексты или другие встраиваемые в форму виджеты. 
                Используется в конструкторе форм при добавлении новых полей в анкету.',
            'triggerupdate'  => 'manual',
            'triggercleanup' => 'never',
            'unique'         => 1,
        );
        $inputsList['id'] = $this->createList($inputsList);
        // заполняем список блоков формы
        foreach ( $this->_inputTypes as $name => $class )
        {
            $description = $this->_inputLabels[$name];
            $item = array(
                'name'        => $name,
                'value'       => $class,
                'description' => $description,
                'objecttype'  => 'EasyListItem',
                'objectfield' => 'value',
                'easylistid'  => $inputsList['id'],
            );
            $this->createListItem($item);
        }
        // создаем настройку чтобы можно было легко найти этот список
        $inputsListIdConfig = array(
            'name'         => 'formInputsListId',
            'title'        => 'id списка допустимых блоков для конструктора форм',
            'description'  => 'Это служебная настройка, не изменяйте ее',
            'type'         => 'number',
            'minvalues'    => 1,
            'maxvalues'    => 1,
            'objecttype'   => 'system',
            'objectid'     => 0,
            'easylistid'   => 0,
            'valuetype'    => 'EasyList',
            'valuefield'   => 'id',
            'valueid'      => $yesNoList['id'],
        );
        $inputsListIdConfig['id'] = $this->createConfig($inputsListIdConfig);
        // создаем вторую настройку, которая уже содержит список а не id
        $inputsListConfig = array(
            'name'         => 'formInputsList',
            'title'        => 'Список используемых строительных блоков для конструктора форм',
            'description'  => 'Чтобы включить или отключить возможность добавления
                стандартных блоков в конструкторе форм 
                поставьте или снимите галочку напротив названия элемента.
                Эту настройку можно редактировать. ',
            'type'         => 'checkboxlist',
            'minvalues'    => 0,
            'maxvalues'    => 0,
            'objecttype'   => 'system',
            'objectid'     => 0,
            'easylistid'   => $inputsList['id'],
            'valuetype'    => 'EasyList',
            'valuefield'   => 'listItems',
            'valueid'      => $yesNoList['id'],
        );
        $inputsListConfig['id'] = $this->createConfig($inputsListConfig);
        
        /////////////////////////////////////////////////////////////////////
        // отключенные типы проектов
        $excludedTypes = array(
            'name'           => 'Отключить оповещение о проектах выбранного типа',
            'description'    => 'Мы не хотим оповещать вас о проекты которые вам не интересны.
                Вы можете отключить те типы проектов в которых не собираетесь участвовать.
                После этого мы исключим вашу анкету из списка потенциальных участников такого
                проекта. После этого перестанем оповещать вас о наборе на проекты такого типа.',
            'triggerupdate'  => 'manual',
            'triggercleanup' => 'manual',
            'unique'         => 1,
        );
        // изначально список пуст
        $userExcludedTypes = array(
            'name' => 'Выбранные типы проектов для которых отключены оповещения',
            'triggerupdate'  => 'manual',
            'triggercleanup' => 'manual',
            'unique'         => 1,
        );
        // черный список проектов
        $projectsBlackList = array(
            'name'           => 'Черный список проектов',
            'description'    => 'Вы можете выборочно отказаться от участия 
                в проектах которые вам не интересны.
                Пока проект находится в этом списке вы будете исключены из списка его потенциальных
                участников, пригласить вас на такой проект будет нельзя.
                Оповещения с такого проекта присылаться не будут.
                Мероприятия роли и новости этого проекта не будут для вас видны.',
            'triggerupdate'  => 'manual',
            'triggercleanup' => 'manual',
            'unique'         => 1,
        );
        // изначально список пуст
        $userProjectsBlackList = array(
            'name' => 'Проекты в черном списке',
            'triggerupdate'  => 'manual',
            'triggercleanup' => 'manual',
            'unique'         => 1,
        );
        // белый список проектов
        $projectsWhiteList = array(
            'name'           => 'Белый список проектов',
            'description'    => 'Поместите в этот список проекты которые вам интересны.
                Если проект в белом списке - то мы будем оповещать вас о нем даже если
                оповещение об остальных проектах такого типа у вас отключено.<br>
                Пример: отключить приглашения на любые проекты с типом "Реалити-шоу" кроме
                проекта "Топ-модель".',
            'triggerupdate'  => 'manual',
            'triggercleanup' => 'manual',
            'unique'         => 1,
        );
        // изначально список пуст
        $userProjectsWhiteList = array(
            'name' => 'Проекты в белом списке',
            'triggerupdate'  => 'manual',
            'triggercleanup' => 'manual',
            'unique'         => 1,
        );
        // сохраняем три списка
        $excludedTypes['id']      = $this->createList($excludedTypes);
        $projectsBlackList['id']  = $this->createList($projectsBlackList);
        $projectsWhiteList['id']  = $this->createList($projectsWhiteList);
        // сохраняем три пустых списка (это заранее созданные шаблоны для хранения элементов) 
        $userExcludedTypes['id']      = $this->createList($userExcludedTypes);
        $userProjectsBlackList['id']  = $this->createList($userProjectsBlackList);
        $userProjectsWhiteList['id']  = $this->createList($userProjectsWhiteList);
        
        // создаем настройку для каждого списка
        $excludedTypesConfig = array(
            'name'         => 'projectTypesBlackList',
            'title'        => $excludedTypes['name'],
            'description'  => $excludedTypes['description'],
            'type'         => 'checkboxlist',
            'minvalues'    => 0,
            'maxvalues'    => 0,
            'objecttype'   => 'system',
            'objectid'     => 0,
            'easylistid'   => $excludedTypes['id'],
            'valuetype'    => 'EasyList',
            'valuefield'   => 'listItems',
            'valueid'      => $userExcludedTypes['id'],
        );
        $projectsBlackListConfig = array(
            'name'         => 'projectTypesBlackList',
            'title'        => 'Список используемых строительных блоков для конструктора форм',
            'description'  => 'Чтобы включить или отключить возможность добавления
                стандартных блоков в конструкторе форм 
                поставьте или снимите галочку напротив названия элемента.
                Эту настройку можно редактировать. ',
            'type'         => 'checkboxlist',
            'minvalues'    => 0,
            'maxvalues'    => 0,
            'objecttype'   => 'system',
            'objectid'     => 0,
            'easylistid'   => $projectsBlackList['id'],
            'valuetype'    => 'EasyList',
            'valuefield'   => 'listItems',
            'valueid'      => $userProjectsBlackList['id'],
        );
        $projectsWhiteListConfig = array(
            'name'         => 'projectTypesWhiteList',
            'title'        => 'Список используемых строительных блоков для конструктора форм',
            'description'  => 'Чтобы включить или отключить возможность добавления
                стандартных блоков в конструкторе форм 
                поставьте или снимите галочку напротив названия элемента.
                Эту настройку можно редактировать. ',
            'type'         => 'checkboxlist',
            'minvalues'    => 0,
            'maxvalues'    => 0,
            'objecttype'   => 'system',
            'objectid'     => 0,
            'easylistid'   => $projectsWhiteList['id'],
            'valuetype'    => 'EasyList',
            'valuefield'   => 'listItems',
            'valueid'      => $userProjectsWhiteList['id'],
        );
        // сохраняем каждую настройку
        $excludedTypesConfig['id']     = $this->createConfig($excludedTypesConfig);
        $projectsBlackListConfig['id'] = $this->createConfig($projectsBlackListConfig);
        $projectsWhiteListConfig['id'] = $this->createConfig($projectsWhiteListConfig);
        
        
        /////////////////////////////////////////////////////////////////////
        // удаляем старую настройку оповещений
        $condition = "name='preferedProjectTypes'";
        $oldTypesConfigItems = $this->dbConnection->createCommand()->select('id,easylistid,valuetype,valueid')->
            from('{{config}}')->where($condition)->queryAll();
        foreach ( $oldTypesConfigItems as $oldConfigItem )
        {
            $delListCondition  = "";
            $delItemsCondition = "";
            if ( $oldConfigItem['easylistid'] )
            {
                $delListCondition  .= "id={$oldConfigItem['easylistid']} ";
                $delItemsCondition .= "easylistid={$oldConfigItem['easylistid']} ";
            }
            if ( $oldConfigItem['valueid'] AND $oldConfigItem['valuetype'] == 'EasyList' )
            {
                if ( $delListCondition )
                {
                    $delListCondition  .= " OR id={$oldConfigItem['valueid']} ";
                    $delItemsCondition .= " OR easylistid={$oldConfigItem['valueid']} ";
                }else
                {
                    $delListCondition  .= " id={$oldConfigItem['valueid']} ";
                    $delItemsCondition .= " easylistid={$oldConfigItem['valueid']} ";
                }
            }
            if ( $delListCondition )
            {
                $this->delete("{{easy_lists}}", $delListCondition);
                $this->delete("{{easy_list_items}}", $delItemsCondition);
            }
        }
        $this->delete("{{config}}", "name='preferedProjectTypes'");
        
        
        /////////////////////////////////////////////////////////////////////
        // обновление типов настроек
        $allConfigItems = $this->dbConnection->createCommand()->select('id,type')->
            from('{{config}}')->queryAll();
        foreach ( $allConfigItems as $configItem )
        {
            $newConfigType = 'text';
            if ( ! isset($this->_configTypes[$configItem['type']]) )
            {
                continue;
            }
            $newConfigType = $this->_configTypes[$configItem['type']];
            $this->update('{{config}}', array('type' => $newConfigType), 'id='.$configItem['type']);
        }
        
        /////////////////////////////////////////////////////////////////////
        // временный стоп-лист для рассылки
        $emailBlackList = array(
            'name'           => 'Битые, недоступные или тестовые email-адреса',
            'description'    => 'На адреса в этом списке не будут отправляться никакие письма',
            'triggerupdate'  => 'manual',
            'triggercleanup' => 'manual',
            'unique'         => 1,
        );
        $emailBlackList['id']  = $this->createList($emailBlackList);
        $emailBlackListConfig = array(
            'name'         => 'emailBlackList',
            'title'        => 'На адреса в этом списке не будут отправляться никакие письма',
            'type'         => 'checkboxlist',
            'minvalues'    => 0,
            'maxvalues'    => 0,
            'objecttype'   => 'system',
            'objectid'     => 0,
            'easylistid'   => 0,
            'valuetype'    => 'EasyList',
            'valuefield'   => 'listItems',
            'valueid'      => $emailBlackList['id'],
        );
        $emailBlackListConfig['id'] = $this->createConfig($emailBlackListConfig);
    }
}