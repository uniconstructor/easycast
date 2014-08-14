<?php
/**
 * Разметка для динамической формы анкеты (режим заполнения по шагам)
 * @todo предусмотреть случай когда все уже заполнено
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */
?>
<div class="row-fluid">
    <div class="container">
        <?php
        // FIXME сделать загрузку видео в зависимости от настроек
        if ( $this->vacancy->id == 749 )
        {
            $this->render('templates/xupload', array(
                'model' => $this->model,
            ));
        }
        // выводим специальный скрытый элемент, который каждую минуту
        // посылает запрос на сайт, чтобы при длительном
        // заполнении анкеты не произошла потеря сессии и все данные не пропали
        $this->widget('ext.EHiddenKeepAlive.EHiddenKeepAlive', array(
            'url'    => Yii::app()->createAbsoluteUrl('//site/keepAlive'),
            'period' => 45,
        ));
        $validationUrl = Yii::app()->createAbsoluteUrl('//projects/vacancy/validateStep', array(
            'qid' => (int)$this->questionary->id,
            'vid' => $this->vacancy->id,
        ));
        $submitUrl = Yii::app()->createAbsoluteUrl('//projects/vacancy/registration', array(
            'qid' => (int)$this->questionary->id,
            'vid' => $this->vacancy->id,
        ));
        // начало формы регистрации 
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id'     => $this->formId,
            'action' => $submitUrl,
            'type'   => 'vertical',
            'enableAjaxValidation'   => true,
            'enableClientValidation' => false,
            'errorMessageCssClass'   => 'alert alert-danger',
            'clientOptions' => array(
                'validateOnSubmit' => true,
                'validateOnChange' => true,
                'validationUrl'    => $validationUrl,
            ),
        ));
        // скрытое поле для хранения текущего шага
        echo CHtml::hiddenField('_index', '1', array('id' => 'wIndex'));
        
        $ajaxSubmit = CHtml::ajax(array(
            'dataType' => 'json',
            'type'     => 'post',
            'url'      => $submitUrl,
            'data'     => new CJavaScriptExpression("$('#{$this->formId}').serialize()"),
        ));
        
        // составляем шаги формы
        $stepInstances = WizardStepInstance::model()->forVacancy($this->vacancy->id)->findAll();
        $stepIds       = CJSON::encode(CHtml::listData($stepInstances, 'id', 'step.name'));
        $totalCount    = count($stepInstances);
        $tabs   = array();
        $count  = 1;
        $nextJs = "var formObj = $('#{$this->formId}');
            var settings = formObj.data('settings');
            var messages = {};
            var \$button = formObj.data('submitObject'),
                extData = '&' + settings.ajaxVar + '=' + formObj.attr('id');
            if ( \$button && \$button.length) {
                extData += '&' + \$button.attr('name') + '=' + \$button.attr('value');
            }
        ";
        $stepSuccessJs = "";
        foreach ( $stepInstances as $stepInstance )
        {
            $tabs['step'.$count] = array(
                'label'   => $stepInstance->step->name,
                'content' => $this->createStepContent($form, $stepInstance, $count),
            );
            // скрытое поле, содержащее id текущей проверяемой вкладки
            echo CHtml::hiddenField('steps['.$count.']', $stepInstance->id);
            $count++;
            
            if ( $totalCount == $count )
            {// последний шаг формы - отправляем и сохраняем данные
                $stepSuccessJs .= $this->createLastStepJs($stepInstance, $count);
            }
        }
        $nextJs .= "";
        
        // виджет с формой создания роли по шагам
        // wizard с формой создания кастинга и отбора людей
        $this->widget('bootstrap.widgets.TbWizard', array(
            'id'           => 'dynamic-registration-wizard',
            'type'         => 'pills',
            'pagerContent' => $this->render('wizard/pager', array('form' => $form), true),
            'options' => array(
                'nextSelector'     => '.btn-next',
                'previousSelector' => '.button-previous',
                'onTabClick'       => 'js:function(tab, navigation, index) {return false;}',
                'onTabShow'        => 'js:function(tab, navigation, index) {
                    var $total   = navigation.find("li").length;
                    var $current = index + 1;
                    var $percent = ($current/$total) * 100;
                    $("#wizard-bar > .bar").css({width:$percent + "%"});
                    $("#wIndex").val(index+1);
                }',
                'onNext' => "js:function(tab, navigation, index) {
                    {$stepSuccessJs}
                    return true;
                }",
                'onPrevious' => "js:function(tab, navigation, index) {
                    $('.button-next').show();
                    $('#dynamic-registration-submit_{$this->vacancy->id}').hide();
                }",
                'onInit' => "js:function(tab, navigation, index) {
                    $('#wIndex').val(1);
                }",
            ),
            'htmlOptions' => array(
                'class' => 'wizard',
            ),
            'tabs' => $tabs,
        ));
        //'success'  => $data->ajaxSuccessScript,
        $ajaxSubmit = CHtml::ajax(array(
            'dataType' => 'json',
            'type'     => 'post',
            //'data'     => new CJavaScriptExpression("js:function(){return \$('#{$this->formId}').serialize();}"),
            'data'     => new CJavaScriptExpression("formObj.serialize() + extData"),
            'url'      => Yii::app()->createUrl('//projects/vacancy/registration/', array(
                'qid' => (int)$this->questionary->id,
                'vid' => (int)$this->vacancy->id,
                Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
            )),
        ));
        ?>
        <?php $this->endWidget(); ?>
    </div>
</div>
<script>
// проверка промежуточных шагов
$('.button-next').bind('click', function (e) {
    var formObj = $('#<?= $this->formId; ?>');
    var settings = formObj.data('settings');
    var messages = {};
    var $button = formObj.data('submitObject'),
        extData = '&' + settings.ajaxVar + '=' + formObj.attr('id');
    if ( $button && $button.length) {
        extData += '&' + $button.attr('name') + '=' + $button.attr('value');
    }
    
    e.preventDefault();
    $.ajax({
        url:  settings.validationUrl,
        type: formObj.attr('method'),
        data: formObj.serialize() + extData,
        dataType: 'json',
    }).done( function ( data ) {
        var dataType = typeof data;
        if ( ( dataType == 'string' && data == '[]' ) || ( dataType == 'object' && $(data).length === 0 ) )
        {
            moveNext = true;
            console.log('next');
            
            $('#dynamic-registration-form_es_').hide();
            $('#dynamic-registration-form_galleryid_em_').hide();
            $('#dynamic-registration-wizard').bootstrapWizard('next');
        }else
        {
            triggerAjaxValidationEvent(formObj);
            console.log('stop');
            moveNext = false;
        }
        return moveNext;
    });
    console.log('finish');
    return false;
});
// проверка и отправка данных формы на финальном шаге
$('#dynamic-registration-submit_<?= $this->vacancy->id; ?>').bind('click', function (e) {
    var formObj  = $('#<?= $this->formId; ?>');
    var settings = formObj.data('settings');
    var messages = {};
    var $button  = formObj.data('submitObject'),
        extData  = '&' + settings.ajaxVar + '=' + formObj.attr('id');
    if ( $button && $button.length) {
        extData += '&' + $button.attr('name') + '=' + $button.attr('value');
    }
    
    e.preventDefault();
    $.ajax({
        url:  settings.validationUrl,
        type: formObj.attr('method'),
        data: formObj.serialize() + extData,
        dataType: 'json',
    }).done( function (data) {
        var dataType = typeof data;
        if ( ( dataType == 'string' && data == '[]' ) || ( dataType == 'object' && $(data).length === 0 ) )
        {
            moveNext = true;
            console.log('next[final]');
            $('#dynamic-registration-form_es_').hide();
            $('#dynamic-registration-form_galleryid_em_').hide();
            formObj.submit();
        }else
        {
            triggerAjaxValidation(formObj);
            console.log('stop[final]');
            moveNext = false;
        }
        return moveNext;
    });
    console.log('finish[final]');
    return false;
});
</script>