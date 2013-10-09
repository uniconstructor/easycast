<?php

// подключаем родительский класс (список заявок или участников с подробной информацией о каждом)
Yii::import('application.modules.admin.extensions.QAdminFullDataList.QAdminFullDataList');

/**
 * Список заявок или участников проекта, с подробной информацией о каждом участнике
 */
class QTokenFullDataList extends QAdminFullDataList
{
    /**
     * @var CustomerInvite - приглашение, дающее доступ заказчику
     */
    public $customerInvite;
    
    /**
     * Получить кнопки с действиями для участника
     * @param ProjectMember $member
     * @return string
     */
    protected function getMemberActions($member)
    {
        return $this->widget('application.modules.projects.extensions.MemberActions.MemberActions', array(
            'member'         => $member,
            'customerInvite' => $this->customerInvite,
            'refreshButtons' => true,
        ), true);
    }
}