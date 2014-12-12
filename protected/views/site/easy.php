<?php
/**
 * Страница быстрой регистрации массовки
 * @package easycast
 * 
 * @todo языковые строки
 */
/* @var $this          SiteController */
/* @var $form          TbActiveForm */
/* @var $massActorForm MassActorsForm */

$this->pageTitle   = 'Регистрация - easyCast';
$this->breadcrumbs = array(
    'Регистрация',
);

?>
<div class="row-fluid">
    <div class="span8 offset2">
        <?php
        // форма быстрой регистрации массовки
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id'                     => 'mass-actors-form',
            'enableAjaxValidation'   => true,
            'enableClientValidation' => false,
            'type'          => 'horizontal',
            'clientOptions' => array(
                'validateOnSubmit' => true,
                'validateOnChange' => false,
            ),
        ));
        ?>
        <h1 style="text-align:center;">Регистрация</h1>
        <p class="note muted" style="text-align:center;">
            <?php echo Yii::t('coreMessages', 'form_required_fields', array('{mark}' => '<span class="required">*</span>')); ?>
        </p>
        <?php 
        // ошибки формы
        echo $form->errorSummary($massActorForm);
        // имя
        echo $form->textFieldRow($massActorForm, 'firstname', array(
                'size'        => 60,
                'maxlength'   => 128,
                'placeholder' => 'Имя',
            ),
            array(
                'prepend'     => '<i class="icon icon-user"></i>',
            )
        );
        // фамилия
        echo $form->textFieldRow($massActorForm, 'lastname', array(
                'size'        => 60,
                'maxlength'   => 128,
                'placeholder' => 'Фамилия',
            ),
            array(
                'prepend'     => '<i class="icon icon-user"></i>',
            )
        );
        // email
        echo $form->textFieldRow($massActorForm, 'email', array(
                'size'        => 60,
                'maxlength'   => 255,
                'placeholder' => 'mail@example.com',
            ),
            array(
                'prepend'     => '<i class="icon icon-envelope"></i>',
            )
        );
        // телефон
        echo $form->textFieldRow($massActorForm, 'phone', array(
                'size'        => 60,
                'maxlength'   => 20,
                'placeholder' => '(987)654-32-10',
            ),
            array(
                'prepend'     => '+7',
                'hint'        => 'Десять цифр',
            )
        );
        ?>
        
        <div class="control-group">
            <?php 
            // пол
            echo $form->labelEx($massActorForm, 'gender', array('class' => 'control-label'));
            ?>
            <div class="controls">
            <?php
            $form->widget('ext.ECMarkup.ECToggleInput.ECToggleInput', array(
                'model'     => $massActorForm,
                'attribute' => 'gender',
                'onLabel'   => 'Мужской',
                'onValue'   => 'male',
                'offLabel'  => 'Женский',
                'offValue'  => 'female',
            ));
            echo $form->error($massActorForm, 'gender');
            ?>
            </div>
        </div>
        
        <?php 
        // дата рождения
        echo $form->datePickerRow($massActorForm, 'birthdate', array(
                'options' => array(
                    'language'  => 'ru',
                    'format'    => 'dd.mm.yyyy',
                    'startView' => 'decade',
                    'weekStart' => 1,
                    'startDate' => '-75y',
                    'endDate'   => '-1y',
                    'autoclose' => true,
                ),
            ),
            array(
                'hint'    => 'Нажмите на название месяца или на год',
                'prepend' => '<i class="icon-calendar"></i>',
            )
        );
        // размер оплаты за день
        echo $form->textFieldRow($massActorForm, 'salary', array(
                'size'        => 60,
                'placeholder' => '',
            ),
            array(
                'hint'    => 'за один съемочный день',
                //'prepend' => '<i class="icon-info"></i>',
                'append'  => 'p.',
            )
        );
        
        // фотографии 
        echo $form->labelEx($massActorForm, 'galleryid');
        echo $form->hiddenField($massActorForm, 'galleryid');
        $this->widget('GalleryManager', array(
            'gallery'         => $gallery,
            'controllerRoute' => '/questionary/gallery',
        ));
        echo $form->error($massActorForm, 'galleryid');
        
        // согласие с условиями использования
        echo $form->checkBoxRow($massActorForm, 'policyagreed');
        ?>
        <div class="form-actions">
            <?php 
            // кнопка регистрации
            $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType' => 'submit',
                'type'       => 'success',
                'size'       => 'large',
                'label'      => 'Регистрация'
            )); 
            ?>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>