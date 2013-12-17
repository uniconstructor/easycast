<?php
/**
 * Первая страница создания онлайн-кастинга с формой
 */
/* @var $this OnlineCastingController */

$this->pageTitle = 'Онлайн-кастинг';

$this->breadcrumbs = array(
    'Онлайн-кастинг',
);

$castingTabContent = '';
if ( $step == 'info' )
{
    $castingTabContent = $this->renderPartial('_info', array(
        'onlineCastingForm' => $onlineCastingForm,
    ), true);
}
$roleTabContent = '';
if ( $step == 'roles' )
{
    $roleTabContent = $this->renderPartial('_roles', array(
        'onlineCastingRoleForm' => $onlineCastingRoleForm,
    ), true);
}
$finishTabContent = '';
if ( $step == 'finish' )
{
    $finishTabContent = $this->renderPartial('_finish', array(
        'onlineCastingRoleForm' => $onlineCastingRoleForm,
        'onlineCastingForm'     => $onlineCastingForm,
    ), true);
}
?>

<?php 
// wizard с формой создания кастинга и отбора людей
// @todo невозможно проставить overflow:hidden для tab-content, поэтому горизонтальная прокрутка пока остается
$this->widget('bootstrap.widgets.TbWizard', array(
        'type'         => 'pills', // 'tabs' or 'pills'
        'pagerContent' => $this->renderPartial('_pager', null, true),
        'options' => array(
            'nextSelector'     => '.button-next',
            'previousSelector' => '.button-previous',
            'firstSelector'    => '.button-first',
            'lastSelector'     => '.button-last',
            'onTabShow'        => 'js:function(tab, navigation, index) {
                var $total = navigation.find("li").length;
                var $current = index+1;
                var $percent = ($current/$total) * 100;
                $("#wizard-bar > .bar").css({width:$percent+"%"});
            }',
            'onTabClick' => 'js:function(tab, navigation, index) {return false;}',
        ),
        'htmlOptions' => array(
            'class' => 'row span12',
        ),
        'tabs' => array(
            array(
                'label'   => 'Информация о кастинге',
                'content' => $castingTabContent,
                'active'  => ($step == 'info'),
            ),
            array(
                'label'   => 'Требования к участникам',
                'content' => $roleTabContent,
                'active'  => ($step == 'roles'),
            ),
            array(
                'label'   => 'Готово',
                'content' => $finishTabContent,
                'active'  => ($step == 'finish'),
            ),
        ),
    )
);

//CVarDumper::dump(OnlineCastingForm::getCastingInfo(), 10, true);
//CVarDumper::dump(OnlineCastingForm::getRoleInfo(), 10, true);
?>
