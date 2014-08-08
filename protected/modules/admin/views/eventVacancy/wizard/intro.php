<?php
/**
 * 
 */
/* @var $this  EventVacancyController */
/* @var $model EventVacancy */
/* @var $form  TbActiveForm */
?>
<div class="row">
    <div class="span12">
        <h3 class="title"><?= $this->getStepLabel(); ?></h3>
        <ul>
            <li>Составьте список всех обязательных и дополнительных полей для роли</li>
            <li>Определите в каком порядке их лучше заполнять</li>
            <li>Выясните, какие поля обязательны для заполнения</li>
            <li>Точно выясните каковы критерии отбора на роль, в каких параметрах допустимо отклонение а в каких нет</li>
            <li>Определите на какие разделы нужно разбить поданные заявки, 
            и какие поля из анкеты содержат информацию о том, помещать анкету в раздел или не помещать</li>
            <li>Решите по каким вкладкам должна быть распределена информация анкеты</li>
        </ul>
        <?php 
        // форма одного шага
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id'     => 'wizard-'.$this->getCurrentStep().'-form',
            'method' => 'post',
            'enableAjaxValidation' => false,
            
        ));
        
        echo CHtml::hiddenField('startWizard', 1);
        echo '<br>';
        $form->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'submit',
            'type'       => 'primary',
            'size'       => 'large',
            'label'      => 'Начать',
            'htmlOptions' => array(
                'name'  => 'submit',
            ),
        ));
        $this->endWidget();
        ?>
    </div>
</div>