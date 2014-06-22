<?php
/**
 * Список кнопок для смены статуса заявки и кнопка для перехода к следующей анкете
 */
/* @var $this MpMemberData */
?>
<div class="row-fluid">
    <?php
    // кнопки для изменения статуса анкеты
    $this->widget('projects.extensions.MemberActions.MemberActions', array(
        'member'             => $this->member,
        'customerInvite'     => $this->customerInvite,
        'forceDisplayStatus' => true,
        'displayMode'        => 'row',
    ));
    ?>
</div>
