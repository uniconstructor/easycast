<?php
/**
 * Страница создания роли в онлайн-кастинге
 */
/* @var $this OnlineCastingController */
/* @var $form TbActiveForm */
?>
<div class="page-alternate">
    <div id="wizard-bar" class="progress progress-striped progress-info">
        <div class="bar"></div>
    </div>
    <div class="title-page">
        <h1 class="title">Требования к участникам</h1>
        <h4 class="intro-description">
            Укажите разделы в которых будет производиться поиск.
            Если не выбрано ни одной категории - поиск будет происходить по всей базе.<br>
            Количество подходящих людей можно увидеть внизу.
        </h4>
    </div>
    <?php 
    // получаем корневой раздел каталога ("вся база") для того чтобы искать по всем доступным анкетам
    $rootSection = CatalogSection::model()->findByPk(1);
    // виджет расширенной формы поиска (по всей базе)
    $this->widget('catalog.extensions.search.QSearchForm.QSearchForm', array(
        'searchObject' => $rootSection,
        'mode'         => 'form',
        'dataSource'   => 'external',
        'data'         => OnlineCastingForm::getRoleCriteria(),
        'searchUrl'    => '/onlineCasting/saveRoleCriteria',
        'clearUrl'     => '/onlineCasting/clearRoleCriteria',
        'countUrl'     => '/onlineCasting/count',
        'refreshDataOnChange' => true,
        'searchButtonTitle'   => 'Сохранить',
    ));
    ?>
</div>
<div class="page">
    <div class="container">
        <div class="row-fluid">
            <h1 style="text-align:center;">Информация о роли</h1>
            <p class="note muted" style="text-align:center;">
                <?php echo Yii::t('coreMessages', 'form_required_fields', array('{mark}' => '<span class="required">*</span>')); ?>
            </p>
            <?php
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
            // роль
            echo $form->textFieldRow($onlineCastingRoleForm, 'name', array(
                'size'        => 60,
                'maxlength'   => 255,
                'prepend'     => '<i class="icon icon-user"></i>',
                'placeholder' => 'Например "танцор"'));
            // описание роли
            echo $form->redactorRow($onlineCastingRoleForm, 'description', array(
                'editorOptions' => array('lang' => 'ru')
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
            <div class="form-actions text-center">
                <?php 
                // назад
                $form->widget('bootstrap.widgets.TbButton', array(
                    'buttonType' => 'link',
                    'type'       => 'default',
                    'size'       => 'large',
                    'label'      => '< Назад',
                    'url'        => Yii::app()->createUrl('/onlineCasting/create', array('step' => 'info')),
                ));
                echo '&nbsp';
                // к следующем шагу
                $form->widget('bootstrap.widgets.TbButton', array(
                    'buttonType' => 'submit',
                    'type'       => 'success',
                    'size'       => 'large',
                    'label'      => 'Следующий шаг >',
                ));
                ?>
            </div>
            <?php 
                $this->endWidget();
            ?>
        </div>
    </div>
</div>