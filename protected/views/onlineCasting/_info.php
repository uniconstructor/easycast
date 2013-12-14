<?php
/**
 * Первый шаг создания онлайн-кастинга: внесение информации о проекте и мероприятии
 */
/* @var $this OnlineCastingController */

// получаем все возможные типы проекта
$projectTypes = Project::model()->getTypeList();

// форма создания онлайн-кастинга
/* @var $form TbActiveForm */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'                     => 'online-casting-form',
    'enableAjaxValidation'   => true,
    'enableClientValidation' => true,
    'type'                   => 'horizontal',
    'clientOptions' => array(
        'validateOnSubmit' => true,
        'validateOnChange' => false,
    ),
    'action' => Yii::app()->createUrl('/onlineCasting/create'),
));

?>
<div id="wizard-bar" class="progress progress-striped">
    <div class="bar"></div>
</div>
<div class="span8 offset2">
    <h1 style="text-align:center;">Информация о кастинге</h1>
    <p class="note muted" style="text-align:center;">
        <?php echo Yii::t('coreMessages', 'form_required_fields', array('{mark}' => '<span class="required">*</span>')); ?>
    </p>
    <?php 
    // название проекта
    echo $form->textFieldRow($onlineCastingForm, 'projectname', array(
        'size'        => 60,
        'maxlength'   => 255,
        'prepend'     => '<i class="icon icon-film"></i>',
        'placeholder' => ''));
    // тип проекта
    echo $form->dropDownListRow($onlineCastingForm, 'projecttype', $projectTypes);
    
    // описание проекта
    echo $form->redactorRow($onlineCastingForm, 'projectdescription', array(
        'options' => array(
            'lang' => 'ru')
    ));
    
    // описание мероприятия
    echo $form->redactorRow($onlineCastingForm, 'eventdescription', array(
        'options' => array(
            'lang' => 'ru')
    ));
    
    // предполагаемая дата проведения
    echo $form->datepickerRow($onlineCastingForm,
        'plandate', array(
            'options' => array(
                'language'  => 'ru',
                'format'    => 'dd.mm.yyyy',
                'startView' => 'month',
                'weekStart' => 1,
                'autoclose' => true,
            ),
            'hint'    => 'Если точная дата еще не известна - оставьте поле пустым',
            'prepend' => '<i class="icon-calendar"></i>',
        )
    );
    
    // имя
    echo $form->textFieldRow($onlineCastingForm, 'name', array(
        'size'        => 60,
        'maxlength'   => 255,
        'prepend'     => '<i class="icon icon-user"></i>',
        'placeholder' => 'Имя'));
    // фамилия
    echo $form->textFieldRow($onlineCastingForm, 'lastname', array(
        'size'        => 60,
        'maxlength'   => 255,
        'prepend'     => '<i class="icon icon-user"></i>',
        'placeholder' => 'Фамилия'));
    // email
    echo $form->textFieldRow($onlineCastingForm, 'email', array(
        'size'        => 60,
        'maxlength'   => 255,
        'prepend'     => '@',
        'placeholder' => 'your@email.com'));
    // телефон
    echo $form->textFieldRow($onlineCastingForm, 'phone', array(
        'size'        => 60,
        'maxlength'   => 20,
        'prepend'     => '+7',
        'placeholder' => '(987)654-32-10'));
    // ошибки формы
    echo $form->errorSummary($onlineCastingForm);
    ?>
    <input type="hidden" name="step" value="roles">
    <div class="form-actions">
        <?php 
        // кнопка отправки
        $form->widget('bootstrap.widgets.TbButton', array(
                'buttonType' => 'submit',
                //'buttonType' => 'ajaxSubmit',
                'type'       => 'success',
                'size'       => 'large',
                'label'      => 'Следующий шаг >',
                /*'url'        => Yii::app()->createUrl('/onlineCasting/saveCasting', array(
                    'ajax' => 'online-casting-form')
                ),*/
                /*'htmlOptions' => array(
                    'class' => 'button-next',
                ),*/
                /*'ajaxOptions' => array(
                    'method' => 'post',
                ),*/
            )
        ); 
        ?>
    </div>
    <?php 
        $this->endWidget();
    ?>
</div>