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
     * @var Project
     */
    protected $project;
    /**
     * @var ProjectEvent
     */
    protected $event;
    /**
     * @var EventVacancy[]
     */
    protected $vacancies;
    /**
     * @var User - руководитель проекта
     */
    protected $manager;
    /**
     * @var bool
     */
    protected $addContacts = false;
    
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
        $this->event     = ProjectEvent::model()->findByPk($data['event']->id);
        $this->project   = Project::model()->findByPk($this->event->projectid);
        if ( $manager = User::model()->findByPk($this->callList->authorid) )
        {
            $this->manager = $manager;
            $this->mailOptions['contactPhone'] = $manager->questionary->mobilephone;
            $this->mailOptions['contactEmail'] = $manager->email;
        }
        $this->clearVacancies($data['vacancies']);
        
        //CVarDumper::dump($this->vacancies, 3, true);
    }
    
    /**
     * @see EMailBase::run()
     */
    public function run()
    {
        $this->addSegment($this->getHeaderBlock());
        // информация о проекте
        $this->addSegment($this->getProjectBlock());
        // список актеров для каждой роли
        $this->addVacancies();
        // выводим виджет со всеми данными
        parent::run();
    }
    
    /**
     * 
     * @return string
     */
    protected function getHeaderBlock()
    {
        $block = $this->textBlock('', 'Фотовызывной');
        $block['headerAlign']    = 'center';
        $block['addHeaderRuler'] = true;
        return $block;
    }
    
    /**
     * Получить блок письма с информацией о проекте
     * @return string
     */
    protected function getProjectBlock()
    {
        $block = array();
        
        $block['type']         = 'imageLeft';
        $block['imageStyle']   = 'border:3px solid #c3c3c3;border-radius:10px;height:150px;width:150px;margin-top:5px;';
        $block['imageLink']    = $this->project->getAvatarUrl();
        $block['text']         = $this->getProjectDescription();
        $block['addTextRuler'] = true;
        
        return $block;
    }
    
    /**
     * 
     * @return string
     */
    protected function getProjectDescription()
    {
        $result = '';
        
        $result .= '<b>Проект:</b> '.$this->project->name.'<br>';
        $result .= '<b>Мероприятие:</b> '.$this->event->name.'<br>';
        $result .= '<b>Дата и время:</b> '.$this->event->getFormattedTimePeriod().'<br>';
        $result .= '<b>Руководитель проекта:</b> '.$this->getManagerName().'<br>';
        $result .= '<b>Контактный телефон:</b> '.$this->getManagerPhone().'<br>';
        
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
        $vacancyInfo['type']           = 'subHeader';
        $vacancyInfo['header']         = 'Роль: '.$vacancy['name'];
        $vacancyInfo['headerInfo']     = $this->getVacancyTimePeriod($vacancy);
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
        
        $block['type']         = 'imageLeft';
        $block['imageStyle']   = 'border:3px solid #c3c3c3;border-radius:10px;height:150px;width:150px;margin-top:5px;';
        $block['imageLink']    = $questionary->getAvatarUrl('catalog');
        $block['text']         = $this->getActorDescription($questionary);
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
        
        $result .= '<h3>'.$questionary->fullname.'</h3>';
        $result .= 'Возраст: '.$questionary->age.'<br>';
        if ( ! empty($bages) )
        {
            $result .= 'Квалификация: <i>'.implode(', ', $bages).'</i><br>';
        }
        if ( $this->addContacts )
        {
            $result .= '<br/>';
            $result .= 'Телефон (моб.): '.$questionary->mobilephone.'<br>';
            if ( $questionary->homephone )
            {
                $result .= 'Телефон (дом.):'.$questionary->mobilephone.'<br>';
            }
            $result .= 'email: '.$questionary->user->email.'<br>';
            
        }
        $result .= '<b>Для заметок:</b> <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>';
        
        return $result;
    }
    
    /**
     * 
     * @return void
     */
    protected function addManagerInfo()
    {
        $block = array();
        
        $block['type']         = 'imageLeft';
        $block['imageStyle']   = 'border:3px solid #c3c3c3;border-radius:50px;height:150px;width:150px;margin-top:5px;';
        $block['imageLink']    = $this->manager->questionary->getAvatarUrl('catalog');
        $block['text']         = $this->getManagerDescription($this->manager->questionary);
        $block['addTextRuler'] = true;
        
        $this->addSegment($block);
    }
    
    /**
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
                $vacancyInfo = array('name' => $name, 'members' => $members);
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
        return $this->manager->questionary->firstname.' '.$this->manager->questionary->lastname;
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
        
        
        return 'с '.$start.' до '.$end;
    }
}