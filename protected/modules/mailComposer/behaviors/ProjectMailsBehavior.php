<?php

/**
 * В этот класс вынесены все функции создания писем для модуля "projects"
 * 
 * @todo языковые строки
 */
class ProjectMailsBehavior extends CBehavior
{
    /**
     * Составить тему письма для приглашения на съемки
     *
     * @param EventInvite $invite - приглашение на мероприятие
     * @return string
     *
     * @todo проверить Chtml::encode()
     */
    public function createNewInviteMailSubject($invite)
    {
        // название проекта и мероприятия
        $projectName = $invite->event->project->name;
        $eventName   = $invite->event->name;
         
        // дата и время начала мероприятия
        $startDate = $invite->event->getFormattedTimeStart();
         
        // дата и время или название мероприятия (если время не указано)
        $eventInfo = $eventName;
        if ( $startDate AND ! $invite->event->nodates )
        {// если мероприятие создано на конкретную дату - отобразим ее
            $eventInfo = $startDate;
        }
         
        $subject = 'Приглашение на съемки в проекте "'.$projectName.'" ('.$eventInfo.')';
        return $subject;
    }
    
    /**
     * Получить текст письма с приглашением на съемки
     * @param EventInvite $invite - приглашение на мероприятие
     * @param array $mailOptions - дополнительные настройки для создания письма
     * @return string
     * 
     * @todo предлагать посмотреть другие приглашения только если они реально есть
     * @todo добавить отображение лого проекта
     */
    public function createNewInviteMailText($invite, $mailOptions=array())
    {
        $segments    = new CMap();
        $mailOptions = $this->getInviteMailOptions($invite, $mailOptions);
        
        // Создаем текст письма: приветствие и что вообще произошло (только текст, без заголовка)
        $greetingBlock = $this->createNewInviteGreetingBlock($invite);
        $segments->add(null, $greetingBlock);
        
        // о проекте (если о нем есть информация)
        if ( $projectInfoBlock = $this->createProjectInfoBlock($invite) )
        {
            $segments->add(null, $projectInfoBlock);
        }
        
        $middleBlock = array();
        if ( $invite->event->type == 'group' )
        {
            $middleBlock['text'] = 'Информация о предстоящем мероприятии:';
        }else
        {
            $middleBlock['text'] = 'Мероприятия будут проходить в следующие дни:';
        }
        $segments->add(null, $middleBlock);
        
        // список мероприятий и вакансий
        if ( $invite->event->type == 'group' )
        {// группа мероприятий - выведем информацию о каждом (несколько блоков)
            $groupBlocks = $this->createGroupEventDescription($invite->event);
            $segments->mergeWith($groupBlocks);
        }else
        {// одно мероприятие - информация о нем помещается в один блок
            $eventBlock = $this->createSingleEventDescription($invite->event);
            $segments->add(null, $eventBlock);
        }
        
        // внизу информация обо всех доступных вакансиях
        if ( $vacancyBlocks = $this->createVacancyBlocks($invite) )
        {
            $segments->mergeWith($vacancyBlocks);
        }
        
        // послесловие и ссылка на подачу заявки
        $endingBlock = $this->createNewInviteEndingBlock($invite);
        $segments->add(null, $endingBlock);
        
        // добавляем все блоки с информацией в массив настроек для виджета EMailAssembler
        $mailOptions['segments'] = $segments;
        // создаем виджет и получаем из него полный HTML-код письма
        return $this->owner->widget('application.modules.mailComposer.extensions.widgets.EMailAssembler.EMailAssembler',
            $mailOptions, true); 
    }
    
    /**
     * Получить текст письма с уведомлением о том, что заявка участника принята
     * @param ProjectMember $projectMember - данные заявки участника
     * @return string
     *
     * @todo добавить обработку поля "дополнительная информация для участников"
     * @todo писать, на какую именно роль выбрали
     */
    public function createApproveMemberMailText($projectMember, $mailOptions=array())
    {
        $segments    = new CMap();
        $mailOptions = $this->getApproveMailOptions($projectMember, $mailOptions);
        $event       = $projectMember->vacancy->event;
        
        // блок с текстом приветствия
        $greetingBlock = $this->createApproveGreetingBlock($projectMember);
        $segments->add(null, $greetingBlock);
        
        // блок с напоминанием и датами съемок
        if ( $event->type == 'group' )
        {// группа мероприятий - выведем информацию о каждом (несколько блоков)
            $groupBlocks = $this->createGroupEventDescription($projectMember->vacancy->event);
            $segments->mergeWith($groupBlocks);
        }else
        {// одно мероприятие - информация о нем помещается в один блок
            $eventBlock = $this->createSingleEventDescription($projectMember->vacancy->event);
            $segments->add(null, $eventBlock);
        }
        // заключение
        $segments->add(null, array('text' => 'Ждем вас.'."<br>\n"));
         
        // добавляем все блоки с информацией в массив настроек для виджета EMailAssembler
        $mailOptions['segments'] = $segments;
        // создаем виджет и получаем из него полный HTML-код письма
        return $this->owner->widget('application.modules.mailComposer.extensions.widgets.EMailAssembler.EMailAssembler',
            $mailOptions, true);
    }
    
    /**
     * Получить текст письма с уведомлением о том, что заявка участника отклонена
     * @param ProjectMember $projectMember - данные заявки участника
     * @return string
     *
     * @todo указывать причины отказа
     */
    public function createRejectMemberMailText($projectMember, $mailOptions=array())
    {
        $segments    = new CMap();
        $mailOptions = $this->getRejectMailOptions($projectMember, $mailOptions);
        $projectName = $projectMember->vacancy->event->project->name;
        
        // письмо состоит из одного блока
        $block = array();
        $block['text'] = $this->createUserGreeting($projectMember->member);
        $block['text'] .= 'Некоторое время назад вы подавали заявку на участие в проекте "'.
            $projectName.'", на роль "'.$projectMember->vacancy->name.'".'."<br>\n";
        $block['text'] .= 'Мы передали вашу заявку режиссеру, она рассмотрена и отклонена.'."<br>\n";
        $segments->add(null, $block);
        
        //$message .= 'Необходимое количество человек для мероприятия "'.$eventName.'" уже набрано.'."<br>\n<br>\n";
        //$message .= 'Если у нас появятся другие предложения, то мы обязательно сообщим вам о них.';
         
        // добавляем все блоки с информацией в массив настроек для виджета EMailAssembler
        $mailOptions['segments'] = $segments;
        // создаем виджет и получаем из него полный HTML-код письма
        return $this->owner->widget('application.modules.mailComposer.extensions.widgets.EMailAssembler.EMailAssembler',
            $mailOptions, true);
    }
    
    /**
     * Получить массив для создания раздела письма с приветствием и информацией о том что вообще происходит
     * @param EventInvite $invite - приглашение участника
     *
     * @return array
     */
    protected function createNewInviteGreetingBlock($invite)
    {
        $block = array();
        // ссылка на проект
        $projectUrl = Yii::app()->createAbsoluteUrl('/projects/projects/view',
            array('id' => $invite->event->project->id));
        $projectLink   = CHtml::link($invite->event->project->name, $projectUrl, array('target' => '_blank'));
        
        $block['text'] = $this->createUserGreeting($invite->questionary);
        $block['text'] .= 'Приглашаем вас принять участие в проекте "'.$projectLink.'"'."<br>\n";
        
        return $block;
    }
    
    /**
     * Получить массив для создания раздела письма с информацией о проекте
     * @param EventInvite $invite - приглашение участника
     * 
     * @return array|bool
     */
    protected function createProjectInfoBlock($invite)
    {
        $block   = array('header' => 'О проекте');
        $project = $invite->event->project;
        // получаем ссылку на проект
        $projectUrl = Yii::app()->createAbsoluteUrl('/projects/projects/view',
            array('id' => $invite->event->project->id));
        $projectLink = CHtml::link($project->name, $projectUrl, array('target' => '_blank'));
        
        if ( false )
        {// @todo заглушка: если у проекта есть логотип - меняем тип верстки блока, чтобы отобразить его
            $block['type'] = 'imageLeft'; 
            $block['imageLink'] = '';
        }
        // описание проекта
        if ( trim($project->description) )
        {
            $block['text'] = $project->description;
        }elseif ( trim($project->customerdescription) )
        {
            $block['text'] = $project->customerdescription;
        }elseif ( trim($project->shortdescription) )
        {
            $block['text'] = $project->shortdescription;
        }else
        {// у проекта вообще нет описания - ничего не выводим
            return;
        }
        
        return $block;
    }
    
    /**
     * Получить информацию об отдельном мероприятии проекта
     * @param ProjectEvent $event - отображаемое мероприятие
     * @return array
     * 
     * @todo если съемки заканчиваются на следующий день - добавлять дату ко времени окончания
     * @todo вернуть ссылку на мероприятие
     */
    protected function createSingleEventDescription($event)
    {
        $block = array();
        
        if ( $event->nodates )
        {// мероприятие без определенной даты - не выводим дату и время начала
            $dateInfo = $event->getFormattedTimeStart();
        }else
        {// дата мероприятия известна - выводим ее в отформатированном виде
            $startDate = $event->getFormattedTimeStart();
            $endDate   = Yii::app()->getDateFormatter()->format('HH:mm', $event->timeend);
            $dateInfo  = $startDate." - ".$endDate;
        }
        // создаем ссылку на просмотр мероприятия
        $eventUrl  = Yii::app()->createAbsoluteUrl('/projects/projects/view', array('eventid' => $event->id));
        $eventLink = CHtml::link($event->name, $eventUrl, array('target' => '_blank'));
        
        $block['header'] = $dateInfo;
        //$block['text']   = 'Что планируется: '.$eventLink;
        $block['text']   = 'Что планируется: '.$event->name.'<br>';
                 
        if ( trim($event->description) )
        {// описание мероприятия
            $block['text'] .= 'Подробности: '.$event->description;
        }
         
        return $block;
    }
    
    /**
     * Получить информацию о группе мероприятий
     * @param ProjectEvent $group - отображаемая группа мероприятий
     * @return array
     */
    protected function createGroupEventDescription($group)
    {
        $blocks = array();
        
        foreach ( $group->events as $event )
        {// отображаем все дни(мероприятия) которые включены в группу.
            // одно мероприятие - один блок
            $blocks[] = $this->createOneGroupEventDescription($event);
        }
        
        return $blocks;
    }
    
    /**
     * Получить описание одного мероприятия из группы
     * Если на этот отдельный день для участника есть еще доступные вакансии - 
     * то они не выводятся здесь, а приходят отдельным письмом
     * 
     * @param ProjectEvent $event - отображаемое мероприятие
     * @return string
     * 
     * @todo вернуть ссылку на мероприятие
     */
    protected function createOneGroupEventDescription($event)
    {
        $block = array();
         
        $startDate = $event->getFormattedTimeStart();
        $endDate   = Yii::app()->getDateFormatter()->format('HH:mm', $event->timeend);
        //$eventUrl  = Yii::app()->createAbsoluteUrl('/projects/projects/view', array('eventid' => $event->id));
        //$eventLink = CHtml::link($event->name, $eventUrl, array('target' => '_blank'));
         
        $block['header'] = $startDate.' - '.$endDate;
        $block['text'] = '<b>'.$event->name.'</b><br>';
        
        if ( trim($event->description) )
        {
            $block['text'] .= 'Дополнительная информация: '.$event->description;
        }
         
        return $message;
    }
    
    /**
     * Создать список вакансий, подходящих для участника с описанием каждой вакансии
     * @param EventInvite $event
     * @return array|null
     *
     * @todo переписать механизм получения списка подходящих вакансий (брать из базы)
     */
    protected function createVacancyBlocks($invite)
    {
        $blocks = array();
         
        if ( ! $vacancies = $invite->event->getAllowedVacancies($invite->questionaryid) )
        {// нет ни одной подходящий участнику вакансии - ничего не выводим
            return;
        }
        
        $infoBlock = array();
        $infoBlock['header'] = 'Предлагаемые роли';
        $infoBlock['text']   = 'Здесь перечислены все роли, на которые вы можете подать заявку.<br> ';
        $infoBlock['text']  .= 'Система выбирает их, основываясь на данных вашей анкеты.<br> ';
        $blocks[] = $infoBlock;
        
        foreach ( $vacancies as $vacancy )
        {
            $vacancyBlock = array();
            $vacancyBlock['header'] = $vacancy->name;
            $vacancyBlock['text'] = "Описание: ".$vacancy->description.'<br>';
            if ( $vacancy->salary )
            {
                $vacancyBlock['text'] .= "Оплата (за съемочный день): {$vacancy->salary} р.";
            }
            $blocks[] = $vacancyBlock;
        }
         
        return $blocks;
    }
    
    /**
     * Получить последний блок письма с приглашением на съемки - с предложением и кнопкой подачи заявки
     * @param EventInvite $event
     * @return array
     * 
     * @todo вернуть ссылку на список приглашений в анкете
     */
    protected function createNewInviteEndingBlock($invite)
    {
        $block  = array();
        $button = array();
        
        // ссылка на список приглашений в профиле
        $invitesUrl = Yii::app()->createAbsoluteUrl(Yii::app()->getModule('questionary')->profileUrl,
            array('id' => $invite->questionary->id, 'activeTab' => 'invites'));
        $invitesLink   = CHtml::link('Мои приглашения', $invitesUrl, array('target' => '_blank'));
        // ссылка на подачу заявки по токену
        $subscribeUrl = Yii::app()->createAbsoluteUrl('/projects/invite/subscribe',
            array('id' => $invite->id, 'key' => $invite->subscribekey));
        
        // текст приглашения
        $block['text'] = "Если хотите принять участие - то для этого достаточно подать заявку, 
            кликнув по этой кнопке:<br>\n";
        $block['text'] .= '<small>(На открывшейся странице можно будет выбрать роль)</small>';
        /*$block['text'] .= 'Просмотреть, другие приглашения вы можете
	        на своей странице, в разделе "'.$invitesLink.'".'."<br>\n";*/
        // кнопка с приглашением
        $button['caption'] = 'Подать заявку';
        $button['link']    = $subscribeUrl;
        // добавляем кнопку в блок
        $block['button'] = $button;
        
        return $block;
    }
    
    /**
     * Получить из стандартного набора настроек письма и переданных извне параметров один массив настроек
     * для письма с приглашением
     *
     * @param array $newOptions
     * @return array
     */
    protected function getInviteMailOptions($invite, $newOptions)
    {
        // задаем стандартные настройки отображения письма с приглашением
        $defaultMailOptions = array(
            // @todo после рассылки для НТВ вернуть отображение телефона в положение true
            'showContactPhone'         => false,
            'showContactEmail'         => true,
            // @todo подставлять сюда контакты руководителя проекта
            //'contactPhone'           => Yii::app()->params['adminPhone'],
            //'contactEmail'           => Yii::app()->params['adminEmail'],
            // главный большой заголовок всего письма
            'mainHeader'               => 'Приглашение на съемки',
            // добавляем подпись о том, почему ему пришла рассылка - он подходит по критериям
            'signature'                => $this->createInviteSignature(),
            // всегда сообщаем как с нами связаться
            'showFeedbackNotification' => true,
            // @todo пока что напоминаем про пароль всем. В конце сентябьря уберем и сделаем еще одноразовых линков если захотим.
            'showPasswordNotification' => false,
            // определяем, зашел ли участник на сайт хотя бы раз.
            // Если нет - то EMailAssembler напомнит ему о том что пароль можно восстановить
            // если он его забыл или не получил при регистрации
            'userHasFirstAccess'       => ($invite->questionary->user->getLastvisit() != 0),
        );
    
        if ( is_array($newOptions) AND ! empty($newOptions) )
        {// меняем какие-то настройки извне, если нужно
            return CMap::mergeArray($defaultMailOptions, $newOptions);
        }else
        {// ничего не меняем, оставляем все как есть
            return $defaultMailOptions;
        }
    }
    
    /**
     * Получить из массив настроек для письма с одобрением заявки
     *
     * @param ProjectMember $projectMember - данные заявки участника
     * @param array $newOptions
     * @return array
     */
    protected function getApproveMailOptions($projectMember, $newOptions)
    {
        // задаем стандартные настройки отображения письма
        $defaultMailOptions = $this->owner->getMailDefaults();
        $defaultMailOptions['mainHeader'] = 'Ваша заявка принята';
    
        if ( is_array($newOptions) AND ! empty($newOptions) )
        {// меняем какие-то настройки извне, если нужно
            return CMap::mergeArray($defaultMailOptions, $newOptions);
        }else
        {// ничего не меняем, оставляем все как есть
            return $defaultMailOptions;
        }
    }
    
    /**
     * Получить из массив настроек для письма с отклонением заявки
     *
     * @param ProjectMember $projectMember - данные заявки участника
     * @param array $newOptions
     * @return array
     */
    protected function getRejectMailOptions($projectMember, $newOptions)
    {
        // задаем стандартные настройки отображения письма
        $defaultMailOptions = $this->owner->getMailDefaults();
        $defaultMailOptions['mainHeader'] = '';
    
        if ( is_array($newOptions) AND ! empty($newOptions) )
        {// меняем какие-то настройки извне, если нужно
            return CMap::mergeArray($defaultMailOptions, $newOptions);
        }else
        {// ничего не меняем, оставляем все как есть
            return $defaultMailOptions;
        }
    }
    
    /**
     * Получить пояснение о том, что рассылка пришла из-за того, что данные анкеты подходят выборке 
     * (для подписи внизу)
     * @return string
     */
    protected function createInviteSignature()
    {
        return 'Вы получили это приглашение потому что ваши анкетные данные 
            соответствуют критериям отбора на роли для предстоящих съемок.';
    }
    
    /**
     * Получить первый блок письма о принятии заявки (вступление) 
     * @param ProjectMember $projectMember - данные заявки участника
     * @return array
     */
    protected function createApproveGreetingBlock($projectMember)
    {
        $block = array();
        $event = $projectMember->vacancy->event;
        
        $block['text'] = $this->createUserGreeting($projectMember->member);
        $block['text'] .= 'Ваша заявка была направлена режиссеру, рассмотрена и подтверждена.'."<br>\n";
        $block['text'] .= 'Теперь вы участник проекта "'.$event->project->name.'".'."<br>\n";
        $block['text'] .= 'Роль, на которую вы утверждены: "'.$projectMember->vacancy->name."\"<br>\n";
        
        if ( $event->type == 'group' )
        {
            $block['text'] .= "<br>\n".'Напоминаем, что съемки будут проходить в следующие дни:';
        }else
        {
            $block['text'] .= "<br>\n".'Напоминаем вам информацию о мероприятии:';
        }
        
        return $block;
    }
    
    /**
     * Получить строку с приветствием для участника
     * @param Questionary $questionary
     * @return string
     */
    protected function createUserGreeting($questionary)
    {
        if ( is_object($questionary) AND trim($questionary->firstname) )
        {
            return $questionary->firstname.", здравствуйте.<br>\n<br>\n";
        }else
        {
            return "Добрый день.<br>\n<br>\n";
        }
    }
}