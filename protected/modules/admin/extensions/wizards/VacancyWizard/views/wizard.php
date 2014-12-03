<?php
/**
 * Виджет редактирования роли (из трех шагов)
 */
/* @var $this VacancyWizard */

$this->widget('bootstrap.widgets.TbWizard', array(
        'type'         => 'pills',
        'pagerContent' => $this->render('_pager', null, true),
        'options' => array(
            'nextSelector'     => '.button-next',
            'previousSelector' => '.button-previous',
            'firstSelector'    => '.button-first',
            'lastSelector'     => '.button-last',
            'onTabClick'       => 'js:function(tab, navigation, index) {return false;}',
            'onTabShow'        => 'js:function(tab, navigation, index) {
                var $total   = navigation.find("li").length;
                var $current = index+1;
                var $percent = ($current/$total) * 100;
                $("#wizard-bar > .bar").css({width:$percent+"%"});
            }',
        ),
        'htmlOptions' => array(
            'class' => 'row-fluid',
        ),
        'tabs' => array(
            array(
                'label'   => 'Роль',
                'content' => $this->getInfoTab(),
                'active'  => $this->isActiveTab('info'),
            ),
            array(
                'label'   => 'Критерии отбора',
                'content' => $this->getFiltersTab(),
                'active'  => $this->isActiveTab('filters'),
            ),
            array(
                'label'   => 'Дополнительные поля',
                'content' => $this->getExtraFieldsTab(),
                'active'  => $this->isActiveTab('extrafields'),
            ),
        ),
    )
);

// Выводим здесь все всплывающие modal-формы для сложных значений
// Их оказалось нельзя выводить в середине формы анкеты потому что вложенные виджеты форм в Yii не допускаются
// Сами формы генерируются по ходу отрисовки формы и запоминаются в клипы, а затем выводятся здесь
$clips = Yii::app()->getModule('admin')->formClips;
foreach ( $clips as $clip )
{
    echo $this->controller->clips[$clip];
}
