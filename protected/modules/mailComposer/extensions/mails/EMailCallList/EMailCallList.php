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
     *
     * @var RCallList - сохраненные данные фотовызывного
     */
    public $callList;
    /**
     *
     * @var bool
     */
    public $addContacts = false;
    /**
     *
     * @var Project
     */
    protected $project;
    /**
     *
     * @var ProjectEvent
     */
    protected $event;
    /**
     *
     * @var EventVacancy[]
     */
    protected $vacancies;
    /**
     *
     * @var User - руководитель проекта
     */
    protected $manager;

    /**
     *
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
        $this->event = ProjectEvent::model()->findByPk($data['event']->id);
        $this->project = Project::model()->findByPk($this->event->projectid);
        if ( $this->project->leader )
        {
            $this->manager = $this->project->leader;
            $this->mailOptions['contactPhone'] = $this->manager->questionary->mobilephone;
            $this->mailOptions['contactEmail'] = $this->manager->email;
            $this->mailOptions['manager'] = $this->manager;
            $this->mailOptions['showTopServiceLinks'] = true;
            $this->mailOptions['topBarOptions']['displayWebView'] = true;
            $this->mailOptions['topBarOptions']['webViewLink'] = $this->getWebViewLink();
        }
        // убираем дубли вакансий
        $this->clearVacancies($data['vacancies']);
        
        // CVarDumper::dump($this->vacancies, 3, true);
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
        $block = $this->textBlock('', 'ФОТОВЫЗЫВНОЙ');
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
        
        $block['type'] = 'imageLeft';
        $block['imageStyle'] = 'border:3px solid #c3c3c3;border-radius:10px;height:150px;width:150px;margin-top:5px;';
        $block['imageLink'] = $this->project->getAvatarUrl();
        $block['text'] = $this->getProjectDescription();
        $block['addCutRuler'] = true;
        
        return $block;
    }

    /**
     * Получить описание проекта
     * @return string
     */
    protected function getProjectDescription()
    {
        $result = '<div style="font-size:16px;line-height:23px;">';
        $result .= '<b>Проект:</b> ' . $this->project->name . '<br>';
        $result .= '<b>Мероприятие:</b> ' . $this->event->name . '<br>';
        $result .= '<b>Дата и время:</b> ' . $this->event->getFormattedTimePeriod() . '<br>';
        $result .= '</div>';
        
        $result .= '<hr>';
        $result .= '<b>Руководитель проекта:</b> ' . $this->getManagerName() . '<br>';
        $result .= '<b>Контактный телефон:</b> ' . $this->getManagerPhone() . '<br>';
        
        return $result;
    }

    /**
     *
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
            'type' => 'cutRuler'));
    }

    /**
     *
     * @param EventVacancy $vacancy
     * @return void
     */
    protected function addVacancy($vacancy)
    {
        // добавляем информацию о роли
        $vacancyInfo = array();
        $vacancyInfo['type'] = 'subHeader';
        $vacancyInfo['header'] = 'Роль: ' . $vacancy['name'];
        $vacancyInfo['headerInfo'] = $this->getVacancyTimePeriod($vacancy);
        $vacancyInfo['addHeaderRuler'] = true;
        $this->addSegment($vacancyInfo);
        
        // добавляем всех участников роли
        foreach ( $vacancy['members'] as $qid => $member )
        {
            $questionary = Questionary::model()->findByPk($qid);
            $this->addActor($questionary);
        }
    }

    /**
     *
     * @param Questionary $questionary
     * @return void
     */
    protected function addActor($questionary)
    {
        $block = array();
        
        $block['type'] = 'imageLeft';
        $block['imageStyle'] = 'border:3px solid #c3c3c3;border-radius:10px;height:150px;width:150px;margin-top:5px;';
        $block['imageLink'] = $questionary->getAvatarUrl('catalog');
        $block['text'] = $this->getActorDescription($questionary);
        $block['addTextRuler'] = true;
        
        $this->addSegment($block);
    }

    /**
     *
     * @param Questionary $questionary
     * @return string
     */
    protected function getActorDescription($questionary)
    {
        $result = '';
        $bages = $questionary->getBages();
        
        $result .= '<h3 style="text-transform:uppercase;font-size:20px;font-weight:bold;color:#727272;margin:11px 0px 6px 0px;">' . $questionary->fullname . ', ' . $questionary->age . '</h3>';
        if ( !empty($bages) )
        {
            $result .= 'Квалификация: <i>' . implode(', ', $bages) . '</i><br>';
        }
        if ( $this->addContacts )
        {
            $result .= '<br/>';
            $result .= 'Телефон (моб.): ' . $questionary->mobilephone . '<br>';
            if ( $questionary->homephone )
            {
                $result .= 'Телефон (дом.):' . $questionary->mobilephone . '<br>';
            }
            $result .= 'email: ' . $questionary->user->email . '<br>';
        
        }
        $result .= $this->getNotesField();
        
        return $result;
    }

    /**
     *
     * @return void
     */
    protected function addManagerInfo()
    {
        $block = array();
        
        $block['type'] = 'imageLeft';
        $block['imageStyle'] = 'border:3px solid #c3c3c3;border-radius:75px;height:150px;width:150px;margin-top:5px;';
        $block['imageLink'] = $this->manager->questionary->getAvatarUrl('catalog');
        $block['text'] = $this->getManagerDescription();
        
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
        $result .= '<h3>' . $this->manager->questionary->fullname . '</h3>';
        $result .= '<span>Руководитель проектов</span><br>';
        $result .= '<span style="display:block;">' . $this->getManagerPhone() . ' | ' . $this->manager->email . '</span>';
        if ( $this->manager->questionary->fbprofile )
        {
            // $fbIcon = '<img src="'. Yii::app()->createAbsoluteUrl('/') .'/images/facebook_icon.png">&nbsp;&nbsp;&nbsp;';
            $fbIcon = '';
            $fbLink = CHtml::link($fbIcon . $this->manager->questionary->fbprofile, $this->manager->questionary->fbprofile, array(
                'target' => '_blank'));
            $result .= '<span style="display:block;width:100%;">' . $fbLink . '</span>';
        }
        $result .= '<span style="display:block;text-align:center;"><b>+7 495 227-5-226</b></span>';
        $result .= '<span style="width:100%;text-align:right;"><img style="float:right;width:220px;" src="' . Yii::app()->createAbsoluteUrl('/') . '/images/24-7-365.png" width="220"></span>';
        
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
        foreach ( $vacancies as $item )
        {
            $vacancy = $item['vacancy'];
            $members = $item['members'];
            
            $name = trim(mb_ereg_replace('[0-9 ]{1-3}:[0-9 ]{1-3}', '', $vacancy->name));
            
            if ( isset($this->vacancies[$name]) )
            {
                $this->vacancies[$name]['members'] = CMap::mergeArray($this->vacancies[$name]['members'], $members);
            }else
            {
                $vacancyInfo = array(
                    'name' => $name, 
                    'members' => $members);
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
        return $this->manager->questionary->firstname . ' ' . $this->manager->questionary->lastname;
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
            'id' => $this->callList->id, 
            'key' => $this->callList->key, 
            'style' => 'color:#ffffff;font-weight:bold;'));
        return CHtml::link('Версия для печати', $url, array(
            'target' => '_blank'));
    }

    /**
     *
     * @param EventVacancy $vacancy
     * @return string
     */
    protected function getVacancyTimePeriod($vacancy)
    {
        $start = $this->event->timestart;
        $end = $this->event->timeend;
        $start = Yii::app()->getDateFormatter()->format('HH:mm', $start);
        $end = Yii::app()->getDateFormatter()->format('HH:mm', $end);
        
        return 'с ' . $start . ' до ' . $end;
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