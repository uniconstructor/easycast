<?php
/**
 * Разметка для динамической формы анкеты
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */
?>
<div class="row-fluid">
    <div class="offset2 span8">
        <?php
        // FIXME сделать загрузку видео в зависимости от настроек
        if ( $this->vacancy->id == 749 )
        {
            $this->render('templates/xupload', array(
                'model' => $model,
            ));
        }
        
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id' => 'dynamic-registration-form',
            'enableAjaxValidation'   => ! $model->hasFullInfo(),
            'enableClientValidation' => false,
            'type'                   => 'vertical',
            'clientOptions' => array(
                'validateOnSubmit' => ! $model->hasFullInfo(),
                'validateOnChange' => false,
                // @todo при AJAX-проверке данных делать кнопку неактивной
                // @todo не происходит перенаправление участника после подачи заявки если 
                //       у него заполнены все поля еще до регистрации
                /*'beforeValidate'   => "js:function(form){
                    $('#dynamic-registration-submit_{$this->vacancy->id}').prop('disabled', 'disabled');
                    $('#dynamic-registration-submit_{$this->vacancy->id}').removeClass('btn-success');
                    $('#dynamic-registration-submit_{$this->vacancy->id}').addClass('btn-disabled');
                    $('#dynamic-registration-submit_{$this->vacancy->id}').text('Проверка...');
                    
                    return true;
                }",
                'afterValidate'    => "js:function(form, data, hasError){
                    $('#dynamic-registration-submit_{$this->vacancy->id}').removeProp('disabled');
                    $('#dynamic-registration-submit_{$this->vacancy->id}').addClass('btn-success');
                    $('#dynamic-registration-submit_{$this->vacancy->id}').removeClass('btn-disabled');
                    $('#dynamic-registration-submit_{$this->vacancy->id}').text('Отправить');
                    
                    return ! hasError;
                }",*/
                'htmlOptions' => array(
                    'class' => 'well',
                ),
            ),
        ));
        // выводим специальный скрытый элемент, который каждую минуту
        // посылает запрос на сайт, чтобы при длительном
        // заполнении анкеты не произошла потеря сессии и все данные не пропали
        $this->widget('ext.EHiddenKeepAlive.EHiddenKeepAlive', array(
            'url'    => Yii::app()->createAbsoluteUrl('//site/keepAlive'),
            'period' => 45,
        ));
        if ( empty($model->extraFields) AND empty($model->userFields) )
        {// участник все заполнил, поблагодарим его за это
            $this->widget('ext.ECMarkup.ECAlert.ECAlert', array(
                'message' => 'В вашей анкете достаточно информации для подачи заявки на эту роль.<br>
                    Вы подходите по критериям отбора.<br>
                    Просто нажмите кнопку для подтверждения участния в этом проекте.',
                'type' => 'success',
            ));
        }else
        {// для подачи заявки нужны доп. данные
            $this->render('_inputDisclaimer');
        }
        
        // @todo добавить возможность вводить дополнительную информацию перед формой
        //       (например правила участия)
        
        foreach ( $model->userFields as $userField )
        {// обязательные поля формы (оставлены только нужные)
            echo $this->getUserFieldLayout($form, $model, $userField);
        }
        foreach ( $model->extraFields as $extraField )
        {// дополнительные поля заявки (оставлены только нужные)
            echo $this->getExtraFieldLayout($form, $model, $extraField);
        }
        if ( $model->hasFullInfo() )
        {// параметр для индикации того что все данные у участника уже есть
            echo CHtml::hiddenField('alreadyFilled', '1');
        }
        ?>
        <div class="form-actions">
            <?php 
            // ошибки формы
            echo $form->errorSummary($model);
            // @todo не выводится ошибка через beforeValidate
            echo $form->error($model, 'galleryid').'<br>';
            // кнопка регистрации
            $this->widget('bootstrap.widgets.TbButton', array(
                'id'         => 'dynamic-registration-submit_'.$this->vacancy->id,
                'buttonType' => 'submit',
                'type'       => 'success',
                'size'       => 'large',
                'label'      => 'Отправить',
            )); 
            ?>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>