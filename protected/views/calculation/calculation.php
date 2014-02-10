<?php
/**
 * Страница с формой расчета стоимости
 */
/* @var $this CalculationController */


$this->pageTitle = 'Расчет стоимости';

$this->breadcrumbs = array(
    'Расчет стоимости',
);

// форма расчета стоимости
/* @var $form TbActiveForm */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'calculation-form',
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
    <h1 style="text-align:center;">Расчет стоимости</h1>
    <p class="note muted" style="text-align:center;">
        <?php echo Yii::t('coreMessages', 'form_required_fields', array('{mark}' => '<span class="required">*</span>')); ?>
    </p>
    <?php 
    // ошибки формы
    echo $form->errorSummary($calculationForm);
    // название проекта
    echo $form->textFieldRow($calculationForm, 'projectname', array(
        'size'        => 60,
        'maxlength'   => 255,
        'prepend'     => '<i class="icon icon-film"></i>',
        'placeholder' => ''));
    // тип проекта
    echo $form->dropDownListRow($calculationForm, 'projecttype', $projectTypes);
    
    ?>
    
    <div class="control-group">
        <?php 
        echo $form->labelEx($calculationForm, 'eventtime', array('class' => 'control-label'));
        ?>
        <div class="controls">
        <?php
        // дневная или ночная съемка
        $form->widget(
            'ext.ECMarkup.ECToggleInput.ECToggleInput',
            array(
                'model'     => $calculationForm,
                'attribute' => 'eventtime',
                'onLabel'   => 'Дневная',
                'onValue'   => 'day',
                'offLabel'  => 'Ночная',
                'offValue'  => 'night',
            )
        );
        echo $form->error($calculationForm, 'eventtime');
        ?>
        </div>
    </div>
    
    <?php 
    // кого хотите пригласить?
    echo $form->select2Row($calculationForm, 'categories',
        array(
            'asDropDownList' => false,
            //'data'           => $categories,
            'options' => array(
                'tags'            => $categories,
                'placeholder'     => 'Выберите одну или несколько категорий',
                'width'           => '60%',
                'tokenSeparators' => array(',')
            )
        )
    );
    // предполагаемая дата проведения
    echo $form->datepickerRow($calculationForm,
        'plandate', array(
            'options' => array(
                'language'  => 'ru',
                'format'    => 'dd.mm.yyyy',
                'startView' => 'month',
                'weekStart' => 1,
                'autoclose' => true,
            ),
        ),
        array(
            'hint'    => 'Если точная дата еще не известна - оставьте поле пустым',
            'prepend' => '<i class="icon-calendar"></i>',
        )
    );
    // сколько съемочных дней планируется
    echo $form->textFieldRow($calculationForm, 'daysnum', array(
        'maxlength'   => 255,
        'prepend'     => '<i class="icon icon-th"></i>',
        'append'      => 'дн.',
    ));
    // длительность смены (в часах)
    echo $form->textFieldRow($calculationForm, 'duration', array(
        'maxlength'   => 255,
        'prepend'     => '<i class="icon icon-time"></i>',
        'append'      => 'ч.',
        'hint'        => 'Если на разные дни съемок планируется разное количество часов - укажите здесь максимальное
            число и в комментариях напишите как именно распределяется время: это поможет нам точнее расчитать
            для вас стоимость.',
    ));
    ?>
    <div class="control-group">
        <div class="controls">
            <h3>Контактная информация</h3>
        </div>
    </div>
    <?php
    // имя
    echo $form->textFieldRow($calculationForm, 'name', array(
        'size'        => 60,
        'maxlength'   => 255,
        'prepend'     => '<i class="icon icon-user"></i>',
        'placeholder' => 'Имя',
    ));
    // фамилия
    echo $form->textFieldRow($calculationForm, 'lastname', array(
        'size'        => 60,
        'maxlength'   => 255,
        'prepend'     => '<i class="icon icon-user"></i>',
        'placeholder' => 'Фамилия',
    ));
    // email
    echo $form->textFieldRow($calculationForm, 'email', array(
        'size'        => 60,
        'maxlength'   => 255,
        'prepend'     => '@',
        'placeholder' => 'your@email.com',
        'hint'        => 'На этот адрес мы отправим вам расчет стоимости',
    ));
    // телефон
    echo $form->textFieldRow($calculationForm, 'phone', array(
        'size'        => 60,
        'maxlength'   => 20,
        'prepend'     => '+7',
        'placeholder' => '(987)654-32-10',
        'hint'        => 'Для связи с вами, на случай если потребуется уточнить детали.<br>(10 цифр, без восьмерки)',
    ));
    // комментарий
    echo $form->redactorRow($calculationForm, 'comment', array(
        'editorOptions' => array('lang' => 'ru'),
    ));
    ?>
    <div class="form-actions">
        <?php 
        // кнопка отправки
        $form->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'submit',
            'type'       => 'success',
            'size'       => 'large',
            'label'      => 'Запросить расчет стоимости',
        )); 
        ?>
    </div>
    <?php 
        $this->endWidget();
    ?>
</div>