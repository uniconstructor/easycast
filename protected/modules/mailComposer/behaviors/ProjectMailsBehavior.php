<?php

/**
 * В этот класс вынесены все функции создания писем для модуля "projects"
 * 
 * @todo языковые строки
 * @todo перенести все стандартные функции (приветствие, и т. д.) 
 *       отсюда в базовый класс виджета письма (EMailBase)
 * @todo рефакторинг: изменить принцип формирования писем - вместо функций использовать виджеты.
 *       Один виджет - одно письмо. Все виджеты должны находиться в папке modules.mailComposer.extensions.mails
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
        
        // создаем текст письма: приветствие и что вообще произошло (только текст, без заголовка)
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
     * Получить текст письма с уведомлением о том, что заявка участника одобрена
     * @param ProjectMember $projectMember - данные заявки участника
     * @param array $mailOptions - дополнительные настройки для создания письма
     * @return string
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
            $groupBlocks = $this->createGroupEventDescription($projectMember->vacancy->event, true);
            $segments->mergeWith($groupBlocks);
        }else
        {// одно мероприятие - информация о нем помещается в один блок
            $eventBlock = $this->createSingleEventDescription($projectMember->vacancy->event, true);
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
     * @param array $mailOptions - дополнительные настройки для создания письма
     * @return string
     *
     * @todo указывать причины отказа
     */
    public function createRejectMemberMailText($projectMember, $mailOptions=array())
    {
        $segments    = new CMap();
        $mailOptions = $this->getRejectMailOptions($projectMember, $mailOptions);
        $projectName = $projectMember->vacancy->event->project->name;
        if ( $projectMember->vacancy->limit > 8 )
        {// это массовая роль, как правило она не художественная и на нее набирается множество
            // людей (например статисты или массовка)
            // @todo сделать более умное определение этого параметра (по критериям поиска) 
            $massRole = true;
        }else
        {// единичная роль
            $massRole = false;
        }
        
        // письмо состоит из одного блока
        $block = array();
        $block['text'] = $this->createUserGreeting($projectMember->member);
        $block['text'] .= 'Некоторое время назад вы подавали заявку на участие в проекте "'.
            $projectName.'", на роль "'.$projectMember->vacancy->name.'".'."<br>\n";
        $block['text'] .= "К сожалению она была отклонена. <br> 
            Возможные причины:<br>\n";
        $block['text'] .= "<ul>";
        $block['text'] .= "<li>Анкета одного из других кандидатов оказалась ближе к образу по мнению креативной группы кастинга.</li>";
        $block['text'] .= "<li>В вашей анкете недостаточно данных.
                Настоятельно рекомендуем вам следить за тем чтобы ваша анкета была максимально подробно заполнена.
                Обязательно размещайте свежие фото и видео, а также пополнять фильмографию.</li>";
        if ( $massRole )
        {
            $block['text'] .= "<li>Вы подали заявку слишком поздно и достаточное количество человек уже набрано.</li>";
        }
        $block['text'] .= "</ul>";
        if ( $projectMember->vacancy->id == 749 )
        {// @todo временно заменяем текст для кастинга СТС 
            $block['text'] = $this->getMCRejectionText($projectMember);
        }
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
     * FIXME заглушка на время проведения кастинга СТС
     * 
     * @return string
     */
    protected function getMCRejectionText($projectMember)
    {
        $block = array();
        $block['text'] = $this->createUserGreeting($projectMember->member);
        $block['text'] .= 'Некоторое время назад вы подавали заявку на участие в проекте "'.
            $projectName.'", на роль "'.$projectMember->vacancy->name.'".'."<br>\n";
        $block['text'] .= "К сожалению она была отклонена. <br>
            Возможные причины:<br>\n";
        $block['text'] .= "<ul>";
        $block['text'] .= "<li>Вы недостаточно разрернуто ответили на вопросы анкеты</li>";
        $block['text'] .= "<li>Ваше видео не отвечает указанным в форме требованиям или не соответствует формату передачи.</li>";
        $block['text'] .= "<li>Было набрано достаточное количество человек вашего типажа.</li>";
        $block['text'] .= "</ul>";
        
        return $block['text'];
    } 
    
    /**
     * Получить текст письма для участника, с информацией о том, что его заявка на роль предварительно одобрена
     * @param ProjectMember $projectMember - данные заявки участника
     * @param array $mailOptions - дополнительные настройки для создания письма
     * @return string
     */
    public function createPendingMemberMailText($projectMember, $mailOptions=array())
    {
        $segments    = new CMap();
        $mailOptions = $this->owner->getMailDefaults();
        $mailOptions['showContactPhone'] = false;
        $vacancyName = $projectMember->vacancy->name;
        $projectName = $projectMember->vacancy->event->project->name;
        
        $text  = $this->createUserGreeting($projectMember->member);
        $text .= 'Ваша заявка на роль &laquo;'.$vacancyName.'&raquo; в проекте &laquo;'.$projectName.'&raquo; получала статус &laquo;предварительно одобрена&raquo;.<br>';
        $text .= 'Это означает, что вы прошли первый этап отбора, но окончательное решение пока еще не принято.<br>';
        $text .= 'Наш менеджер обязательно свяжется с вами в ближайшее время и расскажет о дальнейших этапах кастинга!<br>';
        $text .= 'Удачи!';
        $text .= '<br><br><i>(Это автоматическое уведомление, отвечать на него не нужно)</i>';
        
        $segments->add(null, $this->owner->textBlock($text));
        $mailOptions['segments'] = $segments;
        // создаем виджет и получаем из него полный HTML-код письма
        return $this->owner->widget('application.modules.mailComposer.extensions.widgets.EMailAssembler.EMailAssembler',
            $mailOptions, true);
    }
    
    /**
     * Создать тему для письма заказчику (предоставление доступа к отбору актеров/проведение онлан-кастинга)
     * @param CustomerInvite $customerInvite - приглашение заказчика
     * @return string
     */
    public function createCustomerInviteMailSubject($customerInvite)
    {
        $subject = 'Вам предоставлен доступ ';
        switch ( $customerInvite->objecttype )
        {
            case 'project':
                $subject .= 'к отбору актеров в проекте "'.$customerInvite->project->name.'"';
            break;
            case 'event':
                $subject .= 'к отбору актеров на мероприятие "'.$customerInvite->event->name.
                    '" ('.$customerInvite->event->getFormattedTimePeriod().')';
            break;
            case 'vacancy': 
                $subject .= 'к отбору актеров на роль "'.$customerInvite->vacancy->name.'"';
            break;
        }
        return $subject;
    }
    
    /**
     * Создать письмо для заказчика, которое предоставляет ему доступ к отбору актеров
     * @param CustomerInvite $customerInvite - приглашение заказчика
     * @param array $mailOptions - дополнительные настройки для создания письма
     * @return string
     */
    public function createCustomerInviteMailText($customerInvite, $mailOptions=array())
    {
        $segments    = new CMap();
        $mailOptions = $this->owner->getMailDefaults();
        
        // приветствие, объясняем, куда мы дали доступ
        $text = $this->createCustomerGreeting($customerInvite);
        $text .= $this->createCustomerAccessDescription($customerInvite);
        $segments->add(null, $this->owner->textBlock($text));
        
        // краткая инструкция 
        $helpText = $this->createCustomerInviteHelp($customerInvite);
        $segments->add(null, $this->owner->textBlock($helpText, 'Как пользоваться'));
        
        // пишем дополнительные комментарии внизу
        $infoText = $this->createCustomerInviteInfo($customerInvite);
        $segments->add(null, $this->owner->textBlock($infoText, 'Особенности работы с заявками'));
        
        // наш комментарий к этому приглашению (если есть)
        $commentText = $this->createCustomerInviteComment($customerInvite);
        $segments->add(null, $this->owner->textBlock($commentText));
        
        // кнопка "начать отбор"
        $buttonSegment = $this->createCustomerInviteButton($customerInvite);
        $segments->add(null, $buttonSegment);
        
        $mailOptions['segments'] = $segments;
        // создаем виджет и получаем из него полный HTML-код письма
        return $this->owner->widget('application.modules.mailComposer.extensions.widgets.EMailAssembler.EMailAssembler',
            $mailOptions, true);
    }
    
    /**
     * Создать текст письма с приглашением активировать анкету для актеров из базы Светланы Строиловой
     * @param Questionary $questionary
     * @return string
     */
    public function createSSInviteMailText($questionary)
    {
        return $this->owner->widget('application.modules.mailComposer.extensions.mails.EMailSSNotification.EMailSSNotification',
            array('questionary' => $questionary),
            true);
    }
    
    /**
     * Создать текст письма с приглашением активировать анкету для актеров из нашей базы
     * @param Questionary $questionary
     * @return string
     */
    public function createECRegistrationMailText($questionary)
    {
        return $this->owner->widget('application.modules.mailComposer.extensions.mails.EMailECRegistration.EMailECRegistration',
            array('questionary' => $questionary),
            true);
    }
    
    /**
     * Подтверждение регистрации через подачу заявки на проект "топ-модель по-русски"
     * @param Questionary $questionary
     * @return string
     */
    public function createTMRegistrationMailText($questionary)
    {
        return $this->owner->widget('application.modules.mailComposer.extensions.mails.EMailTMRegistration.EMailTMRegistration',
            array('questionary' => $questionary),
        true);
    }
    
    /**
     * Подтверждение регистрации через подачу заявки на проект "МастерШеф"
     * @param Questionary $questionary
     * @param EventVacancy $questionary
     * @return string
     */
    public function createMCRegistrationMailText($questionary, $vacancy)
    {
        return $this->owner->widget('application.modules.mailComposer.extensions.mails.EMailMCRegistration.EMailMCRegistration',
            array(
                'questionary' => $questionary,
                'vacancy'     => $vacancy,
            ),
        true);
    }
    
    /**
     * Создать письмо с фотовызывным
     * @param RCallList $callList
     * @param bool $addContacts
     * @return string
     */
    public function createCallListMailText($callList, $addContacts=false)
    {
        return $this->owner->widget('application.modules.mailComposer.extensions.mails.EMailCallList.EMailCallList',
            array('callList' => $callList, 'addContacts' => $addContacts),
            true);
    }
    
    /**
     * Создать письмо с кастинг-листом
     * @param RCallList $castingList
     * @param bool $addContacts
     * @return string
     */
    public function createCastingListMailText($castingList, $addContacts=false)
    {
        return $this->owner->widget('application.modules.mailComposer.extensions.mails.EMailCastingList.EMailCastingList',
            array('callList' => $castingList, 'addContacts' => $addContacts),
            true);
    }
    
    /**
     * Создать письмо с коммерческим предложением
     * @param CustomerOffer $offer
     * @param User $manager - руководитель проектов от имени которого составляется письмо
     * @return string
     */
    public function createOfferMailText($offer, $manager=null)
    {
        return $this->owner->widget('application.modules.mailComposer.extensions.mails.EMailOffer.EMailOffer',
            array('offer' => $offer, 'manager' => $manager), true);
    }
    
    /**
     * Создать письмо с оповещением о новом заказе
     * @param FastOrder $order - заказ, для которого составляется письмо
     * @param string $target - для кого составляется письмо
     *                         team - оповещение для команды
     *                         customer - подтверждение для заказчика
     * @return string
     */
    public function createOrderMailText($order, $target)
    {
        return $this->owner->widget('application.modules.mailComposer.extensions.mails.EMailOrder.EMailOrder',
            array('order' => $order, 'target' => $target), true);
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
     * @param bool $showAddInfo - показывать ли дополнительную информацию? (для подтвержденных участников)
     * @return array
     * 
     * @todo если съемки заканчиваются на следующий день - добавлять дату ко времени окончания
     * @todo вернуть ссылку на мероприятие
     */
    protected function createSingleEventDescription($event, $showAddInfo=false)
    {
        // создаем ссылку на просмотр мероприятия
        //$eventUrl  = Yii::app()->createAbsoluteUrl('/projects/projects/view', array('eventid' => $event->id));
        //$eventLink = CHtml::link($event->name, $eventUrl, array('target' => '_blank'));
        // дата, время, название мероприятия
        $block = array();
        $block['header'] = $event->getFormattedTimePeriod();
        $block['text']   = '<h4>'.$event->name.'</h4>';
                 
        if ( trim($event->description) )
        {// описание
            $block['text'] .= $event->description;
        }
        if ( $showAddInfo )
        {// дополнительная информация для подтвержденных участников
            $block['text'] .= $this->getEvendAddInfo($event);
        }
        return $block;
    }
    
    /**
     * Получить информацию о группе мероприятий
     * @param ProjectEvent $group - отображаемая группа мероприятий
     * @param bool $showAddInfo - показывать ли дополнительную информацию? (для подтвержденных участников)
     * @return array
     */
    protected function createGroupEventDescription($group, $showAddInfo=false)
    {
        $blocks = array();
        
        foreach ( $group->events as $event )
        {// отображаем все дни(мероприятия) которые включены в группу.
            // одно мероприятие - один блок
            $blocks[] = $this->createOneGroupEventDescription($event, $showAddInfo);
        }
        return $blocks;
    }
    
    /**
     * Получить описание одного мероприятия из группы
     * Если на этот отдельный день для участника есть еще доступные вакансии - 
     * то они не выводятся здесь, а приходят отдельным письмом
     * 
     * @param ProjectEvent $event - отображаемое мероприятие
     * @param bool $showAddInfo - показывать ли дополнительную информацию? (для подтвержденных участников)
     * @return string
     * 
     * @todo вернуть ссылку на мероприятие
     * @todo получить дополнительную информацию об участниках отдельным блоком
     */
    protected function createOneGroupEventDescription($event, $showAddInfo=false)
    {
        //$eventUrl  = Yii::app()->createAbsoluteUrl('/projects/projects/view', array('eventid' => $event->id));
        //$eventLink = CHtml::link($event->name, $eventUrl, array('target' => '_blank'));
        $block = array();
        $block['header'] = $event->getFormattedTimePeriod();
        $block['text']   = '<h4>'.$event->name.'</h4>';
        
        if ( trim($event->description) )
        {
            $block['text'] .= $event->description;
        }
        if ( $showAddInfo )
        {
            $block['text'] .= $this->getEvendAddInfo($event);
        }
        return $message;
    }
    
    /**
     * Получить дополнительную информацию для участников
     * @param ProjectEvent $event
     * @return string
     */
    protected function getEvendAddInfo($event)
    {
        $text = '<h3>Как подтвержденному участнику вам теперь доступна следующая информация:</h3>';
        if ( trim($event->meetingplace) )
        {
            $text .= '<h4>Время и место встречи:</h4>';
            //$text .= '<p>'.$event->meetingplace.'</p>';
            $text .= $event->meetingplace;
        }
        if ( trim($event->memberinfo) )
        {
            $text .= '<h4>Информация для участников:</h4>';
            //$text .= '<p>'.$event->memberinfo.'</p>';
            $text .= $event->memberinfo;
        }
        return $text;
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
        $infoBlock['header'] = 'Предлагаемые роли:';
        $infoBlock['text']   = 'Здесь перечислены все роли, на которые вы можете подать заявку.<br>';
        $infoBlock['text']  .= 'Система выбирает их, основываясь на данных вашей анкеты. ';
        $blocks[] = $infoBlock;
        
        foreach ( $vacancies as $vacancy )
        {
            $vacancyBlock = array();
            $vacancyBlock['header'] = '&#9658;'.$vacancy->name;
            $vacancyBlock['text']   = '<b>Описание:</b>'.$vacancy->description;
            if ( $vacancy->salary )
            {
                $vacancyBlock['text'] .= "<h5>Оплата (за съемочный день): {$vacancy->salary} р.</h5>";
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
        $vacancyName = $projectMember->vacancy->name;
        
        $block['text'] = $this->createUserGreeting($projectMember->member);
        $block['text'] .= 'Некоторое время назад вы подавали заявку на роль &laquo;'.$vacancyName.'&raquo;';
        $block['text'] .= 'Поздравляем! Ваша заявка была подтверждена!'."<br>\n";
        $block['text'] .= 'Наш менеджер обязательно свяжется с вами в ближайшее время 
            и расскажет о всех подробностях вашего участия в проекте!.'."<br>\n";
        $block['text'] .= "Удачи!<br>\n";
        
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
            return '<span style="font-size:18px;">'.$questionary->firstname.", здравствуйте.</span><br>\n<br>\n";
        }else
        {
            return "Добрый день.<br>\n<br>\n";
        }
    }
    
    /**
     * Получить строку с приветствием для заказчика
     * @param CustomerInvite $customerInvite - приглашение заказчика
     * @return string
     * 
     * @todo функция-заглушка. Подставлять имя в приветствии.
     */
    protected function createCustomerGreeting($customerInvite)
    {
        if ( trim($customerInvite->name) )
        {
            return "{$customerInvite->name}, здравствуйте.<br>\n<br>\n";
        }else
        {
            return "Добрый день.<br>\n<br>\n";
        }
    }
    
    /**
     * Описание того, куда получен доступ: проект, мероприятие, роль или онлайн-кастинг
     * (фрагмент письма с приглашением для заказчика)
     * @param CustomerInvite $customerInvite - приглашение заказчика
     * @return string
     */
    protected function createCustomerAccessDescription($customerInvite)
    {
        $text = 'По вашей просьбе предоставляем вам доступ к процессу отбора актеров ';
        
        switch ( $customerInvite->objecttype )
        {
            case 'project': $text .= 'для проекта "'.$customerInvite->project->name.'".'; break;
            case 'event':
                $text .= 'на мероприятие "'.$customerInvite->event->name.'"';
                if ( $customerInvite->event->type != ProjectEvent::TYPE_GROUP AND ! $customerInvite->event->nodates )
                {// у мероприятия есть конкретная дата, и это не группа событий
                    $text .= ' , которое состоится '.$customerInvite->event->getFormattedTimeStart();
                }
            break;
            case 'vacancy': 
                $text .= 'на роль "'.$customerInvite->vacancy->name.'".';
            break;
        }
        
        return $text;
    }
    
    /**
     * Краткая справка о том, как пользоваться отбором актеров
     * (фрагмент письма с приглашением для заказчика)
     * @param CustomerInvite $customerInvite - приглашение заказчика
     * @return string
     */
    protected function createCustomerInviteHelp($customerInvite)
    {
        $text  = 'Для того чтобы начать отбор участников, воспользуйтесь ссылкой "Начать отбор" в конце письма.<br>';
        $text .= 'Пройдя по ней, вы увидите список заявок';
        
        switch ( $customerInvite->objecttype )
        {
            case 'project': $text .= ' на участие в этом проекте, который разделен по съемочным дням и ролям.'; break;
            case 'event':   $text .= ', он разделен по ролям.'; break;
            case 'vacancy': $text .= ' на эту роль.'; break;
        }
        
        $text .= '<br><br>Каждую заявку можно предварительно одобрить, подтвердить или отклонить.';
        $text .= '<ul>';
        $text .= '<li>Подтвердить: взять актера на роль.
            Вы уверены в том, что этот человек вам подходит, и хотели бы видеть его на съемочной площадке.';
        $text .= '<li>Предварительно одобрить: вы еще не уверены в том что именно этот человек 
            подходит для этой роли, или хотите отобрать нескольких лучших претендентов, 
            чтобы потом выбрать уже из них (удобно при большом количестве заявок на роль). 
            Предварительное одобрение не закрепляет за актером роль - это делается только кнопкой "подтвердить".';
        $text .= '<li>Отклонить: этот человек вам не подходит? Отклоните его заявку! 
            Ваше решение для участника окончательно: повторно подать заявку на эту же роль он не сможет.';
        $text .= '</ul>';
        
        $text .= 'После того как вы закончите подбор актеров, нажмите на большую кнопку "Завершить" внизу страницы.';
        
        return $text;
    }
    
    /**
     * Дополнительная информация с описанием того как происходит отбор актеров
     * (фрагмент письма с приглашением для заказчика)
     * @param CustomerInvite $customerInvite - приглашение заказчика
     * @return string
     */
    protected function createCustomerInviteInfo($customerInvite)
    {
        $text = '';
        $text .= '<ul>';
        $text .= '<li>В процессе работы нет необходимости сверять такие данные как рост, вес или возраст 
            участника с условиями отбора на роль: наши поисковые алгоритмы уже сделали эту работу за вас. 
            EasyCast устроен таким образом, что те, кто не проходит по требованиям роли не получает 
            на нее приглашения и не может подать на нее заявку.';
        $text .= '<li>Подтверждение или отклонение заявок нельзя отменить (мы обязательно добавим эту функцию позже). 
            Пожалуйста, будьте внимательны. Пользуйтесь предварительным одобрением заявок.';
        $text .= '<li>После подтверждения заявки мы связываемся с актером, еще раз просим 
            его подтвердить свое участие и сообщаем ему всю необходимую информацию (время, место, условия съемок).';
        $text .= '<li>Когда на роль набирается нужное количество человек - оставшиеся 
            на нее заявки отклоняются автоматически.';
        $text .= '<li>Иногда имеет смысл подождать, если текущее количество заявок вас не устраивает. 
            По нашим правилам участники могут подавать заявки пока роль не будет заполнена - 
            возможно тот кто вам нужен пока еще не успел этого сделать.';
        $text .= '</ul>';
        
        return $text;
    }
    
    /**
     * Комментарий и контакты менеджера, по которым заказчик может связатся с нами, 
     * если у него проблемы с отбором людей
     * (фрагмент письма с приглашением для заказчика)
     * @param CustomerInvite $customerInvite - приглашение заказчика
     * @return string
     */
    protected function createCustomerInviteComment($customerInvite)
    {
        $text  = '';
        $phone = Yii::app()->params['adminPhone'];
        
        if ( trim(strip_tags($customerInvite->comment)) )
        {
            $text .= 'Дополнительная информация: '.$customerInvite->comment;
        }
        if ( trim($customerInvite->manager->questionary->mobilephone) )
        {// стараемся всегда дать телефон того, кто отправлял приглашение 
            $phone = $customerInvite->manager->questionary->mobilephone;
        }
        //$text .= 'Если у вас остались вопросы - то вы можете задать их просто ответив на это письмо,
        //    или по телефону '.$phone.'.';
        
        return $text;
    }
    
    /**
     * Кнопка "начать отбор" в письме с приглашением для заказчика
     * @param CustomerInvite $customerInvite - приглашение заказчика
     * @return array - массив для создания блока письма
     */
    protected function createCustomerInviteButton($customerInvite)
    {
        $block  = array();
        $button = array();
        // ссылка на отбор людей
        $url = Yii::app()->createAbsoluteUrl('/projects/invite/selection',
            array(
                'id' => $customerInvite->id,
                'k1' => $customerInvite->key,
                'k2' => $customerInvite->key2,
        ));
        //$block['text'] .= '<small>(Ссылка действительна в течении часа после использования)</small>';
        // сама кнопка 
        $button['caption'] = 'Начать отбор';
        $button['link']    = $url;
        // добавляем кнопку в блок
        $block['button'] = $button;
        
        return $block;
    }
}