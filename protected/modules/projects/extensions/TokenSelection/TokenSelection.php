<?php
// подключаем родительский класс (заявки и участники проекта)
Yii::import('application.modules.admin.extensions.ProjectMembers.ProjectMembers');

/**
 * Виджет отбора участников по одноразовой ссылке из письма заказчика
 * 
 * @todo добавить дополнительную проверку ключей в init
 * @todo брать objectType и objectId из приглашения
 */
class TokenSelection extends ProjectMembers
{
    /**
     * @var CustomerInvite - приглашение, дающее доступ заказчику
     */
    public $customerInvite;
    /**
     * @var bool - отображать ли полную анкету участника в заявке?
     */
    public $displayFullInfo = true;
    /**
     * @var string - по умолчанию показываем заявки и предварительно подтвержденных участников
     */
    public $displayType = 'applications';
    
    /**
     * @see ProjectMembers::init()
     */
    public function init()
    {
        if ( ! ( $this->customerInvite instanceof CustomerInvite ) )
        {
            throw new CException('В виджет отбора актеров не передано приглашение');
        }
        // тип и id объекта берем из приглашения
        $this->objectType = $this->customerInvite->objecttype;
        $this->objectId   = $this->customerInvite->objectid;
        
        parent::init();
    }
    
    /**
     * @see ProjectMembers::run()
     */
    public function run()
    {
        parent::run();
        $linkParams = array(
            'id' => $this->customerInvite->id,
            'k1' => $this->customerInvite->key,
            'k2' => $this->customerInvite->key2,
        );
        $this->render('footer', array('linkParams' => $linkParams));
    }
    
    /**
     * Получить кнопки с действиями для участника
     * @param ProjectMember $member
     * @return string
     * 
     * @todo рефакторинг: вынести краткий список участников в отдельный виджет
     */
    protected function getMemberActions($member)
    {
        $this->widget('application.modules.projects.extensions.MemberActions.MemberActions', array(
            'member'         => $member,
            'customerInvite' => $this->customerInvite,
        ), true);
    }
    
    /**
     * Посмотреть заявки на вакансию, а также утвержденных на вакансию участников
     * @param EventVacancy $vacancy - просматриваемая вакансия
     * @return string - html-код таблицы с участниками
     */
    protected function getVacancyMembers($vacancy=null)
    {
        $result = '';
        if ( ! $vacancy )
        {// отображается одна роль отдельно, а не все роли мероприятия
            $vacancy = EventVacancy::model()->findByPk($this->objectId);
        }
        
        if ( $customerData = $this->customerInvite->loadData() )
        {// нужны только заявки с определенными статусами
            $members = ProjectMember::model()->forVacancy($vacancy->id)->withStatus($customerData['statuses'])->findAll();
        }else
        {
            $members = $vacancy->requests;
        }
        // получаем краткое описание вакансии
        $result .= $this->getVacancySummary($vacancy);
        if ( ! $members )
        {// для этой роли нет участников или заявок - так и напишем
            $result .= '<div class="alert">(Пусто)</div>';
            return $result;
        }
        // участники или заявки есть - отобразим их
        if ( $this->displayFullInfo )
        {// выводим полную анкету для каждого участника
            $result .= $this->getFullMembersList($members);
        }else
        {// выводим краткую таблицу
            $result .= $this->getShortMembersList($members);
        }
    
        return $result;
    }
    
    /**
     * Получить список заявок на участие с кнопками принятия/отклонения заявки
     * с подробной информацией о каждом участнике (вся анкета)
     *
     * @param array $members - список заявок (объекты класса ProjectMember)
     * @return string
     */
    protected function getFullMembersList($members)
    {
        return $this->widget('application.modules.projects.extensions.QTokenFullDataList.QTokenFullDataList', array(
            'members'        => $members,
            'customerInvite' => $this->customerInvite,
        ), true);
    }
}