<?php
/**
 * Первая страница создания онлайн-кастинга с формой
 */
/* @var $this OnlineCastingController */

$this->pageTitle = 'Онлайн-кастинг';

$this->breadcrumbs = array(
    'Онлайн-кастинг'
);
?>

<?php 
// wizard с формой создания кастинга и отбора людей
$this->widget(
    'bootstrap.widgets.TbWizard',
    array(
        'type' => 'tabs', // 'tabs' or 'pills'
        'pagerContent' => '<div style="float:right">
    <!--input type="button" class="btn button-next" name="next" value="Вперед" /-->
    <!--input type="button" class="btn button-last" name="last" value="Last" /-->
    </div>
    <div style="float:left">
    <!--input type="button" class="btn button-first" name="first" value="First" /-->
    <!--input type="button" class="btn button-previous" name="previous" value="Назад" /-->
    </div><br /><br />',
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
            //'onTabClick' => 'js:function(tab, navigation, index) {alert("Tab Click Disabled");return false;}',
        ),
        'tabs' => array(
            array(
                'label'   => 'Информация о кастинге',
                'content' => $this->renderPartial('_step1',
                array(
                        'onlineCastingForm' => $onlineCastingForm,
                    ), true),
                //'content' => 'Информация о кастинге',
                'active'  => ($step == 'info'),
            ),
            array(
                'label'   => 'Условия участия',
                //'content' => '<div id="wizard-bar" class="progress progress-striped"><div class="bar"></div></div>(условия участия)',
                'content' => $this->renderPartial('_roles',
                array(
                        'onlineCastingRoleForm' => $onlineCastingRoleForm,
                    ), true),
                'active'  => ($step == 'roles'),
            ),
            array(
                'label'   => 'Готово',
                'content' => '<div id="wizard-bar" class="progress progress-striped"><div class="bar"></div></div>',
                'active'  => ($step == 'finish'),
            ),
        ),
    )
    );
