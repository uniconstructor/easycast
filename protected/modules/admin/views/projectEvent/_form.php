<?php 
/**
 * Форма редактирования мероприятия в админке
 * 
 * @todo указывать адрес вместо текстового поля "место проведения"
 */
/* @var $this  ProjectEventController */
/* @var $model ProjectEvent */
/* @var $form  TbActiveForm */

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id' => 'project-event-form',
	'enableAjaxValidation' => false,
)); 
$dateFormatter = new CDateFormatter('ru');
?>
	<?php 
	// ошибки формы
	echo Yii::t('coreMessages','form_required_fields', array('{mark}' => '<span class="required">*</span>'));
	echo $form->errorSummary($model);
	
	// название события
	echo $form->textFieldRow($model, 'name', array('class' => 'span5', 'maxlength' => 255)); 
	
	// тип мероприятия
	$eventTypes = $model->getTypes();
	echo $form->dropDownListRow($model, 'type', $eventTypes);
	
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
	{// черновики можно
	    echo $form->dropDownListRow($model, 'parentid', $groups);
	}
	
	// описание мероприятия
	echo $form->redactorRow($model, 'description', array(), array(
        'hint' => 'Видно всем',
    ));
    // Дополнительная информация для участников
    echo $form->redactorRow($model, 'memberinfo', array(), array(
        'hint' => 'Отображается только подтвержденным участникам',
    ));
    // нужно создать мероприятие без даты (она пока неизвестна)
    // @todo выключать даты начала и окончания при установке этой галочки
    echo $form->checkBoxRow($model, 'nodates');

    // время начала
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

	// время окончания
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

	// время сбора
    // @todo заменить этот виджет на более удобный: http://amsul.ca/pickadate.js/time.htm#formats
    echo $form->labelEx($model, 'eta');
    $this->widget('ext.ETinyTimePicker.ETinyTimePicker', array(
        'model'     => $model,
        'attribute' => 'eta',
    ));
    echo $form->error($model,'eta');

	// Описание места встречи
	echo $form->redactorRow($model, 'meetingplace', array(), array(
	    'hint' => 'Отображается только подтвержденным участникам',
	));
    //echo '<div class="alert">Отображается только подтвержденным участникам</div>';

    // показывать ли время начала съемок?
    $showTimeStartOptions = array();
    if ( $model->isNewRecord )
    {
        $showTimeStartOptions['checked'] = false;
    }
    echo $form->checkBoxRow($model, 'showtimestart');
    echo '<div class="alert">Если галочка поставлена - участникам покажется и время встречи, и время съемок.
                Если снята - только время сбора.</div>';
	
    // Фотографии
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
    
    // кнопка сохранения данных
	$this->widget('bootstrap.widgets.TbButton', array(
		'buttonType'=>'submit',
		'type'=>'primary',
		'label'=>$model->isNewRecord ? 'Создать' : 'Сохранить',
	));
    ?>

<?php $this->endWidget(); ?>
