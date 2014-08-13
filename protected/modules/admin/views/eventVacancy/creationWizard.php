<?php
/**
 * Страница просмотра роли
 */
/* @var $this  EventVacancyController */
/* @var $model EventVacancy */

$this->breadcrumbs = array(
	'Администрирование'          => array('/admin'),
    'Проекты'                    => array('/admin/project'),
    $model->event->project->name => array('/admin/project/view', 'id' => $model->event->project->id),
    $model->event->name          => array('/admin/projectEvent/view', 'id' => $model->event->id),
	$model->name,
);

$this->menu = array(
	array(
	    'label' => 'Страница события', 
	    'url'   => array('/admin/projectEvent/view', 'id' => $model->event->id),
    ),
	array(
	    'label' => 'Добавить роль',
	    'url'   => array('/admin/eventVacancy/create', 'eventid' => $model->event->id),
    ),
	array(
	    'label' => 'Редактировать роль',
	    'url'   => array('/admin/eventVacancy/update', 'id' => $model->id),
    ),
    array(
        'label' => 'Все заявки',
        'url'   => array('/admin/projectMember/index', 'vid' => $model->id),
    ),
);

if ( $model->status === EventVacancy::STATUS_DRAFT )
{// разрешаем удалять вакансию-черновик
    $this->menu[] = array(
        'label'       => 'Удалить роль', 
        'url'         => '#',
        'linkOptions' => array(
                'submit'  => array('/admin/eventVacancy/delete', 'id' => $model->id),
                'confirm' => 'Удалить эту роль?',
                'csrf'    => true,
            ),
        );
}
if ( in_array('active', $model->getAllowedStatuses()) )
{// ссылка на активацию вакансии
    $this->menu[] = array(
        'label' => 'Опубликовать роль',
        'url'   => array('/admin/eventVacancy/setStatus', 'id' => $model->id, 'status' => 'active'),
        'linkOptions' => array(
            'confirm' => 'Это действие оповестит всех подходящих участников о начале съемок.
                ВНИМАНИЕ: после публикации роли редактировать критерии поиска будет нельзя.
                Опубликовать роль "'.$model->name.'"?',
        ),
    );
}
if ( in_array('finished', $model->getAllowedStatuses()) )
{// Ссылка на закрытие вакансии
    $this->menu[] = array(
        'label' => 'Закрыть роль',
        'url'   => array('/admin/eventVacancy/setStatus',
            'id'     => $model->id,
            'status' => 'finished',
        ),
        'linkOptions' => array(
            'confirm' => 'Закрыть роль "'.$model->name.'"?',
        ),
    );
}
if ( $model->status === EventVacancy::STATUS_ACTIVE )
{// предоставить доступ
    $this->menu[] = array(
        'label' => 'Предоставить доступ',
        'url'   => array('/admin/customerInvite/create',
            'objectType' => 'vacancy',
            'objectId'   => $model->id, 
        ),
    );
}

// отображение оповещений о смене статуса
$this->widget('bootstrap.widgets.TbAlert');
?>
<div class="row-fluid">
    <div class="span9">
        <h1 class="title">Роль "<?= $model->name; ?>"</h1>
        <?php
        $registrationUrl = Yii::app()->createAbsoluteUrl('/projects/vacancy/registration', array('vid' => $model->id));
        // описание самой роли
        $this->widget('bootstrap.widgets.TbDetailView', array(
        	'data'       => $model,
        	'attributes' => array(
        		'description:html',
        		array(
                    'label' => 'Заполнение',
                    'value' => '('.$model->membersCount.' из '.$model->limit.') [Заявки: '.$model->requestsCount.']',
                ),
        		array(
                    'label' => ProjectsModule::t('status'),
                    'value' => $model->statustext,
                ),
        		array(
                    'label' => 'Ссылка на форму регистрации',
                    'value' => CHtml::link($registrationUrl, $registrationUrl, array('target' => '_blank')),
                    'type'  => 'html',
                ),
                'salary',
        	),
        ));
        ?>
    </div>
    <div class="span3">
        <?php
        // меню справа - выводится отдельно, для того чтобы использовать 
        $this->beginWidget('zii.widgets.CPortlet', array(
            'title' => Yii::t('coreMessages', 'operations'),
        ));
        $this->widget('bootstrap.widgets.TbMenu', array(
            'type'  => 'tabs', 
            'items' => $this->menu,
        ));
        $this->endWidget();
        ?>
    </div>
</div>
<hr>
<div class="row-fluid">
    <?php 
    $steps = array();
    $steps['Intro'] = array(
        'label'   => 'Подготовка',
        'content' => $this->renderPartial('wizard/intro', null, true),
    );
    $steps['BaseInfo'] = array(
        'label'   => 'Основное',
        'content' => $this->renderPartial('wizard/baseInfo', array('model' => $model), true),
    );
    $steps['SearchData'] = array(
        'label'   => 'Критерии поиска',
        'content' => $this->widget('catalog.extensions.search.QSearchForm.QSearchForm', array(
            'searchObject' => $model,
            'mode'         => 'vacancy',
            'dataSource'   => 'db',
            'searchUrl'    => '/admin/eventVacancy/setSearchData',
            'clearUrl'     => '/admin/eventVacancy/clearFilterSearchData',
            'countUrl'     => '/admin/eventVacancy/setSearchData',
            'countResultPosition' => 'bottom',
            'refreshDataOnChange' => true,
            //'refreshDataOnChange' => false,
            'searchButtonTitle'      => 'Сохранить',
            'clearButtonHtmlOptions' => array(
                'class' => 'btn btn-danger btn-large',
                'id'    => 'clear_search',
            ),
            'countContainerHtmlOptions' => array(
                'class' => 'well text-center',
            ),
        ), true),
    );
    $steps['Sections'] = array(
        'label'   => 'Группы заявок',
        'content' => $this->renderPartial('wizard/sections', array('vacancy' => $model), true),
    );
    
    /*if ( isset($steps[$step]) )
    {
        $steps[$step]['active'] = true;
    }*/

    $ajaxSubmit = CHtml::ajax(array(
        'dataType' => 'json',
        //'success'  => $data->ajaxSuccessScript,
        'type' => 'post',
        'url'  => Yii::app()->createUrl('//admin/eventVacancy/update', array(
            'id'   => $model->id,
            'step' => 'Search',
        )),
        'data' => new CJavaScriptExpression('$("#event-vacancy-form").serialize()'),
    ));
    
    /*$initJs = "\n$('body').on('yiiFormSuccess', function(data) {
        $('#vacancy-wizard').bootstrapWizard('next');
    });\n";*/
    $nextJs = "
        if ( index === 2 )
        {
            var formObj = $('#event-vacancy-form');
            triggerAjaxValidationEvent(formObj);
            var settings = formObj.data('settings');
            
            var messages = {};
            var \$button = formObj.data('submitObject'),
                extData = '&' + settings.ajaxVar + '=' + formObj.attr('id');
    		if ( \$button && \$button.length) {
    			extData += '&' + \$button.attr('name') + '=' + \$button.attr('value');
    		}
    
    		$.ajax({
    			url:  settings.validationUrl,
    			type: formObj.attr('method'),
    			data: formObj.serialize() + extData,
    			dataType: 'json',
    			success: function (data) {
    				if ( data.length === 0 )
    				{
                        {$ajaxSubmit} 
                        return true;
    				} else {
                        return false;
    				}
    			},
            });
        }
        
        return true;
        ";
    
    
    // виджет с формой создания роли по шагам
    // wizard с формой создания кастинга и отбора людей
    $this->widget('bootstrap.widgets.TbWizard', array(
            'id'           => 'vacancy-wizard',
            'type'         => 'pills',
            'pagerContent' => $this->renderPartial('wizard/_pager', null, true),
            'options' => array(
                'nextSelector'     => '.button-next',
                'previousSelector' => '.button-previous',
                'onTabClick'       => 'js:function(tab, navigation, index) {return false;}',
                'onTabShow'        => 'js:function(tab, navigation, index) {
                    var $total   = navigation.find("li").length;
                    var $current = index+1;
                    var $percent = ($current/$total) * 100;
                    $("#wizard-bar > .bar").css({width:$percent+"%"});
                }',
                'onNext' => "js:function(tab, navigation, index) {
                    {$nextJs}
                }",
                /*'onInit' => "js:function(tab, navigation, index) {
                    {$initJs}
                }",*/
                /*'onTabChange' => "js:function(tab, navigation, index) {
                    {$nextJs}
                }",*/
            ),
            'htmlOptions' => array(
                'class' => 'wizard',
            ),
            'tabs' => $steps,
        )
    );
    
    ?>
    <!--h2 class="title">Критерии поиска</h2-->
    <?php 
    // виджет расширенной формы поиска (для указания критериев отбора на роль)
    /*$this->widget('catalog.extensions.search.QSearchForm.QSearchForm', array(
        'searchObject' => $model,
        'mode'         => 'vacancy',
        'dataSource'   => 'db',
        'searchUrl'    => '/admin/eventVacancy/setSearchData',
        'clearUrl'     => '/admin/eventVacancy/clearFilterSearchData',
        'countUrl'     => '/admin/eventVacancy/setSearchData',
        'countResultPosition' => 'bottom',
        'refreshDataOnChange' => true,
        //'refreshDataOnChange' => false,
        'searchButtonTitle'      => 'Сохранить',
        'clearButtonHtmlOptions' => array(
            'class' => 'btn btn-danger btn-large',
            'id'    => 'clear_search',
        ),
        'countContainerHtmlOptions' => array(
            'class' => 'well text-center',
        ),
    ));*/
    ?>
</div>
<hr>
<div class="row-fluid">
    <?php 
    // обязательные и дополнительные поля для подачи заявки
    /*$this->widget('admin.extensions.ExtraFieldsManager.ExtraFieldsManager', array(
        'vacancy' => $model,
    ));*/
    ?>
</div>
<?php
// modal-окна с формами для EditableGrid элементов
$clips = Yii::app()->getModule('admin')->formClips;
foreach ( $clips as $clip )
{
    echo $this->clips[$clip];
}
?>