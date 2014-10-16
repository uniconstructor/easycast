<?php 
/**
 * Форма создания/редактирования приглашения для заказчика
 * @todo сделать 3 кнопки "создать", "сохранить" и "отправить"
 */
/* @var $form TbActiveForm */
/* @var $model CustomerInvite */

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id' => 'customer-invite-form',
	'enableAjaxValidation' => true,
));
?>
<div class="page-alternate">
    <div class="container">
        <?php
        echo $form->errorSummary($model);
        echo $form->textFieldRow($model, 'email', array('class' => 'span5', 'maxlength' => 255));
        echo $form->textFieldRow($model, 'name',  array('class' => 'span5', 'maxlength' => 255));
        
        // Наш комментарий для заказчика
    	echo $form->labelEx($model, 'comment'); 
        $this->widget('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
        	'model'     => $model,
        	'attribute' => 'comment',
        	'options'   => array('lang' => 'ru'),
        ));
        echo $form->error($model, 'comment');
        ?>
        <div class="alert alert-info">
            Этот комментарий добавится в письмо с приглашением, в самый конец. Используйте это поле
            чтобы сообщить заказчику дополнительную информацию.
        </div>
        <?php 
        // галочки для выбора статусов заявок, которые будут включены в фотовызывной
        echo CHtml::label('Статусы заявок', 'statuses');
        echo CHtml::checkBoxList('statuses', array(
                ProjectMember::STATUS_DRAFT, 
                ProjectMember::STATUS_PENDING,
            ), 
            array(
                ProjectMember::STATUS_DRAFT   => 'На рассмотрении',
                ProjectMember::STATUS_PENDING => 'Предварительно одобренные',
                ProjectMember::STATUS_ACTIVE  => 'Утвержденные',
            ),
            array(
                'labelOptions' => array('style' => 'display:inline;'),
            )
        );
        echo '<br><br>';
        echo CHtml::label('<b>Разрешить просмотр контактов во всех заявках?</b>', 'dispayContacts', array(
            'style' => 'display:inline;',
        ));
        echo CHtml::checkBox('displayContacts');
        ?>
    	<div class="form-actions">
    		<?php
    		if ( $model->isNewRecord )
    		{
    		    $this->widget('bootstrap.widgets.TbButton', array(
    		        'buttonType' => 'submit',
    		        'type'       => 'primary',
                    'size'       => 'large',
    		        'label'      => 'Отправить',
    		    ));
    		} 
            ?>
    	</div>
    <?php $this->endWidget(); ?>
    </div>
</div>