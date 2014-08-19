<?php

class m140818_213200_linkVacanciesWithWizards extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $wizardsTable             = "{{wizards}}";
        $vacanciesTable           = "{{event_vacancies}}";
        $wizardStepsTable         = "{{wizard_steps}}";
        $wizardStepInstancesTable = "{{wizard_step_instances}}";
        $userFieldInstancesTable  = "{{q_field_instances}}";
        $extraFieldInstancesTable = "{{extra_field_instances}}";
        $extraFieldValuesTable    = "{{extra_field_values}}";
        $listsTable               = "{{easy_lists}}";
        $listInstancesTable       = "{{easy_list_instances}}";
        $listItemsTable           = "{{easy_list_items}}";
        
        
        // Получить все роли, использующие регистрацию по шагам
        $vacancies = $this->dbConnection->createCommand()->select()->
            from($vacanciesTable)->where("regtype='wizard'")->queryAll();
        foreach ( $vacancies as $vacancy )
        {
            // счетчик для количества полей в роли 
            // чтобы правильно расставить их в списке по полю sortorder
            $fieldNum = 0;
            // для каждой роли, заполняемой по шагам добавляем и привязываем wizard
            $wizard = array(
                'name'         => "VARCHAR(255) DEFAULT NULL",
                'timecreated'  => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
                'timemodified' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
                'objecttype'   => "vacancy",
                'objectid'     => $vacancy['id'],
            );
            $this->insert($wizardsTable, $wizard);
            $wizardId = $this->dbConnection->getLastInsertID();
            
            // Получить все экземпляры шагов регистрации для роли
            $stepInstances = $this->dbConnection->createCommand()->select()->
                from($wizardStepInstancesTable)->
                where("objecttype='vacancy' AND objectid=".$wizardId)->queryAll();
            
            // из каждого экземпляра создаем отдельный шаг регистрации
            foreach ( $stepInstances as $stepInstance )
            {
                // получаем полную информацию о шаге
                $stepInfo = $this->dbConnection->createCommand()->select()->
                    from($wizardStepsTable)->where("id=".$stepInstance['wizardstepid'])->queryRow();
                // создаем шаг и привязываем его к роли
                $newStep = array(
                    'name'        => $stepInfo['name'],
                    'header'      => $stepInfo['header'],
                    'description' => $stepInfo['description'],
                    'timecreated' => time(),
                    'prevlabel'   => $stepInfo['prevlabel'],
                    'nextlabel'   => $stepInfo['nextlabel'],
                    'objecttype'  => $stepInstance['objecttype'],
                    'objectid'    => $stepInstance['objectid'],
                );
                $this->insert($wizardStepsTable, $newStep);
                $newStepId = $this->dbConnection->getLastInsertID();
                
                // создаем новый пустой список для полей
                $newList = array(
                    'name'        => 'Список полей раздела '.$stepInfo['name'],
                    'timecreated' => time(),
                );
                $this->insert($listsTable, $newList);
                $newListId = $this->dbConnection->getLastInsertID();
                
                // и привязываем созданный список к шагу регистрации
                $newListInstance = array(
                    'easylistid'  => $newListId,
                    'objecttype'  => 'WizardStep',
                    'objectid'    => $newStepId,
                    'timecreated' => time(),
                );
                $this->insert($listInstancesTable, $newListInstance);
                $newListInstanceId = $this->dbConnection->getLastInsertID();
                
                // перенос данных анкеты: собираем все поля из шага 
                // регистрации и привязываем их обратно к роли вместе с данными

                // переносим в роль поля анкеты из одного шага
                $qFieldInstances = $this->dbConnection->createCommand()->select()->
                    from($userFieldInstancesTable)->
                    where("objecttype='wizardstepinstance' AND objectid=".$stepInstance['id'])->queryAll();
                foreach ( $qFieldInstances as $qFieldInstance )
                {
                    $fieldNum++;
                    // проверим, привязано ли уже это поле к этой роли не в рамках шага регистрации
                    $qfCondition = "objecttype='vacancy' AND fieldid=".$qFieldInstance['fieldid'].
                        " AND objectid=".$vacancy['id'];
                    $existedInstance = $this->dbConnection->createCommand()->select()->
                        from($userFieldInstancesTable)->where($qfCondition)->queryRow();
                    if ( $existedInstance )
                    {// поле уже есть в роли, а к шагу регистрации мы экземпляры полей 
                        // больше не привязываем: удаляем связь поля с шагом формы регистрации
                        $this->delete($userFieldInstancesTable, "id=".$qFieldInstance['id']);
                        
                        // запоминаем id оставшегося экземпляра для списка полей в шаге
                        $qfInstanceId = $existedInstance['id'];
                    }else
                    {// поле анкеты привязано к шагу регистрации но не привязано к самой роли:
                        // бережно переносим его в роль
                        $this->update($userFieldInstancesTable, array(
                            'objecttype' => 'vacancy',
                            'objectid'   => $vacancy['id'],
                        ), 'id='.$qFieldInstance['id']);
                        
                        // запоминаем id перенесенного экземпляра поля для списка полей в шаге формы
                        $qfInstanceId = $qFieldInstance['id'];
                    }
                    
                    // добавляем поле в список полей раздела (easyList)
                    $listItem = array(
                        'easylistid'  => $newListId,
                        'objecttype'  => 'QFieldInstance',
                        'objectid'    => $qfInstanceId,
                        'timecreated' => time(),
                        'sortorder'   => $fieldNum,
                    );
                    $this->insert($listItemsTable, $listItem);
                }
                
                // переносим в роль поля заявки из одного шага
                $extraFieldInstances = $this->dbConnection->createCommand()->select()->
                    from($extraFieldInstancesTable)->
                    where("objecttype='wizardstepinstance' AND objectid=".$stepInstance['id'])->queryAll();
                foreach ( $extraFieldInstances as $extraFieldInstance )
                {
                    $fieldNum++;
                    // проверим, привязано ли уже это поле к этой роли не в рамках шага регистрации
                    $exCondition = "objecttype='vacancy' AND fieldid=".$extraFieldInstance['fieldid'].
                        " AND objectid=".$vacancy['id'];
                    $existedInstance = $this->dbConnection->createCommand()->select()->
                        from($extraFieldInstancesTable)->where($exCondition)->queryRow();
                    if ( $existedInstance )
                    {// доп. поле уже привязано к роли
                        $efValues = $this->dbConnection->createCommand()->select()->
                            from($extraFieldValuesTable)->
                            where("instanceid=".$extraFieldInstance['id'])->queryAll();
                        foreach ( $efValues as $efValue )
                        {// переносим все сохраненные значения в роль, к уже существующем экземпляру поля
                            $this->update($extraFieldValuesTable, array(
                                'instanceid' => $existedInstance,
                            ), 'id='.$efValues['id']);
                        }
                        // после чего старый удаляем
                        $this->delete($extraFieldInstancesTable, "id=".$extraFieldInstance['id']);
                        
                        // запоминаем id оставшегося экземпляра для списка полей в шаге
                        $efInstanceId = $existedInstance['id'];
                    }else
                    {// поле анкеты привязано к шагу регистрации но не привязано к самой роли:
                        // бережно переносим его в роль
                        $this->update($extraFieldInstancesTable, array(
                            'objecttype' => 'vacancy',
                            'objectid'   => $vacancy['id'],
                        ), 'id='.$extraFieldInstance['id']);
                        
                        // запоминаем id перенесенного экземпляра поля для списка полей в шаге формы
                        $efInstanceId  = $qFieldInstance['id'];
                    }
                    
                    // добавляем поле в список полей раздела (easyList)
                    $listItem = array(
                        'easylistid'  => $newListId,
                        'objecttype'  => 'ExtraFieldInstance',
                        'objectid'    => $efInstanceId,
                        'timecreated' => time(),
                        'sortorder'   => $fieldNum,
                    );
                    $this->insert($listItemsTable, $listItem);
                }
            }
        }
        // шаги регистрации во всех ролях распределены: можем удалять мусор
        $this->delete($wizardStepsTable, 'objectid=0');
        
        // удаляем таблицу экземпляров шагов регистрации: она вносила лишнюю сложность
        $this->dropTable($wizardStepInstancesTable);
    }
}