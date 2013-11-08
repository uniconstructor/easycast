<?php
/**
 * Страница быстрой регистрации массовки
 * @package easycast
 */

$this->pageTitle = 'Регистрация';

// убираем из заголовка все лишнее
$this->ecHeaderOptions = array(
    'displayContacts'  => false,
    'displayloginTool' => false,
    'displayInformer'  => false,
);

// форма быстрой регистрации массовки
/* @var $form TbActiveForm */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'mass-actors-form',
    //'enableAjaxValidation'   => true,
    'enableAjaxValidation'   => true,
    'enableClientValidation' => false,
    'type' => 'horizontal',
    'clientOptions' => array(
        'validateOnSubmit' => true,
        'validateOnChange' => false,
    ),
));
?>

<div class="span8 offset2">
    <p class="note muted" style="text-align:center;">
        <?php echo Yii::t('coreMessages', 'form_required_fields', array('{mark}' => '<span class="required">*</span>')); ?>
    </p>
    <?php 
    // ошибки формы
    echo $form->errorSummary($massActorForm);
    // имя
    echo $form->textFieldRow($massActorForm, 'firstname', array(
            'size' => 60,
            'maxlength' => 128,
            'prepend' => '<i class="icon icon-user"></i>',
            'placeholder' => 'Имя'));
    // фамилия
    echo $form->textFieldRow($massActorForm, 'lastname', array(
            'size' => 60,
            'maxlength' => 128,
            'prepend' => '<i class="icon icon-user"></i>',
            'placeholder' => 'Фамилия'));
    // email
    echo $form->textFieldRow($massActorForm, 'email', array(
            'size' => 60,
            'maxlength' => 255,
            'prepend' => '@',
            'placeholder' => 'your@email.com'));
    // телефон
    echo $form->textFieldRow($massActorForm, 'phone', array(
            'size' => 60,
            'maxlength' => 20,
            'prepend' => '+7',
            'placeholder' => '(987)654-32-10'));
    ?>
    
    <div class="control-group">
    <?php 
    // пол
    echo $form->labelEx($massActorForm, 'gender', array('class' => 'control-label'));
    ?>
        <div class="controls">
        <?php
        $form->widget(
            'ext.ECMarkup.ECToggleInput.ECToggleInput',
            array(
                'model'     => $massActorForm,
                'attribute' => 'gender',
                'onLabel'   => 'Мужской',
                'onValue'   => 'male',
                'offLabel'  => 'Женский',
                'offValue'  => 'female',
            )
        );
        echo $form->error($massActorForm, 'gender');
        ?>
        </div>
    </div>
    
    <?php 
    // дата рождения
    echo $form->datepickerRow(
        $massActorForm,
        'birthdate',
        array(
            'options' => array(
                'language' => 'ru',
                'format'    => Yii::app()->params['inputDateFormat'],
                'startView' => 'decade',
                'weekStart' => 1,
                'startDate' => ''),
            'hint' => 'Нажмите на название месяца или на год, чтобы изменить его',
            'prepend' => '<i class="icon-calendar"></i>'
        )
    );
    
    // фотографии 
    echo $form->labelEx($massActorForm, 'galleryid');
    echo $form->hiddenField($massActorForm, 'galleryid');
    $this->widget('GalleryManager', array(
        'gallery'         => $gallery,
        'controllerRoute' => '/questionary/gallery'
    ));
    echo $form->error($massActorForm, 'galeryid');
    
    ?>
    <div class="form-actions">
        <?php 
        // кнопка ренистрации
        $this->widget(
            'bootstrap.widgets.TbButton',
            array(
                'buttonType' => 'submit',
                'type'  => 'success',
                'size'  => 'large',
                'label' => 'Регистрация'
            )
        ); 
        ?>
    </div>
    <?php 
        $this->endWidget();
    ?>
</div>