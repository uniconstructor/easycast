<?php
/**
 * Страница создания роли в онлайн-кастинге
 */
/* @var $this OnlineCastingController */

/* @var $form TbActiveForm */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'                     => 'online-casting-role-form',
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
<h1 style="text-align:center;">Требования к участникам</h1>
<div class="alert alert-info" style="text-align:center;">
    <h4>Укажите, кого вы хотите пригласить</h4>
    Иконками обозначены категории участников, в которых будет производиться поиск.
    Если не выбрано ни одной категории - поиск будет происходить по всей базе.
</div>
<?php 
// получаем корневой раздел каталога ("вся база") для того чтобы искать по всем доступным анкетам
$rootSection = CatalogSection::model()->findByPk(1);
// виджет расширенной формы поиска (по всей базе)
$this->widget('catalog.extensions.search.QSearchForm.QSearchForm', array(
    'searchObject' => $rootSection,
    //'mode'         => 'filter',
    'data'         => OnlineCastingForm::getRoleCriteria(),
    'searchUrl'    => '/onlineCasting/saveRoleCriteria',
    'clearUrl'     => '/onlineCasting/clearRoleCriteria',
    'refreshDataOnChange' => true,
));
?>
<div class="span8 offset2">
    <h1 style="text-align:center;">Информация о роли</h1>
    <p class="note muted" style="text-align:center;">
        <?php echo Yii::t('coreMessages', 'form_required_fields', array('{mark}' => '<span class="required">*</span>')); ?>
    </p>
    <?php 
    // роль
    echo $form->textFieldRow($onlineCastingRoleForm, 'name', array(
        'size'        => 60,
        'maxlength'   => 255,
        'prepend'     => '<i class="icon icon-user"></i>',
        'placeholder' => 'Например "танцор"'));
    // описание роли
    echo $form->redactorRow($onlineCastingRoleForm, 'description', array(
        'options' => array(
            'lang' => 'ru')
    ));
    // роль
    echo $form->textFieldRow($onlineCastingRoleForm, 'salary', array(
        'size'        => 30,
        'maxlength'   => 255,
        'append'      => 'р.',
        'placeholder' => '',
        'hint'        => 'Если точно не известен - оставьте поле пустым',
    ));
    // ошибки формы
    echo $form->errorSummary($onlineCastingRoleForm);
    ?>
    <input type="hidden" name="step" value="roles">
    <div class="form-actions">
        <?php 
        // назад
        $form->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'link',
            'type'       => 'default',
            'size'       => 'large',
            'label'      => '< Назад',
            'url'        => Yii::app()->createUrl('/onlineCasting/create', array('step' => 'info')),
            )
        );
        echo '&nbsp';
        // к следующем шагу
        $form->widget('bootstrap.widgets.TbButton', array(
                'buttonType' => 'submit',
                'type'       => 'success',
                'size'       => 'large',
                'label'      => 'Следующий шаг >',
            )
        );
        ?>
    </div>
    <?php 
        $this->endWidget();
    ?>
</div>