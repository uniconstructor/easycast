<?php

// подключаем базовый класс для шаблонов писем
Yii::import('application.modules.mailComposer.extensions.mails.EMailBase.EMailBase');

/**
 * Фотовызывной лист
 *
 * @todo документировать все параметры и функции
 */
class EMailCallList extends EMailBase
{
    /**
     * @var RCallList - сохраненные данные фотовызывного
     */
    public $callList;
    /**
     * @var bool - добавлять ли контактную информацию в вызывной лист?
     */
    public $addContacts = false;
    
    /**
     * @var Project
     */
    protected $project;
    /**
     * @var ProjectEvent
     */
    protected $event;
    /**
     * @var EventVacancy[] - список ролей в событии
     */
    protected $vacancies;
    /**
     * @var User - руководитель проекта
     */
    protected $manager;
    /**
     * @var string - язык на котором формируется фотовызывной
     */
    protected $language    = 'ru';
    /**
     * @var array - массив с дополнительными строками для перевода тех частей 
     *              фотовызывного, которые нельзя перевести автоматически 
     *              (названия ролей, название проекта, название события)
     *              Структура массива
     *              array(
     *                  'event'   => 'Название мероприятия',
     *                  'project' => 'Название проекта',
     *                  'vacancy' => array(
     *                      '55' => 'Название роли id=55',
     *                      '83' => 'Название роли id=83s',
     *                      ...
     *                  ),
     *              )
     */
    protected $translation = array();

    /**
     * @see EMailBase::init()
     */
    public function init()
    {
        Yii::import('ext.ESearchScopes.behaviors.*');
        Yii::import('ext.ESearchScopes.models.*');
        Yii::import('ext.ESearchScopes.*');
        Yii::import('ext.galleryManager.*');
        
        parent::init();
        
        $data = $this->callList->getData();
        $this->event   = ProjectEvent::model()->findByPk($data['event']->id);
        $this->project = Project::model()->findByPk($this->event->projectid);
        
        if ( isset($data['language']) )
        {// язык на котором следует составить фотовызывной
            $this->language = $data['language'];
            Yii::app()->setLanguage($this->language);
        }
        if ( isset($data['translation']) AND is_array($data['translation']) )
        {// если фотовызывной должен быть сформирован на иностранном языке - то обычно он содержит 
            // дополнительные строки перевода, которые нельзя сформировать автоматически
            $this->translation = $data['translation'];
        }
        //CVarDumper::dump($data, 10, true);die;
        if ( $this->project->leader )
        {// узнаем и получаем контакты руководителя проекта
            $this->manager = $this->project->leader;
            // телефон и email: отображаются внизу письма, в подписи
            $this->mailOptions['contactPhone'] = $this->manager->questionary->mobilephone;
            $this->mailOptions['contactEmail'] = $this->manager->email;
            // руководитель проекта для персонализации внизу письма
            $this->mailOptions['manager']      = $this->manager;
            // подставить шапку письма с телефоном для заказчика
            $this->mailOptions['target']       = 'customer';
            // показываем в шапке письма ссылку на веб-версию письма
            $this->mailOptions['showTopServiceLinks'] = true;
            $this->mailOptions['topBarOptions']['displayWebView'] = true;
            $this->mailOptions['topBarOptions']['webViewLink']    = $this->getWebViewLink();
        }
        
        // убираем дубли вакансий (ролей)
        // это нужно для тех случаев, когда несколько ролей ничем не отличаются друг от друга кроме
        // времени, к которому ожидаются люди
        // @todo удалить эту функцию после того как будет введено время для ролей (то есть когда можно будет
        //       в рамках одного дня назначать несколько ролей на разное время)
        $this->clearVacancies($data['vacancies']);
    }

    /**
     * @see EMailBase::run()
     */
    public function run()
    {
        // заголовок
        $this->addSegment($this->getHeaderBlock());
        // информация о проекте
        $this->addSegment($this->getProjectBlock());
        // список актеров для каждой роли
        $this->addVacancies();
        // информация о руководителе проекта
        $this->addManagerInfo();
        // выводим виджет со всеми данными
        parent::run();
    }

    /**
     * Получить блок письма с заголовком фотовызывного
     * @return string
     */
    protected function getHeaderBlock()
    {
        $block = $this->textBlock('', MailComposerModule::t('casting_list'));
        $block['headerAlign'] = 'center';
        $block['addCutRuler'] = true;
        
        return $block;
    }

    /**
     * Получить блок письма с информацией о проекте
     * @return string
     */
    protected function getProjectBlock()
    {
        $block = array();
        
        $block['type']        = 'imageLeft';
        $block['imageStyle']  = 'border:3px solid #c3c3c3;border-radius:10px;height:150px;width:150px;margin-top:5px;';
        $block['imageLink']   = $this->project->getAvatarUrl();
        $block['text']        = $this->getProjectDescription();
        $block['addCutRuler'] = true;
        // вся информация о пректе и мероприятии будет выведена черным цветом
        $block['textColor']   = '#000';
        
        return $block;
    }

    /**
     * Получить описание проекта
     * @return string
     * 
     * @todo вынести в отдельный виджет
     */
    protected function getProjectDescription()
    {
        $projectName = $this->project->name;
        if ( $this->language != 'ru' AND isset($this->translation['project']) )
        {
            $projectName = $this->translation['project'];
        }
        $eventName = $this->event->name;
        if ( $this->language != 'ru' AND isset($this->translation['event']) )
        {
            $eventName = $this->translation['event'];
        }
        
        $result  = '<div style="font-size:16px;line-height:23px;">';
        $result .= '<b>'.ProjectsModule::t('project').':</b> '.$projectName.'<br>';
        $result .= '<b>'.ProjectsModule::t('event').':</b> '.$eventName.'<br>';
        $result .= '<b>'.ProjectsModule::t('date_and_time').':</b> '.$this->event->getFormattedTimePeriod().'<br>';
        $result .= '</div>';
        
        $result .= '<hr>';
        $result .= '<b>'.ProjectsModule::t('project_manager').':</b> '.$this->getManagerName().'<br>';
        $result .= '<b>'.Yii::t('coreMessages', 'phone').':</b> '.$this->getManagerPhone().'<br>';
        
        return $result;
    }

    /**
     * Добавить в фотовызывной блоки со списком ролей
     * @return void
     */
    protected function addVacancies()
    {
        foreach ( $this->vacancies as $vacancy )
        {
            $this->addVacancy($vacancy);
        }
        // добавляем дополнительный разделитель после всего списка актеров
        $this->addSegment(array(
            'type' => 'cutRuler',
        ));
    }

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
            $this->addActor($questionary);
        }
    }

    /**
     * Добавить в письмо блок с фотографией и описанием одного актера
     * @param Questionary $questionary
     * @return void
     */
    protected function addActor($questionary)
    {
        $block = array();
        
        $block['type']         = 'imageLeft';
        $block['imageStyle']   = 'border:3px solid #c3c3c3;border-radius:10px;height:150px;width:150px;margin-top:5px;';
        $block['imageLink']    = $questionary->getAvatarUrl('catalog');
        $block['text']         = $this->getActorDescription($questionary);
        $block['addTextRuler'] = true;
        
        $this->addSegment($block);
    }

    /**
     * Описание одного актера
     * @param Questionary $questionary
     * @return string
     */
    protected function getActorDescription($questionary)
    {
        $result = '';
        $bages  = $questionary->getBages();
        
        $result .= '<h3 style="text-transform:uppercase;font-size:20px;font-weight:bold;color:#286B84;margin:11px 0px 6px 0px;">'.
            $questionary->fullname.', '.$questionary->age.'</h3>';
        if ( ! empty($bages) )
        {
            $result .= MailComposerModule::t('qualification').': <i>'.implode(', ', $bages).'</i><br>';
        }
        if ( $this->addContacts )
        {
            $result .= '<br/>';
            $result .= QuestionaryModule::t('mobilephone_label').': '.$questionary->mobilephone.'<br>';
            if ( $questionary->homephone )
            {
                $result .= QuestionaryModule::t('homephone_label').': '.$questionary->mobilephone.'<br>';
            }
            $result .= 'email: '.$questionary->user->email.'<br>';
        
        }
        $result .= $this->getNotesField();
        
        return $result;
    }

    /**
     * Добавить блок с информацией о менеджере
     * @return void
     */
    protected function addManagerInfo()
    {
        $block = array();
        
        $block['type']       = 'imageLeft';
        $block['imageStyle'] = 'border:3px solid #c3c3c3;border-radius:75px;height:150px;width:150px;margin-top:5px;';
        $block['imageLink']  = $this->manager->questionary->getAvatarUrl('catalog');
        $block['text']       = $this->getManagerDescription();
        
        $this->addSegment($block);
    }

    /**
     * Подпись с контактами менеджера внизу письма
     * 
     * @return string
     */
    protected function getManagerDescription()
    {
        $result = '';
        
        $result .= '<img style="padding-top:10px;" src="' . Yii::app()->createAbsoluteUrl('/') . '/images/i-take-care.png" width="100%">';
        $result .= '<h3>'.$this->manager->questionary->fullname.'</h3>';
        $result .= '<span>'.ProjectsModule::t('project_manager').'</span><br>';
        $result .= '<span style="display:block;">'.$this->getManagerPhone().' | '.$this->manager->email.' | +7(495)227-5-226</span>';
        
        if ( $this->manager->questionary->fbprofile )
        {
            // $fbIcon = '<img src="'. Yii::app()->createAbsoluteUrl('/') .'/images/facebook_icon.png">&nbsp;&nbsp;&nbsp;';
            $fbIcon = '';
            $fbLink = CHtml::link($fbIcon . $this->manager->questionary->fbprofile, $this->manager->questionary->fbprofile, array(
                'target' => '_blank'));
            $result .= '<span style="display:block;width:100%;">'.$fbLink.'</span>';
        }
        //$result .= '<span style="width:100%;text-align:right;"><img style="float:right;width:220px;" src="' . Yii::app()->createAbsoluteUrl('/') . '/images/24-7-365.png" width="220"></span>';
        
        return $result;
    }

    /**
     *
     * @param EventVacancy[] $vacancies
     * @return void
     */
    protected function clearVacancies($vacancies)
    {
        $this->vacancies = array();
        
        foreach ( $vacancies as $id => $item )
        {
            $vacancy     = $item['vacancy'];
            $members     = $item['members'];
            $translation = '';
            if ( isset($item['translation']) )
            {
                $translation = $item['translation'];
            }
            
            $name = trim(mb_ereg_replace('[0-9 ]{1-3}:[0-9 ]{1-3}', '', $vacancy->name));
            
            if ( isset($this->vacancies[$name]) )
            {
                $this->vacancies[$name]['members'] = CMap::mergeArray($this->vacancies[$name]['members'], $members);
            }else
            {
                $vacancyInfo = array(
                    'name'        => $name, 
                    'members'     => $members,
                    'translation' => $translation,
                );
                $this->vacancies[$name] = $vacancyInfo;
            }
            unset($members);
            unset($vacancy);
        }
        return $this->vacancies;
    }

    /**
     *
     * @return string
     */
    protected function getManagerPhone()
    {
        return $this->manager->questionary->mobilephone;
    }

    /**
     *
     * @return string
     */
    protected function getManagerName()
    {
        if ( $this->language != 'ru' )
        {
            return EcPurifier::translit($this->manager->questionary->firstname.' '.$this->manager->questionary->lastname);
        }else
        {
            return $this->manager->questionary->firstname.' '.$this->manager->questionary->lastname;
        }
    }

    /**
     * Получить ссылку на просмотр веб-версии письма на сайте
     * 
     * @return string
     */
    protected function getWebViewLink()
    {
        $url = Yii::app()->createAbsoluteUrl('/mailComposer/mail/display', array(
            'type' => 'callList', 
            'id'   => $this->callList->id, 
            'key'  => $this->callList->key,
        ));
        return CHtml::link('Версия для печати', $url, array(
            'target' => '_blank',
            'style'  => 'color:#fff;font-weight:bold;',
        ));
    }

    /**
     *
     * @param EventVacancy $vacancy
     * @return string
     */
    protected function getVacancyTimePeriod($vacancy)
    {
        $start = $this->event->timestart;
        $end   = $this->event->timeend;
        $start = Yii::app()->getDateFormatter()->format('HH:mm', $start);
        $end   = Yii::app()->getDateFormatter()->format('HH:mm', $end);
        
        return $start.'-'.$end;
    }

    /**
     * Получить html-код с пустым полем "для заметок"
     * 
     * @return void
     */
    protected function getNotesField()
    {
        return '<div style="width:400px;height:50px;background-color:#fefefe;border-radius:10px;
            border-color:#dddddd; border-width:1px; border-style:solid; margin: 5px 0px 0px 0px;">
                &nbsp;
            </div>';
    }
}