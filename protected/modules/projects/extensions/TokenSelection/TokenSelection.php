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
     * (non-PHPdoc)
     * @see ProjectMembers::init()
     */
    public function init()
    {
        parent::init();
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
            'member' => $member,
            'customerInvite' => $this->customerInvite,
        ), true);
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
            'members' => $members,
            'customerInvite' => $this->customerInvite,
        ), true);
    }
}