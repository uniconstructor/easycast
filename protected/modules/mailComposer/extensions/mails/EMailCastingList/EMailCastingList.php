<?php

// подключаем базовый класс: фотовызывной
Yii::import('application.modules.mailComposer.extensions.mails.EMailCallList.EMailCallList');

/**
 * Письмо "кастинг-лист"
 * Некоторый аналог фотовызывного, но в этом документе выводится вся информация об актерах
 * и каждый актер располагается на отдельной странице при печати
 * Используется при проведении кастинга
 * 
 * От фотовызывного отличается только более подробной информацией о каждом актере
 * @todo улучшить перевод на английский когда будет реализован нормальный i18n для стандартных значений
 *       анкеты, списокв ВУЗов и других данных анкеты, требующих перевода
 */
class EMailCastingList extends EMailCallList
{
    /**
     * Добавить в письмо блок с описанием роли и всех утвержденных на нее актеров
     * @param EventVacancy $vacancy
     * @return void
     */
    protected function addVacancy($vacancy)
    {
        // добавляем информацию о роли
        $vacancyInfo = array();
        $vacancyInfo['type']   = 'subHeader';
        $vacancyInfo['header'] = ProjectsModule::t('role').': ';
        if ( $this->language != 'ru' AND isset($vacancy['translation']) )
        {
            $vacancyInfo['header'] .= $vacancy['translation'];
        }else
        {
            $vacancyInfo['header'] .= $vacancy['name'];
        }
        $vacancyInfo['headerInfo']     = $this->getVacancyTimePeriod($vacancy);
        $vacancyInfo['addHeaderRuler'] = true;
    
        //CVarDumper::dump($vacancy, 10, true);die;
        // и упаковываем ее в блок письма
        $this->addSegment($vacancyInfo);
    
        // добавляем всех участников роли
        foreach ( $vacancy['members'] as $qid => $member )
        {
            if ( ! $questionary = Questionary::model()->findByPk($qid) )
            {// на момент формирования фотовызывного участник в базе был, а в момент отправки или отображения
                // его нет - не можем показать его анкету
                // @todo достаточно редкая ситуация, но все равно нужно записать эту ошибку в лог
                continue;
            }
            $this->addActor($questionary, $member->vacancyid);
        }
    }
    
    /**
     * Добавить в письмо блок с фотографией и описанием одного актера
     * @param Questionary $questionary
     * @param int         $vacancyId
     * @return void
     */
    protected function addActor($questionary, $vacancyId=null)
    {
        // добавляем основную информацию об актере
        $mainBlock = array();
        $mainBlock['type']         = 'imageLeft';
        $mainBlock['imageStyle']   = 'border:3px solid #c3c3c3;border-radius:10px;height:150px;width:150px;margin-top:5px;';
        $mainBlock['imageLink']    = $questionary->getAvatarUrl('catalog');
        $mainBlock['text']         = $this->getActorDescription($questionary);
        $mainBlock['addTextRuler'] = false;
        $mainBlock['textColor']    = '#000';
        $this->addSegment($mainBlock);
        
        // добавляем образование и фильмографию
        $expBlock = array();
        $expBlock['type']           = 'text640';
        $expBlock['text']           = $this->addActorExperience($questionary, $vacancyId);
        $expBlock['addTextRuler']   = true;
        $expBlock['pageBreakAfter'] = 'always';
        $this->addSegment($expBlock);
        
        // добавляем доп. поля из заявки
        /*$addBlock = array();
        $addBlock['type']           = 'text640';
        $addBlock['text']           = $this->getExtraFieldsTable($questionary, $vacancyId);
        $addBlock['addTextRuler']   = false;
        $addBlock['pageBreakAfter'] = 'always';
        $this->addSegment($addBlock);*/
    }
    
    /**
     * Описание одного актера
     * @param Questionary $questionary
     * @return string
     */
    protected function getActorDescription($questionary)
    {
        $result     = '';
        $bages      = $questionary->getBages();
        $data       = array();
        $attributes = array();
    
        $result .= '<h3 style="text-transform:uppercase;font-size:20px;font-weight:bold;color:#286B84;margin:11px 0px 6px 0px;">'.
            $questionary->fullname.'</h3>';
        if ( ! empty($bages) )
        {
            $result .= MailComposerModule::t('qualification').': <i>' . implode(', ', $bages) . '</i><br>';
        }
        
        // собираем в массив все поля содержащие основную информацию
        $fields = array(
            // внешность и основная информация
            'age', 'playage', 'physiquetype','looktype', 'hairlength', 'haircolor', 'eyecolor', 'addchar',
            // остальные параметры
            'height', 'weight', 'chestsize', 'waistsize', 'hipsize', 'wearsize', 'shoessize', 'titsize',
        );
        
        foreach ( $fields as $field )
        {// получаем блок с информацией для каждого поля анкеты
            if ( $property = $this->getPropertyData($field, $questionary) )
            {
                $data[$field] = $property[1];
                $attributes[] = array('name' => $field, 'label' => $property[0], 'type' => 'html');
            }
        }
        
        $result .= $this->widget('bootstrap.widgets.TbDetailView', array(
            'data'       => $data,
            'attributes' => $attributes,
            'type'       => 'stripped bordered'
        ), true);
        
        return $result;
    }
    
    /**
     * Получить ссылку на просмотр веб-версии письма на сайте
     *
     * @return string
     */
    protected function getWebViewLink()
    {
        $url = Yii::app()->createAbsoluteUrl('/mailComposer/mail/display', array(
            'type' => 'castingList',
            'id'   => $this->callList->id,
            'key'  => $this->callList->key,
        ));
        return CHtml::link('Версия для печати', $url, array(
            'target' => '_blank',
            'style'  => 'color:#fff;font-weight:bold;',
        ));
    }
    
    /**
     * Дополнительная информация об актере: фильмография и образование
     * @param Questionary $questionary
     * @return string
     */
    protected function addActorExperience($questionary, $vacancyId=null)
    {
        $result     = '';
        $data       = array();
        $attributes = array();
        
        $result .= $this->render('experience', array(
            'questionary' => $questionary,
            'vacancyId'   => $vacancyId,
        ), true);
        
        return $result;
    }
    
    /**
     * Получить блок с описанием одного поля анкеты
     * @param string $field - поле в анкете (как оно называется в базе)
     * @return string
     * 
     * @todo добавить доп. характеристики
     */
    protected function getPropertyData($field, $questionary)
    {
        $label       = $questionary->getAttributeLabel($field);
        $value       = '';
        $placeholder = '['.Yii::t('coreMessages', 'no_data').']';
        $affix       = '';
        $hint        = '';
    
        switch ( $field )
        {
            case 'age':
                $placeholder = '['.Yii::t('coreMessages', 'not_set').']';
                if ( $value = $questionary->age )
                {
                    $info  = explode(' ', $value);
                    $value = $info[0];
                    $affix = $info[1];
                }
            break;
            case 'playage':
                $placeholder = '['.Yii::t('coreMessages', 'not_set').']';
                $value       = $questionary->playage;
            break;
            case 'looktype':
                if ( $questionary->nativecountry )
                {
                    $hint = $questionary->getAttributeLabel('nativecountryid').': '.$questionary->nativecountry->name;
                }
                $placeholder = '['.Yii::t('coreMessages', 'not_set').']';
                $value       = $questionary->getScalarFieldDisplayValue($field, $questionary->$field);
            break;
            case 'height':
            case 'chestsize':
            case 'waistsize':
            case 'hipsize':
                $affix = Yii::t('coreMessages', 'cm');
                $value = $questionary->getScalarFieldDisplayValue($field, $questionary->$field);
            break;
            case 'weight':
                $affix = Yii::t('coreMessages', 'kg');
                $value = $questionary->getScalarFieldDisplayValue($field, $questionary->$field);
            break;
            case 'addchar': 
                /*if ( ! $value = $this->getAddCharPropertyBlock() )
                {// не выводим поле с дополнительными хакактеристиками если оно не заполнено
                    return;
                }*/
                // @todo временно вообще не выводим дополнительные характеристики
                return;
            break;
            case 'titsize':
                if ( $questionary->gender == 'female' AND $questionary->Titsize )
                {// не выводим поле с дополнительными хакактеристиками если оно не заполнено
                    $value = $questionary->getScalarFieldDisplayValue($field, $questionary->$field);
                }
            break;
            default: 
                $value = $questionary->getScalarFieldDisplayValue($field, $questionary->$field);
            break;
        }
        if ( ! $value )
        {// значение не указано - выведем заглушку
            return;
            // @todo если пользователь - админ то показывать что поле не заполнено
            // $muted = true;
        }
        return array($label, $value.' '.$affix);
    }
    
    
    /**
     * Получить содержимое вкладки "Образование"
     * @param Questionary $questionary
     * @return string - html-код содержимого вкладки
     */
    protected function getEducationTable($questionary)
    {
        $content = '';
        $module  = Yii::app()->getModule('questionary');
    
        // театральные ВУЗы
        if ( $questionary->isactor AND $questionary->actoruniversities )
        {
            $content .= '<h3 style="text-align:center;">'.$module::t('actor_universities_label').'</h3>';
            $content .= $this->getUniversityTable($questionary->actoruniversities);
        }
        // музыкальные ВУЗы
        if ( $questionary->musicuniversities )
        {
            $content .= '<h3 style="text-align:center;">'.$module::t('music_universities_label').'</h3>';
            $content .= $this->getUniversityTable($questionary->musicuniversities);
        }
        // Модельные школы
        if ( $questionary->modelschools )
        {
            $content .= '<h3 style="text-align:center;">'.$module::t('model_schools_label').'</h3>';
            $items = array();
            foreach ( $questionary->modelschools as $school )
            {
                $school->setScenario('view');
                $items[] = $this->getSchoolInfo($school);
            }
            $dataProvider = new CArrayDataProvider($items);
    
            $content .= $this->widget('bootstrap.widgets.TbGridView', array(
                'type'         => 'striped bordered',
                'dataProvider' => $dataProvider,
                'template'     => "{items}",
                'columns' => array(
                    array('name' => 'name', 'header' => $module::t('model_school_label')),
                    array('name' => 'year', 'header' => $module::t('finish_year')),
                ),
            ), true);
        }
        return $content;
    }
    
    /**
     * Получить таблицу со списком ВУЗов
     * @param  array $universities
     * @return string
     */
    protected function getUniversityTable($universities)
    {
        $items  = array();
        $module = Yii::app()->getModule('questionary');
        foreach ( $universities as $university )
        {
            $university->setScenario('view');
            $items[] = $this->getUniversityInfo($university);
        }
        $dataProvider = new CArrayDataProvider($items);
    
        // выводим список ВУЗов
        return $this->widget('bootstrap.widgets.TbGridView', array(
            'type'         => 'striped bordered',
            'dataProvider' => $dataProvider,
            'template'     => "{items}",
            'columns' => array(
                array(
                    'name'   => 'name',
                    'header' => $module::t('university')),
                array(
                    'name'   => 'specialty',
                    'header' => $module::t('specialty')),
                array(
                    'name'   => 'year',
                    'header' => $module::t('finish_year')),
                array(
                    'name'   => 'workshop',
                    'header' => $module::t('workshop')),
            ),
        ), true);
    }
    
    /**
     * Получить фильмографию актера
     * @param Questionary $questionary
     * @return string - html-код содержимого вкладки
     */
    protected function getFilmsTable($questionary)
    {
        $content = '';
        $module  = Yii::app()->getModule('questionary');
    
        if ( $questionary->films )
        {
            $content .= '<h3 style="text-align:center;">'.$module::t('films_label').'</h3>';
    
            $films = array();
            $i     = 0;
            foreach ( $questionary->films as $film )
            {// собираем информацию по фильмографии
                $film->setScenario('view');
                $films[] = $this->getFilmInfo($film);
                $i++;
                
                if ( $i > 10 )
                {// выводим максимум 10 элементов из фильмографии
                    break;
                }
            }
    
            $dataProvider = new CArrayDataProvider($films, array('pagination' => false));
            $content .= $this->widget('bootstrap.widgets.TbGridView', array(
                'type'         => 'striped bordered',
                'dataProvider' => $dataProvider,
                'template'     => "{items}",
                'columns'      => array(
                    array(
                        'name'   => 'name',
                        'header' => $module::t('film_name_label'),
                    ),
                    array(
                        'name'   => 'role',
                        'header' => $module::t('film_role_label'),
                    ),
                    array(
                        'name'   => 'year',
                        'header' => $module::t('film_year_label'),
                    ),
                    array(
                        'name'   => 'director',
                        'header' => $module::t('film_director_label'),
                    ),
                ),
            ), true);
        }
        return $content;
    }
    
    /**
     * Получить таблицу со списком доп. полей
     * 
     * @return string - html-код таблицы с доп. полями
     */
    protected function getExtraFieldsTable($questionary, $vacancyId)
    {
        $content  = '';
        $module   = Yii::app()->getModule('questionary');
        
        $efValues = ExtraFieldValue::model()->
            forQuestionary($questionary)->forVacancy($vacancyId)->findAll();
        if ( $efValues )
        {
            $content .= '<h3 style="text-align:center;">Дополнительная информация</h3>';
            $extraValues = array();
            foreach ( $efValues as $efValue )
            {// собираем информацию по фильмографии
                if ( ! trim($efValue->value) )
                {// не выводим незаполненные поля
                    continue;
                }
                $extraValues[] = $this->getExtraFieldInfo($efValue);
            }
        
            $dataProvider = new CArrayDataProvider($extraValues, array('pagination' => false));
            $content .= $this->widget('bootstrap.widgets.TbGridView', array(
                'type'         => 'striped bordered condensed responsive',
                'htmlOptions'  => array(
                    'style' => 'border-width:1px;border-color:#ccc;',
                ),
                'dataProvider' => $dataProvider,
                'template'     => "{items}",
                'columns'      => array(
                    array(
                        'name'   => 'name',
                        'header' => Yii::t('coreMessages', 'question'),
                        'type'   => 'html',
                        'htmlOptions' => array(
                            'style' => 'text-style:italic;', 
                        ),
                    ),
                    array(
                        'name'   => 'value',
                        'header' => Yii::t('coreMessages', 'answer'),
                    ),
                ),
            ), true);
        }
        return $content;
    }
    
    /**
     * Получить информацию об одном фмльме из фильмографии
     * Если фотовызывной формируется на английском - то вся информация пишется транслитом
     * @param QFilm $film
     * @return array
     */
    protected function getFilmInfo($film)
    {
        $item = array();
        
        if ( $this->language === 'en' )
        {// для английского языка используем транслит (пока нет синхронизации с Кинопоиском)
            $item['id']       = $film->id;
            $item['name']     = EcPurifier::translit($film->name);
            $item['role']     = EcPurifier::translit($film->role);
            $item['year']     = $film->year;
            $item['director'] = EcPurifier::translit($film->director);
        }else
        {// во всех остальных случаях выводим данные как есть
            $item['id']       = $film->id;
            $item['name']     = $film->name;
            $item['role']     = $film->role;
            $item['year']     = $film->year;
            $item['director'] = $film->director;
        }
        return $item;
    }
    
    /**
     * Получить информацию о ВУЗе
     * Если фотовызывной формируется на английском - то вся информация пишется транслитом
     * 
     * @param unknown $university
     * @return void
     */
    protected function getUniversityInfo($university)
    {
        $item = array();
        
        if ( $this->language === 'en' )
        {// для английского языка используем транслит 
            // (пока нет синхронизации с каким-нибудь глобальным каталогом ВУЗов)
            $item['id']        = $university->id;
            $item['name']      = EcPurifier::translit($university->name);
            $item['specialty'] = EcPurifier::translit($university->specialty);
            $item['year']      = $university->year;
            $item['workshop']  = EcPurifier::translit($university->workshop);
        }else
        {// во всех остальных случаях выводим данные как есть
            $item['id']        = $university->id;
            $item['name']      = $university->name;
            $item['specialty'] = $university->specialty;
            $item['year']      = $university->year;
            $item['workshop']  = $university->workshop;
        }
        return $item;
    }
    
    /**
     * Получить информацию о модельной школе
     * 
     * @param QModelSchool $school
     * @return array
     */
    protected function getSchoolInfo($school)
    {
        $item = array();
        
        if ( $this->language === 'en' )
        {
            $item['id']   = $school->id;
            $item['name'] = EcPurifier::translit($school->school);
            $item['year'] = $school->year;
        }else
        {
            $item['id']   = $school->id;
            $item['name'] = $school->school;
            $item['year'] = $school->year;
        }
        return $item;
    }
    
    /**
     * Получить информацию об одном фмльме из фильмографии
     * Если фотовызывной формируется на английском - то вся информация пишется транслитом
     * 
     * @param ExtraFieldValue $extraFieldValue
     * @return array
     */
    protected function getExtraFieldInfo($extraFieldValue)
    {
        $item = array();
        
        $item['id']    = $extraFieldValue->id;
        $item['name']  = $extraFieldValue->fieldObject->label;
        $item['value'] = $extraFieldValue->value;
        
        return $item;
    }
}