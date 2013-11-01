<?php 
/**
 * Форма редактирования мероприятия в админке
 */

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id' => 'project-event-form',
	'enableAjaxValidation' => false,
)); 

$dateFormatter = new CDateFormatter('ru');
?>

	<?php echo Yii::t('coreMessages','form_required_fields', array('{mark}' => '<span class="required">*</span>')); ?>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model, 'name',array('class'=>'span5', 'maxlength'=>255)); ?>
	
	<?php
	// тип мероприятия
	$eventTypes = $model->getTypes();
	echo $form->dropDownListRow($model, 'type', $eventTypes);
	?>
	
	<?php 
	// группа мероприятия
	$groups = $model->getOpenGroups($project->id);
	if ( $model->isNewRecord AND $groupid )
	{// создаем событие в заранее определенной группе
	    $model->parentid = $groupid;
	}
	if ( $model->status != 'draft' )
	{// нельзя перемещать активные мероприятия между группами
	    echo $form->dropDownListRow($model, 'parentid', $groups, array('disabled' => 'disabled'));
	}else
	{
	    echo $form->dropDownListRow($model, 'parentid', $groups);
	}
	?>

	<?php // описание мероприятия
	echo $form->labelEx($model, 'description'); 
        $this->widget('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
    	'model' => $model,
    	'attribute' => 'description',
    	'options' => array(
    		'lang'    => 'ru',
            ),
        ));
    echo $form->error($model,'description');
    echo '<div class="alert">Видно всем</div>';
    ?>
    
    <?php // Дополнительная информация для участников
	echo $form->labelEx($model,'memberinfo'); 
        $this->widget('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
    	'model' => $model,
    	'attribute' => 'memberinfo',
    	'options' => array(
    		'lang'    => 'ru',
            ),
        ));
    echo $form->error($model,'memberinfo');
    echo '<div class="alert">Отображается только подтвержденным участникам</div>';
    ?>
    
    <?php // нужно создать мероприятие без даты (она пока неизвестна)
    // @todo выключать даты начала и окончания при установке этой галочки
    echo $form->checkBoxRow($model, 'nodates');
    ?>
    
	<?php // время начала
        // @todo заменить даты начала и окончания на более удобный виджет (на вариант с диапазоном): 
        //       http://jonthornton.github.io/jquery-timepicker/
	    echo $form->labelEx($model, 'timestart');
	    $defaultStart = time();
	    if ( $model->timestart )
	    {
	        $defaultStart = $model->timestart;
	    }
	    $this->widget('ext.CJuiDateTimePicker.CJuiDateTimePicker',array(
	        //'model'=>$model,
            //'flat' => true,
	        //'attribute'=>'timestart',
            'name' => 'ProjectEvent[timestart]',
            'value' => $dateFormatter->format('dd.MM.yyyy HH:mm', $defaultStart),
	        'options'=>array(
	            'showAnim'=>'fold',
	        ),
	    ));
        echo $form->error($model,'timestart');
	?>

	<?php // время окончания
	    echo $form->labelEx($model, 'timeend');
	    $defaultEnd = time();
	    if ( $model->timeend )
	    {
	        $defaultEnd = $model->timeend;
	    }
	    $this->widget('ext.CJuiDateTimePicker.CJuiDateTimePicker',array(
	        //'model'=>$model,
            //'flat' => true,
	        //'attribute'=>'timeend',
            'name' => 'ProjectEvent[timeend]',
            'value' => $dateFormatter->format('dd.MM.yyyy HH:mm', $defaultEnd),
	        'options'=>array(
	            'showAnim'=>'fold',
	        ),
	    ));
        echo $form->error($model,'timeend');
	?>
	
	<?php // время сбора
	    // @todo заменить этот виджет на более удобный: http://amsul.ca/pickadate.js/time.htm#formats
	    echo $form->labelEx($model, 'eta');
	    $this->widget('ext.ETinyTimePicker.ETinyTimePicker', array(
	        'model'     => $model,
	        'attribute' => 'eta',
	    ));
        echo $form->error($model,'eta');
	?>
	
	<?php // Описание места встречи
	echo $form->labelEx($model,'meetingplace'); 
        $this->widget('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
    	'model' => $model,
    	'attribute' => 'meetingplace',
    	'options' => array(
    		'lang'    => 'ru',
            ),
        ));
    echo $form->error($model, 'meetingplace');
    echo '<div class="alert">Отображается только подтвержденным участникам</div>';
    ?>
	
	<?php // показывать ли время начала съемок?
	    $showTimeStartOptions = array();
	    if ( $model->isNewRecord )
	    {
	        $showTimeStartOptions['checked'] = false;
	    }
	    echo $form->checkBoxRow($model, 'showtimestart');
	    echo '<div class="alert">Если галочка поставлена - участникам покажется и время встречи, и время съемок.
                Если снята - только время сбора.</div>';
	?>
	
	<?php // @todo убрать размер оплаты
	    //echo $form->textFieldRow($model, 'salary', array('class'=>'span5','maxlength'=>32));
	?>
	
	<?php
    echo '<div>Фотогалерея</div>';
    if ( $model->photoGalleryBehavior->getGallery() === null )
    {
        echo '<div class="alert">Нужно сохранить мероприятие перед загрузкой фотографий</div>';
    }else
    {
        $this->widget('GalleryManager', array(
             'gallery' => $model->photoGalleryBehavior->getGallery(),
             'controllerRoute' => '/admin/gallery'
        ));
    }
    ?>

	<?php // echo $form->textFieldRow($model,'addressid',array('class'=>'span5','maxlength'=>11)); ?>
	<?php $this->widget('bootstrap.widgets.TbButton', array(
		'buttonType'=>'submit',
		'type'=>'primary',
		'label'=>$model->isNewRecord ? 'Создать' : 'Сохранить',
	)); ?>

<?php $this->endWidget(); ?>
