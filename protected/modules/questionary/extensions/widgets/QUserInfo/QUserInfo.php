<?php

/**
 * Виджет, отображающий всю информацию из анкеты пользователя, разбитую по вкладкам
 * 
 * @todo добавить возможность использовать html в названиях вкладок
 * @todo сделать метод canViewTab, и вынести в него всю проверку прав
 * @todo переписать с использованием view-файлов
 * @todo показывать пользователю его условия и контакты, с добавлением сообщения о 
 *       том что это его анкета и контакты видны только ему. Исправить функции отображения вкладок и функцию
 *       составления стандартного списка вкладок
 * @todo добавить проверку прав для случая когда вкладки переданы в виджет параметрами.
 *       (можно изначально добавить все, а в init() убирать те вкладки, на которые не хватает прав)
 */
class QUserInfo extends CWidget
{
    /**
     * @var Questionary - анкета участника
     */
    public $questionary;
    /**
     * @var array - список тех вкладок, которые нужно отобразить (по умолчанию - все)
     */
    public $tabNames = array();
    /**
     * @var string - активная в начале вкладка
     * Возможные значения: 'main', 'education', 'skills', 'films', 'model', 'projects', 'awards', 'misc'
     */
    public $activeTab = 'main';
    /**
     * @var bool
     */
    public $nameAsLink;
    
    /**
     * @var string - путь к папке со стилями и скриптами для виджета
     */
    protected $_assetUrl;
    
    /**
     * Подготавливает виджет к отображению
     */
    public function init()
    {
        // Подключаем CSS для оформления
        $this->_assetUrl = Yii::app()->assetManager->publish(
                        Yii::getPathOfAlias('application.modules.questionary.extensions.widgets.QUserInfo.assets') . 
                        DIRECTORY_SEPARATOR);
        Yii::app()->clientScript->registerCssFile($this->_assetUrl.'/QUserInfo.css');
        
        // устанавливаем в объекте анкеты режим "просмотр"
        $this->questionary->setScenario('view');
        if ( $this->questionary->recordingconditions )
        {
            $this->questionary->recordingconditions->setScenario('view');
        }
        
        // Подключаем нужные для отображения информации классы
        Yii::import('application.modules.questionary.extensions.behaviors.*');
        Yii::import('application.modules.questionary.models.*');
        Yii::import('application.modules.questionary.models.complexValues.*');
        Yii::import('ext.CountryCitySelectorRu.*');
        
        if ( ! $this->tabNames )
        {// если явно не указано, какие вкладки отображать - отобразим все которые можно видеть
            $this->tabNames = $this->getDefaultTabNames();
        }
        if ( ! in_array($this->activeTab, $this->tabNames) )
        {// если нужную вкладку по каким-то причинам нельзя отобразить - переправим пользователя на основную
            $this->activeTab = 'main';
        }
    }
    
    /**
     * Отображает виджет
     * @see CWidget::run()
     */
    public function run()
    {
        $tabs = array();
        
        foreach ( $this->tabNames as $tabName )
        {// собираем информацию по каждому разделу
            if ( $tab = $this->getTab($tabName) )
            {// показываем вкладку только тогда, когда в ней есть содержимое
                $tabs[$tabName] = $tab;
            }
        }
        
        $this->widget('bootstrap.widgets.TbTabs', array(
            'type'      => 'tabs',
            'placement' => 'right',
            'tabs'      => $tabs,
        ));
    }
    
    /**
     * Получить список вкладок с информацией о пользователе, которые нужно отобразить
     * (по умолчанию отображаются все, кроме тех которые запрещены правами доступа)
     * 
     * @return array
     */
    protected function getDefaultTabNames()
    {
        // эти вкладки видны всем
        $tabs = array(
            'main',
            'education',
            'skills',
            'films',
            'projects',
            'awards', 
        );
        if ( Yii::app()->user->checkAccess('Admin') )
        {// контакты и условия съемок может видеть только админ
            $tabs[] = 'personal';
            $tabs[] = 'conditions';
        }
        if ( $this->isMyQuestionary() OR Yii::app()->user->checkAccess('Admin') )
        {// для своей анкеты добавляем вкладки cо съемками и приглашениями
            $tabs[] = 'invites';
            $tabs[] = 'requests';
            $tabs[] = 'events';
        }
        return $tabs;
    }
    
    /**
     * Получить вкладку с информацией о пользователе (в зависимости от типа)
     * 
     * @param string $name - короткое название вкладки
     * @return array|bool - вкладка вместе содержимым или false если вкладку отображать не нужно
     * 
     * @todo переместить проверку прав
     */
    protected function getTab($name)
    {
        $content = '';
        
        switch ( $name )
        {
            // "основное" - показывается всегда
            case 'main':       $content = $this->getMainTabContent(); break;
            // "образование" - показывается, если есть хотя бы одни ВУЗ
            case 'education':  $content = $this->getEducationTabContent(); break; 
            // "умения и навыки" - показывается если есть хотя бы один навык или умение (то есть почти всегда)
            case 'skills':     $content = $this->getSkillsTabContent(); break;
            // "фильмография" - показывается если есть хотя бы один фильм
            case 'films':      $content = $this->getFilmsTabContent(); break;
            // "проекты" - показывается, если человек учавствовал хотя бы в одном проекте
            case 'projects':   $content = $this->getProjectsTabContent(); break;
            // "награды" - показывается, если есть хотя бы одна награда
            case 'awards':     $content = $this->getAwardsTabContent(); break;
            // "условия" - показывается всегда, но только админам или модераторам, содержит условия участия в съемках
            case 'conditions': $content = $this->getConditionsTabContent(); break;
            // "Контакты" - показывается только админам, содержит контакты и личную информацию
            case 'personal':   $content = $this->getPersonalTabContent(); break;
            // "Мои приглашения" - показывается всегда, но только на своей странице
            case 'invites':    $content = $this->getInvitesTabContent(); break;
            // "Мои заявки" - показываются только если есть, только на своей странице
            case 'requests':   $content = $this->getRequestsTabContent(); break;
            // "Мои съемки" - показываются только если есть, только на своей странице
            case 'events':     $content = $this->getEventsTabContent(); break;
            
            default: $content = $this->getMainTabContent();//throw new CException('Неизвестный тип вкладки: '.$name);
        }
        
        if ( ! $content )
        {// во вкладке нет никакой информации - значит отображать ее не нужно
            return false;
        }
        
        // во вкладке есть информация - соберем ее
        $tab = array();
        $tab['label']   = $this->getTabLabel($name);
        $tab['content'] = $content;
        if ( $name == $this->activeTab )
        {// делаем вкладку активной если нужно
            $tab['active'] = true;
        }
        
        return $tab;
    }
    
    /**
     * Получить название вкладки по ее короткому имени
     * 
     * @param string $name - короткое название вкладки
     * @return string
     */
    protected function getTabLabel($name)
    {
        $label = QuestionaryModule::t('userinfo_section_'.$name);
        if ( $count = $this->getTabCount($name) )
        {// к вкладке добавляем счетчик количества объектов (если предусмотрен)
            $label .= ' ('.$count.')';
        }
        
        return $label;
    }
    
    /**
     * Получить счетчик количества объектов во вкладке (если предусмотрен)
     * @param string $name - короткое название вкладки
     * @return string
     */
    protected function getTabCount($name)
    {
        switch ( $name )
        {
            case 'invites':  return $this->questionary->invitesCount; break;
            case 'requests': return $this->questionary->requestsCount; break;
            case 'events':   return $this->questionary->upcomingEventsCount; break;
        }
        return false;
    }
    
    /**
     * Получить блок с описанием одного поля анкеты
     * @param string $field - поле в анкете (как оно называется в базе)
     * @return string
     */
    protected function getPropertyBlock($field)
    {
        $questionary = $this->questionary;
        
        $label       = $questionary->getAttributeLabel($field);
        $value       = '';
        $placeholder = '[нет данных]';
        $affix       = '';
        $hint        = '';
        
        switch ( $field )
        {
            case 'age': 
                $placeholder = '[не указан]';
                $value = $this->questionary->age;
            break;
            case 'playage': 
                $placeholder = '[не указан]';
                $value = $this->questionary->playage;
            break;
            case 'looktype':
                $hint = $questionary->getAttributeLabel('nativecountryid').': '.$questionary->nativecountry->name;
                $placeholder = '[не указан]';
                $value = $questionary->getScalarFieldDisplayValue($field, $questionary->$field);
            break;
            case 'height': 
                $affix = 'см';
                $value = $questionary->getScalarFieldDisplayValue($field, $questionary->$field);
            break;
            case 'weight': 
                $affix = 'кг';
                $value = $questionary->getScalarFieldDisplayValue($field, $questionary->$field);
            break;
            case 'addchar':
                if ( ! $value = $this->getAddCharPropertyBlock() )
                {// не выводим поле с дополнительными хакактеристиками если оно не заполнено
                    return '';
                }
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
            $muted = true;
        }
        
        // получаем параметры для виджета с информацией об анкете
        $options = $this->getPropertyOptions($label, $value, $placeholder, $affix, $hint);
        
        return $this->widget('ext.ECMarkup.ECProperty.ECProperty', $options, true);
    }
    
    /**
     * Получить блок с информацией о дополнительных ракактеристиках внешности
     * @return string
     */
    protected function getAddCharPropertyBlock()
    {
        $questionary = $this->questionary;
        $misc = '';
        if ( $addchars = $questionary->addchars )
        {
            foreach ( $addchars as $addchar )
            {
                $addchar->setScenario('view');
                if ( trim($addchar->name) )
                {
                    $misc .= $addchar->name.'<br>';
                }
            }
        }
        if ( $questionary->isathlete )
        {// атлет
            $misc .= $questionary->getAttributeLabel('isathlete').'<br>';
        }
        if ( $questionary->hastatoo )
        {// татуировки
            $misc .= QuestionaryModule::t('hastatoo_enabled').'<br>';
        }
        
        return $misc;
    }
    
    /**
     * Получить параметры для создания блока описанием одного поля анкеты
     * @param string $label - пояснение для поля анкеты
     * @param string $value - значение поля
     * @param string $placeHolder - текст, который выводится если нет значения
     * @param string $affix - подсказка после значения (если есть)
     * 
     * @return array
     */
    protected function getPropertyOptions($label, $value, $placeholder='[нет данных]', $affix='', $hint='')
    {
        $muted = false;
        if ( ! $placeholder )
        {
            $placeholder = '[нет данных]';
        }
        if ( ! $value )
        {
            $value = $placeholder;
            $muted  = true;
        }
        return array(
            'label'  => $label,
            'value'  => $value,
            'affix'  => $affix,
            'muted'  => $muted,
        );
    }
    
    /**
     * Получить содержимое вкладки "Основное"
     * @return string - html-код содержимого вкладки
     * 
     * @todo разделить на 2 виджета
     */
    protected function getMainTabContent()
    {
        $content = '';
        $questionary = $this->questionary;
        
        // Внешность
        $data    = array();
        $options = array();
        $fields  = array();
        
        // собираем в массив все поля содержащие основную информацию
        $fields['main'] = array(
            // внешность (первый блок)
            'age', 'playage', 'physiquetype','looktype', 'hairlength', 'haircolor', 'eyecolor', 'addchar',
            // остальные параметры
            'height', 'weight', 'chestsize', 'waistsize', 'hipsize', 'wearsize', 'shoessize', 'titsize',
        );
        
        foreach ( $fields['main'] as $field )
        {// получаем блок с информацией для каждого поля анкеты
            $data[$field] = $this->getPropertyBlock($field);
        }
        
        if ( Yii::app()->user->checkAccess('Admin') AND trim(strip_tags($questionary->privatecomment)) )
        {// Только для администраторов: выводим дополнительную информацию об анкете
            $content .= '<div class="ec-round-the-corner" style="background-color:#000;padding:20px;">';
            $content .= '<h4>'.'Дополнительная информация для администраторов (не видна участнику)'.'</h4>';
            $content .= $questionary->privatecomment.'</div>';
        }
        
        // Выводим всю основную информацию
        $content .= $this->render('main', array('data' => $data), true);
        
        return $content;
    }
    
    /**
     * Получить содержимое вкладки "Образование"
     *
     * @return string - html-код содержимого вкладки
     */
    protected function getEducationTabContent()
    {
        $content = '';
        $questionary = $this->questionary;
        
        // театральные ВУЗы
        if ( $questionary->isactor AND $questionary->actoruniversities )
        {
            $content .= '<h3>'.QuestionaryModule::t('actor_universities_label').'</h3>';
            $content .= $this->getUniversityTable($questionary->actoruniversities);
        }
        
        // музыкальные ВУЗы
        if ( $questionary->musicuniversities )
        {
            $content .= '<h3>'.QuestionaryModule::t('music_universities_label').'</h3>';
            $content .= $this->getUniversityTable($questionary->musicuniversities);
        }
        
        // Модельные школы
        if ( $questionary->modelschools )
        {
            $content .= '<h3>'.QuestionaryModule::t('model_schools_label').'</h3>';
            $items = array();
            foreach ( $questionary->modelschools as $school )
            {
                $school->setScenario('view');
                $item = array();
                $item['id']       = $school->id;
                $item['name']     = $school->school;
                $item['year']     = $school->year;
                $items[] = $item;
            }
            $dataProvider = new CArrayDataProvider($items);
            
            $content .= $this->widget('bootstrap.widgets.TbGridView', array(
                'type'         => 'striped bordered',
                'dataProvider' => $dataProvider,
                'template'     => "{items}",
                'columns' => array(
                    array('name'=>'name', 'header'=>QuestionaryModule::t('model_school_label')),
                    array('name'=>'year', 'header'=>QuestionaryModule::t('finish_year')),
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
        $items = array();
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
                        'template'=>"{items}",
                        'columns'=>array(
                                        array('name'=>'name', 'header'=>QuestionaryModule::t('university')),
                                        array('name'=>'specialty', 'header'=>QuestionaryModule::t('specialty')),
                                        array('name'=>'year', 'header'=>QuestionaryModule::t('finish_year')),
                                        array('name'=>'workshop', 'header'=>QuestionaryModule::t('workshop')),
                        ),
        ), true);
    }
    
    /**
     * Получить содержимое вкладки "Умения и навыки"
     *
     * @return string - html-код содержимого вкладки
     */
    protected function getSkillsTabContent()
    {
        $content = '';
        $questionary = $this->questionary;
        $bages = $questionary->getBages();
        
        $attributes = array();
        $data = array();
        $data['id'] = 1;
        
        // навыки
        if ( $questionary->hasskills )
        {
            list($attribute, $value) = $this->getUlRow(
            'skills', $questionary->skills,
            'skills',
            null,
            'name');
            $attributes[] = $attribute;
            $data['skills'] = $value;
        }
        // ведущий
        if ( $questionary->isemcee )
        {
            list($attribute, $value) = $this->getUlRow(
                        'emcee', $questionary->emceelist,
                        'isemcee_label',
                        'events',
                        'event',
                        'year');
            $attributes[] = $attribute;
            $data['emcee'] = $value;
        }
        // телеведущий
        if ( $questionary->istvshowmen )
        {
            $attributes[] = array('name'=>'tvshowmen', 'label'=>QuestionaryModule::t('istvshowmen_label'), 'type'=>'html');
            $data['tvshowmen'] = '';
            if ( $questionary->tvshows )
            {
                $data['tvshowmen'] .= QuestionaryModule::t('tvshowmen_show').': ';
                $data['tvshowmen'] .= '<ul>';
                foreach ( $questionary->tvshows as $show )
                {
                    $show->setScenario('view');
                    $showItem    = '';
                    $showDetails = '';
                    
                    // передача
                    $showItem .= $show->projectname;
                    if ( $show->channelname )
                    {// канал
                        $showDetails .= $show->channelname;
                    }
                    if ( $show->period )
                    {// период работы
                        $showDetails .= ', '.$show->period;
                    }
                    if ( $showDetails )
                    {// склеиваем все вместе
                        $showDetails = '('.$showDetails.')';
                        $showItem .= ' '.$showDetails;
                    }
                    $data['tvshowmen'] .= '<li>'.$showItem.'</li>';
                }
                $data['tvshowmen'] .= '</ul>';
            }
        }
        
        // актер театра
        if ( $questionary->istheatreactor )
        {
            if ( ! $questionary->gender OR $questionary->gender == 'male' )
            {
                $theatresTitle = 'theatreactor(male)';
            }else
          {
                $theatresTitle = 'theatreactor(female)';
            }
            
            $attributes[] = array('name'=>'theatreactor', 'label'=>QuestionaryModule::t($theatresTitle), 'type' => 'html');
            $data['theatreactor'] = $this->getTheatresList($questionary->theatres);
        }
        
        // пародист
        if ( $questionary->isparodist )
        {
            list($attribute, $value) = $this->getUlRow(
                        'parodist', $questionary->parodistlist,
                        'isparodist_label',
                        null,//'parodist_images',
                        'name');
            $attributes[] = $attribute;
            $data['parodist'] = $value;
        }
        
        // двойник
        if ( $questionary->istwin )
        {
            list($attribute, $value) = $this->getUlRow(
                        'twin', $questionary->twinlist,
                        'istwin_label',
                        null,//'twin_images',
                        'name');
            $attributes[] = $attribute;
            $data['twin'] = $value;
        }
        
        // Модель
        if ( $questionary->ismodel AND ( $questionary->modelschools OR $questionary->modeljobs ) )
        {
            list($attribute, $value) = $this->getUlRow(
                        'model', $questionary->modeljobs,
                        'ismodel_label',
                        'model_jobs_label',
                        'job',
                        'year');
            $attributes[] = $attribute;
            $data['model'] = $value;
        }
        
        // фотомодель
        if ( $questionary->isphotomodel AND ($questionary->photomodeljobs) )
        {
            list($attribute, $value) = $this->getUlRow(
            'photomodel', $questionary->photomodeljobs,
            'isphotomodel_label',
            'photomodel_jobs_label',
            'job',
            'year');
            $attributes[] = $attribute;
            $data['photomodel'] = $value;
        }
        
        // промо-модель
        if ( $questionary->ispromomodel AND ($questionary->promomodeljobs) )
        {
            list($attribute, $value) = $this->getUlRow(
            'promomodel', $questionary->promomodeljobs,
            'ispromomodel_label',
            'promomodel_jobs_label',
            'job',
            'year');
            $attributes[] = $attribute;
            $data['promomodel'] = $value;
        }
        
        // танцор
        if ( $questionary->isdancer )
        {
            list($attribute, $value) = $this->getUlRow(
                            'dancer', $questionary->dancetypes,
                            'isdancer_label',
                            null,
                            'name',
                            'Level');
            $attributes[] = $attribute;
            $data['dancer'] = $value;
        }
        
        // стриптиз
        if ( $questionary->isstripper AND $questionary->age >= 18 )
        {
            $attributes[] = array('name'=>'stripper', 'label'=>QuestionaryModule::t('isstripper_label'), 'type' => 'html');
            $data['stripper'] = '<ul><li>'.$questionary->Striptype.' ('.$questionary->Striplevel.')</li></ul>';
        }
        
        // певец
        if ( $questionary->issinger )
        {
            list($attribute, $vocaltypes) = $this->getCommaRow(
                            'singer', $questionary->vocaltypes,
                            'issinger_label',
                            'type',
                            'name');
            list($attribute, $voicetimbres) = $this->getCommaRow(
                            'singer', $questionary->voicetimbres,
                            'issinger_label',
                            'voicetimbre_label',
                            'name');
            $attribute['type'] = 'html';
            if ( $questionary->Singlevel )
            {// если указан уровень владения - выведем его
                $attribute['label'] .= '('.$questionary->Singlevel.')';
            }
            $attributes[] = $attribute;
            $data['singer'] = '<ul>';
            if ( trim($vocaltypes) )
            {// выводим типы вокала только если они указаны
                $data['singer'] .= '<li>'.$vocaltypes.'</li>';
            }
            if ( $voicetimbres )
            {// Выводим тембры голоса только если они указаны
                $data['singer'] .= '<li>'.$voicetimbres.'</li>';
            }
            $data['singer'] .= '</ul>';
        }
        
        // музыкант
        if ( $questionary->ismusician )
        {
            list($attribute, $value) = $this->getUlRow(
                            'musician', $questionary->instruments,
                            'ismusician_label',
                            null,
                            'name',
                            'Level');
            $attributes[] = $attribute;
            $data['musician'] = $value;
        }
        
        // спортсмен
        if ( $questionary->issportsman )
        {
            list($attribute, $value) = $this->getUlRow(
                            'sportsman', $questionary->sporttypes,
                            'issportsman_label',
                            null,
                            'name');
            $attributes[] = $attribute;
            $data['sportsman'] = $value;
        }
        
        // экстремал
        if ( $questionary->isextremal )
        {
            list($attribute, $value) = $this->getUlRow(
                            'extremal', $questionary->extremaltypes,
                            'isextremal_label',
                            null,
                            'name');
            $attributes[] = $attribute;
            $data['extremal'] = $value;
        }
        
        // выполнение трюков
        if ( $questionary->hastricks )
        {
            list($attribute, $value) = $this->getUlRow(
                            'tricks', $questionary->tricks,
                            'hastricks_label',
                            null,
                            'name');
            $attributes[] = $attribute;
            $data['tricks'] = $value;
        }
        
        // иностранный язык
        // @todo запустить заново миграцию с иностранными языками
        if ( $questionary->haslanuages AND $questionary->languages )
        {
            list($attribute, $value) = $this->getUlRow(
                            'lanuages', $questionary->languages,
                            'haslanuages_label',
                            null,
                            'name',
                            'Level');
            $attributes[] = $attribute;
            $data['lanuages'] = $value;
        }
        
        if ( ! empty($attributes) )
        {
            $content .= '<h3>'.QuestionaryModule::t('userinfo_section_skills').'</h3>';
            $content .= $this->widget('bootstrap.widgets.TbDetailView', array(
                            'data'       => $data,
                            'attributes' => $attributes), true);
        }
        
        return $content;
    }
    
    /**
     * Получить список театров в которых работал актер
     * @param unknown $theatres
     */
    protected function getTheatresList($theatres)
    {
        $items = array();
        foreach ( $theatres as $theatre )
        {
            $theatre->setScenario('view');
            $item = array();
            $item['id']         = $theatre->id;
            $item['name']       = $theatre->name;
            $item['workperiod'] = $theatre->workperiod;
            $item['director']   = $theatre->director;
            $items[] = $item;
        }
        $dataProvider = new CArrayDataProvider($items);
        
        // выводим список ВУЗов
        return $this->widget('bootstrap.widgets.TbGridView', array(
                        'type'         => 'striped bordered',
                        'dataProvider' => $dataProvider,
                        'template'=>"{items}",
                        'columns'=>array(
                            array('name'=>'name', 'header'=>QuestionaryModule::t('theatre')),
                            array('name'=>'director', 'header'=>QuestionaryModule::t('theatre_director')),
                            array('name'=>'workperiod', 'header'=>QuestionaryModule::t('theatre_workperiod')),
                        ),
        ), true);
        
        return $result;
    }
    
    /**
     * Получить маркированый список для одной строки таблицы с информацией об участнике
     * @param string $name - название ряда таблицы (нужно для виджета TbListView)
     * @param array $values - масив записей для списка
     * @param string $label - id строки перевода для поясняющей надписи слева
     * @param string $caption - id строки перевода для поясняющей надписи справа, вверху списка значений
     * @param string $mainField - поле из которого берется значение элемента списка
     * @param string $secondaryField - поле из которого берется значение в скобках
     * @return array - массив для создания одной строки таблицы с информацией о пользователе
     */
    protected function getUlRow($name, $values, $label, $caption=null, $mainField='name', $secondaryField=null)
    {
        $attribute = array('name'=>$name, 'label'=>QuestionaryModule::t($label), 'type'=>'html');
        $data = '';
        if ( $values )
        {
            if ( $caption )
            {
                $data .= QuestionaryModule::t($caption).': ';
            }
            $data .= '<ul>';
            foreach ( $values as $value )
            {
                $value->setScenario('view');
                $item = $value->$mainField;
                if ( $item AND $secondaryField AND trim($value->$secondaryField) )
                {
                    $item .= ' ('.$value->$secondaryField.')';
                }
                if ( trim($item) )
                {
                    $data .= '<li>'.$item.'</li>';
                }
            }
            $data .= '</ul>';
        }
        return array($attribute, $data);
    }
    
    /**
     * Получить список значений разделенных запятыми для одной строки таблицы
     * @param string $name - название ряда таблицы (нужно для виджета TbListView)
     * @param array $values - масив записей для списка
     * @param string $label - id строки перевода для поясняющей надписи слева
     * @param string $caption - id строки перевода для поясняющей надписи справа, вверху списка значений
     * @param string $mainField - поле из которого берется значение элемента списка
     * @param string $secondaryField - поле из которого берется значение в скобках
     * @return array - массив для создания одной строки таблицы с информацией о пользователе  
     */
    protected function getCommaRow($name, $values, $label, $caption=null, $mainField='name', $secondaryField=null)
    {
        $attribute = array('name'=>$name, 'label'=>QuestionaryModule::t($label));
        $data = '';
        if ( $values )
        {
            if ( $caption )
            {
                $data .= QuestionaryModule::t($caption).': ';
            }
            $items = array();
            foreach ( $values as $value )
            {
                $value->setScenario('view');
                $item = $value->$mainField;
                if ( $item AND $secondaryField AND trim($value->$secondaryField) )
                {
                    $item .= ' ('.$value->$secondaryField.')';
                }
                if ( trim($item) )
                {
                    $items[] = $item;
                }
            }
            $data .= implode(', ', $items);
        }
        return array($attribute, $data);
    }
    
    /**
     * Получить содержимое вкладки "Фильмография"
     * @return string - html-код содержимого вкладки
     */
    protected function getFilmsTabContent()
    {
        $content = '';
        $questionary = $this->questionary;
        
        if ( $this->questionary->films )
        {
            $content .= '<h3>'.QuestionaryModule::t('films_label').'</h3>';
            
            $films = array();
            foreach ( $this->questionary->films as $film )
            {
                $film->setScenario('view');
                $element = array();
                $element['id']       = $film->id;
                $element['name']     = $film->name;
                $element['role']     = $film->role;
                $element['year']     = $film->year;
                $element['director'] = $film->director;
                $films[] = $film;
            }
            
            $dataProvider = new CArrayDataProvider($films, array(
                'pagination' => false)
            );
            $content .= $this->widget('bootstrap.widgets.TbGridView', array(
                'type'         => 'striped bordered',
                'dataProvider' => $dataProvider,
                'template'     => "{items}",
                'columns' => array(
                    array('name' => 'name', 'header' => QuestionaryModule::t('film_name_label')),
                    array('name' => 'role', 'header' => QuestionaryModule::t('film_role_label')),
                    array('name' => 'year', 'header' => QuestionaryModule::t('film_year_label')),
                    array('name' => 'director', 'header' => QuestionaryModule::t('film_director_label')),
                ),
            ), true);
        }
        
        return $content;
    }
    
    /**
     * @deprecated - после переноса полей модели в раздел умений не используется. Удалить при рефакторинге
     *  
     * Получить таблицу с послужным списком модели
     * @param arrat $jobs
     * @param string $jobLabel
     * @return string - html-код таблицы 
     */
    protected function getModelJobsTable($jobs, $jobLabel)
    {
        if ( ! $jobs )
        {
            return '';
        }
        $elements = array();
        foreach ( $jobs as $job )
        {
            $element = array();
            $element['id']   = $job->id;
            $element['name'] = $job->job;
            $element['year'] = $job->year;
            $elements[] = $element;
        }
        
        $dataProvider = new CArrayDataProvider($elements);
        return $this->widget('bootstrap.widgets.TbGridView', array(
                    'type'         => 'striped bordered',
                    'dataProvider' => $dataProvider,
                    'template'=>"{items}",
                    'columns'=>array(
                        array('name'=>'name', 'header'=>QuestionaryModule::t($jobLabel)),
                        array('name'=>'year', 'header'=>QuestionaryModule::t('year_label')),
                    ),
        ), true);
    }
    
    /**
     * Получить содержимое вкладки "Проекты"
     *
     * @return string - html-код содержимого вкладки
     */
    protected function getProjectsTabContent()
    {
        $content = '';
        $questionary = $this->questionary;
        
        //$content .= '<h3>'.QuestionaryModule::t('userinfo_section_projects').'</h3>';
        
        
        return $content;
    }
    
    /**
     * Получить содержимое вкладки "Награды"
     *
     * @return string - html-код содержимого вкладки
     */
    protected function getAwardsTabContent()
    {
        $content = '';
        $questionary = $this->questionary;
        
        // Звания, призы и награды
        if ( $questionary->hasawards AND $questionary->awards )
        {
            $content .= '<h3>'.QuestionaryModule::t('awards_label').'</h3>';
            $elements = array();
            foreach ( $questionary->awards as $award )
            {
                $countryName = '';
                if ( isset($award->country->name) )
                {
                    $countryName = $award->country->name;
                }
                $element = array();
                $element['id'] = $award->id;
                $element['name'] = $award->name;
                $element['nomination'] = $award->nomination;
                $element['country'] = $countryName;
                $element['year'] = $award->year;
                $elements[] = $element;
            }
        
            $dataProvider = new CArrayDataProvider($elements);
            $content .= $this->widget('bootstrap.widgets.TbGridView', array(
                'type'         => 'striped bordered',
                'dataProvider' => $dataProvider,
                'template'=>"{items}",
                'columns'=>array(
                    array('name'=>'name', 'header'=>QuestionaryModule::t('award_name_label')),
                    array('name'=>'nomination', 'header'=>QuestionaryModule::t('award_nomination_label')),
                    array('name'=>'year', 'header'=>QuestionaryModule::t('year_label')),
                    array('name'=>'country', 'header'=>QuestionaryModule::t('award_country_label')),
                ),
            ), true);
        }
        
        return $content;
    }
    
    /**
     * Получить содержимое вкладки "Условия"
     *
     * @return string - html-код содержимого вкладки
     */
    protected function getConditionsTabContent()
    {
        if ( ! Yii::app()->user->checkAccess('Admin') )
        {// условия съемок видны только админам
            return false;
        }
        
        $content = '';
        $questionary = $this->questionary;
        $conditions  = $this->questionary->recordingconditions;
        $attributes  = array();
        $data = array();
        
        
        $content .= '<h3>'.QuestionaryModule::t('recording_conditions_label').'</h3>';
        
        // ночные съемки
        list($attributes[], $data['isnightrecording']) = 
            $this->getConditionRow('isnightrecording', 'nightrecording');
        
        // съемки топлесс
        if ( $questionary->age >= 18 )
        {// отображается только у совершеннолетних актеров, потому что совесть-то иметь надо :)
            list($attributes[], $data['istoplessrecording']) =
                $this->getConditionRow('istoplessrecording', 'istoplessrecording_label');
        }
        
        // социальные проекты
        list($attributes[], $data['isfreerecording']) =
            $this->getConditionRow('isfreerecording', 'isfreerecording_label');
        
        // командировки
        list($attributes[], $data['wantsbusinesstrips']) = 
            $this->getConditionRow('wantsbusinesstrips', 'business_trips', 'business_trips');
        
        // загранпаспорт (отображается только если есть согласие на командировки)
        if ( isset($conditions->wantsbusinesstrips) AND $conditions->wantsbusinesstrips )
        {
            list($attributes[], $data['hasforeignpassport']) = 
                $this->getConditionRow('hasforeignpassport', 'foreign_passport', 'foreign_passport', QuestionaryModule::t('has'));
        }
        // Дополнительные условия
        if ( isset($conditions->custom) AND trim($conditions->custom) )
        {
            $attributes[] = array('name'=>'custom', 'label'=>QuestionaryModule::t('actor_conditions'));
            $data['custom'] = $conditions->custom;
        }
        // Стоимость участия в съемках
        if ( isset($conditions->salary) AND $conditions->salary )
        {
            $attributes[] = array('name'=>'salary', 'label'=>QuestionaryModule::t('salary_label'));
            $data['salary'] = $conditions->Salary;
        }
        // Выводим таблицу с условиями        
        $content .= $this->widget('bootstrap.widgets.TbDetailView', array(
                        'data'       => $data,
                        'attributes' => $attributes), true);
        
        
        
        // Дополнительная информация (ее больше некуда впихнуть)
        $content .= '<h4>'.QuestionaryModule::t('userinfo_section_misc').'</h4>';
        $attributes = array();
        $data = array();
        
        // гражданство
        if ( $questionary->country )
        {
            $attributes[] = array('name'=>'country', 'label'=>QuestionaryModule::t('country_label'));
            $data['country'] = $questionary->country->name;
        }
        
        // город
        if ( $questionary->City )
        {
            $attributes[] = array('name'=>'city', 'label'=>QuestionaryModule::t('city_label'));
            $data['city'] = $questionary->City;
        }
        
        // страховка
        if ( $questionary->hasinshurancecard )
        {
            $attributes[] = array('name'=>'inshurance', 'label'=>'', 'type' => 'html');
            $data['inshurance'] = $this->widget('bootstrap.widgets.TbLabel', array(
                'type'  => 'default',
                'label' => QuestionaryModule::t('has_medical_inshurance'),
            ), true);
        }
        
        $content .= $this->widget('bootstrap.widgets.TbDetailView', array(
            'data'       => $data,
            'attributes' => $attributes), true);
        
        // запрещаем случайную индексацию условий съемок
        return '<noindex>'.$content.'</noindex>';
    }
    
    /**
     * Получить одну строку с информацией об условиях участия в съемках
     * @param string $field
     * @param string $label
     * @param string $caption - всплывающая подсказка
     * @param string $textYes
     * @param string $textNo
     * @return array
     */
    protected function getConditionRow($field, $label, $caption='', $textYes='', $textNo='')
    {
        if ( ! $conditions = $this->questionary->recordingconditions )
        {
            return array('hasforeignpassport', '');
        }
        $data = '';
        $htmlOptions = array();
        
        if ( ! $textYes )
        {
            $textYes = Yii::t('coreMessages', 'yes');
        }
        if ( ! $textNo )
        {
            $textNo = Yii::t('coreMessages','no');
        }
        
        
        $attribute = array('name'=>$field, 'label' => QuestionaryModule::t($label), 'type' => 'html');  
        if ( $conditions->$field )
        {
            $status = $textYes;
            $labelType = 'info';
            if ( $caption )
            {
                $htmlOptions['title'] = QuestionaryModule::t($caption.'_enabled');
            }
        }else
       {
            $status = $textNo;
            $labelType = 'default';
            if ( $caption )
            {
                $htmlOptions['title'] = QuestionaryModule::t($caption.'_disabled');
            }
        }
        
        $data = $this->widget('bootstrap.widgets.TbLabel', array(
            'type'  => $labelType,
            'label' => $status,
            'htmlOptions' => $htmlOptions,
        ), true);
        
        return array($attribute, $data);
    }
    
    /**
     * Отобразить вкладку с персональными данными
     * @todo когда-нибудь отобразить здесь паспортные данные
     * @todo дополнительно обработать данные encode
     * @todo сделать профили ссылками
     * 
     * @return string - html-содержимое вкладки
     */
    public function getPersonalTabContent()
    {
        if ( ! Yii::app()->user->checkAccess('Admin') )
        {// контакты видны только админам
            return false;
        }
        
        $content = '';
        $questionary = $this->questionary;
        $user        = $this->questionary->user;
        
        $attributes  = array();
        $data = array();
        
        // Заголовок
        $content .= '<h3>'.QuestionaryModule::t('contact_information').'</h3>';
        
        // Email
        if ( isset($user->email) AND $user->email )
        {
            $attributes[] = array('name'=>'email', 'label'=>QuestionaryModule::t('email_label'));
            $data['email'] = $user->email;
        }
        
        // Мобильный телефон
        if ( isset($questionary->mobilephone) AND $questionary->mobilephone )
        {
            $attributes[] = array('name'=>'mobilephone', 'label'=>QuestionaryModule::t('mobilephone_label'));
            $data['mobilephone'] = $questionary->mobilephone;
        }
        
        // Домашний
        if ( isset($questionary->homephone) AND $questionary->homephone )
        {
            $attributes[] = array('name'=>'homephone', 'label'=>QuestionaryModule::t('homephone_label'));
            $data['homephone'] = $questionary->homephone;
        }
        
        // Дополнительный
        if ( isset($questionary->addphone) AND $questionary->addphone )
        {
            $attributes[] = array('name'=>'addphone', 'label'=>QuestionaryModule::t('addphone_label'));
            $data['addphone'] = $questionary->addphone;
        }
        
        // профиль в контакте
        if ( isset($questionary->vkprofile) AND $questionary->vkprofile )
        {
            $attributes[] = array('name'=>'vkprofile', 'label'=>QuestionaryModule::t('vkprofile_label'));
            $data['vkprofile'] = $questionary->vkprofile;
        }
        
        // Профиль facebook
        if ( isset($questionary->fbprofile) AND $questionary->fbprofile )
        {
            $attributes[] = array('name'=>'fbprofile', 'label'=>QuestionaryModule::t('fbprofile_label'));
            $data['fbprofile'] = $questionary->fbprofile;
        }
        
        // Профиль в одноклассниках
        if ( isset($questionary->okprofile) AND $questionary->okprofile )
        {
            $attributes[] = array('name'=>'okprofile', 'label'=>QuestionaryModule::t('okprofile_label'));
            $data['okprofile'] = $questionary->okprofile;
        }
        
        $content .= $this->widget('bootstrap.widgets.TbDetailView', array(
            'data'       => $data,
            'attributes' => $attributes), true);
        
        // запрещаем случайную индексацию персональных данных
        return '<noindex>'.$content.'</noindex>';
    }
    
    /**
     * Получить содержимое вкладки "мои приглашения"
     * 
     * @return string
     */
    protected function getInvitesTabContent()
    {
        if ( ! $this->isMyQuestionary() AND ! Yii::app()->user->checkAccess('Admin') )
        {// приглашения показываются только в своей анкете
            return '';
        }
        $content = '';
        $content .= '<h3>'.QuestionaryModule::t('userinfo_section_invites').'</h3>';
        
        // За отображение приглашений на съемки отвечает отдельный виджет
        $content .= $this->widget('questionary.extensions.widgets.QUserInvites.QUserInvites', array(
            'questionary' => $this->questionary,
        ), true);
        
        return $content;
    }
    
    /**
     * Получить содержимое вкладки "мои заявки"
     * 
     * @return string
     */
    protected function getRequestsTabContent()
    {
        if ( ! $this->isMyQuestionary() AND ! Yii::app()->user->checkAccess('Admin') OR ! $this->questionary->requestsCount )
        {// заявки показываются только в своей анкете
            return false;
        }
        return $this->widget('questionary.extensions.widgets.QUserApplications.QUserApplications', array(
            'questionary' => $this->questionary,
        ), true);
    }
    
    /**
     * Получить содержимое вкладки "мои съемки"
     * 
     * @return string
     */
    protected function getEventsTabContent()
    {
        if ( ! $this->isMyQuestionary() AND ! Yii::app()->user->checkAccess('Admin') OR ! $this->questionary->upcomingEventsCount )
        {// съемки показываются только в своей анкете
            return false;
        }
        return $this->widget('questionary.extensions.widgets.QUserEvents.QUserEvents', array(
            'questionary' => $this->questionary,
        ), true);
    }
    
    /**
     * Определить, просматривает пользователь свою или чужую анкету
     * 
     * @return boolean
     */
    protected function isMyQuestionary()
    {
        if ( ! is_object($this->questionary->user) )
        {// защита на случай если что-то не так с пользователем анкеты
            return false;
        }
        if ( Yii::app()->user->checkAccess('User') AND Yii::app()->user->id == $this->questionary->user->id )
        {
            return true;
        }
        return false;
    }
}