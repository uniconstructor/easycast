<?php
/**
 * Отображение одной заявки
 */
/* @var $data MemberInstance */
/* @var $customerInvite CustomerInvite */

$owner->widget('admin.extensions.wizards.processor.MpMemberData.MpMemberData', array(
    'member'             => $data,
    'customerInvite'     => $customerInvite,
    'sectionGridOptions' => $sectionGridOptions,
));