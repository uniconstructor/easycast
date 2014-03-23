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
 */
class EMailCastingList extends EMailCallList
{
    /**
     * Добавить в письмо блок с фотографией и описанием одного актера
     * @param Questionary $questionary
     * @return void
     */
    protected function addActor($questionary)
    {
        // добавляем основную информацию об актере
        $mainBlock = array();
        $mainBlock['type']         = 'imageLeft';
        $mainBlock['imageStyle']   = 'border:3px solid #c3c3c3;border-radius:10px;height:150px;width:150px;margin-top:5px;';
        $mainBlock['imageLink']    = $questionary->getAvatarUrl('catalog');
        $mainBlock['text']         = $this->getActorDescription($questionary);
        $mainBlock['addTextRuler'] = false;
        $this->addSegment($mainBlock);
        
        // добавляем образование и фильмографию
        $expBlock = array();
        $expBlock['type']           = 'text640';
        $expBlock['text']           = $this->addActorExperience($questionary);
        $expBlock['addTextRuler']   = true;
        $expBlock['pageBreakAfter'] = 'always';
        $this->addSegment($expBlock);
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
            $result .= 'Квалификация: <i>' . implode(', ', $bages) . '</i><br>';
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
    protected function addActorExperience($questionary)
    {
        $result     = '';
        $data       = array();
        $attributes = array();
        
        $result .= $this->render('experience', array('questionary' => $questionary), true);
        
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
        $placeholder = '[нет данных]';
        $affix       = '';
        $hint        = '';
    
        switch ( $field )
        {
            case 'age':
                $placeholder = '[не указан]';
                if ( $value = $questionary->age )
                {
                    $info = explode(' ', $value);
                    $value = $info[0];
                    $affix = $info[1];
                }
                break;
            case 'playage':
                $placeholder = '[не указан]';
                $value = $questionary->playage;
                break;
            case 'looktype':
                if ( $questionary->nativecountry )
                {
                    $hint = $questionary->getAttributeLabel('nativecountryid').': '.$questionary->nativecountry->name;
                }
                $placeholder = '[не указан]';
                $value = $questionary->getScalarFieldDisplayValue($field, $questionary->$field);
                break;
            case 'height':
            case 'chestsize':
            case 'waistsize':
            case 'hipsize':
                $affix = 'см';
                $value = $questionary->getScalarFieldDisplayValue($field, $questionary->$field);
                break;
            case 'weight':
                $affix = 'кг';
                $value = $questionary->getScalarFieldDisplayValue($field, $questionary->$field);
                break;
            case 'addchar': 
                /*if ( ! $value = $this->getAddCharPropertyBlock() )
                {// не выводим поле с дополнительными хакактеристиками если оно не заполнено
                    return;
                }*/
                return;
                break;
            case 'titsize':
                if ( $questionary->gender == 'female' AND $questionary->Titsize )
                {// не выводим поле с дополнительными хакактеристиками если оно не заполнено
                    $value = $questionary->getScalarFieldDisplayValue($field, $questionary->$field);
                }
                break;
            default: $value = $questionary->getScalarFieldDisplayValue($field, $questionary->$field); break;
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
     *
     * @return string - html-код содержимого вкладки
     */
    protected function getEducationTable($questionary)
    {
        $content     = '';
        $module      = Yii::app()->getModule('questionary');
    
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
                $item = array();
                $item['id']   = $school->id;
                $item['name'] = $school->school;
                $item['year'] = $school->year;
                $items[] = $item;
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
     * @param array $universities
     * @return string
     */
    protected function getUniversityTable($universities)
    {
        $items  = array();
        $module = Yii::app()->getModule('questionary');
        foreach ( $universities as $university )
        {
            $university->setScenario('view');
            $item = array();
            $item['id']        = $university->id;
            $item['name']      = $university->name;
            $item['specialty'] = $university->specialty;
            $item['year']      = $university->year;
            $item['workshop']  = $university->workshop;
            $items[] = $item;
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
            $i = 0;
            foreach ( $questionary->films as $film )
            {
                $film->setScenario('view');
                $element = array();
                $element['id']       = $film->id;
                $element['name']     = $film->name;
                $element['role']     = $film->role;
                $element['year']     = $film->year;
                $element['director'] = $film->director;
                $films[] = $film;
                $i++;
                
                if ( $i > 10 )
                {
                    break;
                }
            }
    
            $dataProvider = new CArrayDataProvider($films, array(
                'pagination' => false)
            );
            $content .= $this->widget('bootstrap.widgets.TbGridView', array(
                'type'         => 'striped bordered',
                'dataProvider' => $dataProvider,
                'template'     => "{items}",
                'columns' => array(
                    array(
                        'name'   => 'name',
                        'header' => $module::t('film_name_label')),
                    array(
                        'name'   => 'role',
                        'header' => $module::t('film_role_label')),
                    array(
                        'name'   => 'year',
                        'header' => $module::t('film_year_label')),
                    array(
                        'name'   => 'director',
                        'header' => $module::t('film_director_label')),
                ),
            ), true);
        }
    
        return $content;
    }
}