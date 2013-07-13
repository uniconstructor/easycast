<?php
/* @var $this QuestionaryController */
/* @var $model Questionary */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'userid'); ?>
		<?php echo $form->textField($model,'userid',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'mainpictureid'); ?>
		<?php echo $form->textField($model,'mainpictureid',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'firstname'); ?>
		<?php echo $form->textField($model,'firstname',array('size'=>60,'maxlength'=>128)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'lastname'); ?>
		<?php echo $form->textField($model,'lastname',array('size'=>60,'maxlength'=>128)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'middlename'); ?>
		<?php echo $form->textField($model,'middlename',array('size'=>60,'maxlength'=>128)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'birthdate'); ?>
		<?php echo $form->textField($model,'birthdate'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'gender'); ?>
		<?php echo $form->textField($model,'gender',array('size'=>6,'maxlength'=>6)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'timecreated'); ?>
		<?php echo $form->textField($model,'timecreated',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'timefilled'); ?>
		<?php echo $form->textField($model,'timefilled',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'timemodified'); ?>
		<?php echo $form->textField($model,'timemodified',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'height'); ?>
		<?php echo $form->textField($model,'height',array('size'=>3,'maxlength'=>3)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'weight'); ?>
		<?php echo $form->textField($model,'weight',array('size'=>3,'maxlength'=>3)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'wearsizemin'); ?>
		<?php echo $form->textField($model,'wearsizemin',array('size'=>2,'maxlength'=>2)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'wearsizemax'); ?>
		<?php echo $form->textField($model,'wearsizemax',array('size'=>2,'maxlength'=>2)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'shoessize'); ?>
		<?php echo $form->textField($model,'shoessize',array('size'=>2,'maxlength'=>2)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'city'); ?>
		<?php echo $form->textField($model,'city',array('size'=>60,'maxlength'=>128)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'cityid'); ?>
		<?php echo $form->textField($model,'cityid'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'mobilephone'); ?>
		<?php echo $form->textField($model,'mobilephone',array('size'=>32,'maxlength'=>32)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'homephone'); ?>
		<?php echo $form->textField($model,'homephone',array('size'=>32,'maxlength'=>32)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'addphone'); ?>
		<?php echo $form->textField($model,'addphone',array('size'=>32,'maxlength'=>32)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'vkprofile'); ?>
		<?php echo $form->textField($model,'vkprofile',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'looktype'); ?>
		<?php echo $form->textField($model,'looktype'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'haircolor'); ?>
		<?php echo $form->textField($model,'haircolor'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'eyecolor'); ?>
		<?php echo $form->textField($model,'eyecolor'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'physiquetype'); ?>
		<?php echo $form->textField($model,'physiquetype'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'isactor'); ?>
		<?php echo $form->textField($model,'isactor'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'hasfilms'); ?>
		<?php echo $form->textField($model,'hasfilms'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'isemcee'); ?>
		<?php echo $form->textField($model,'isemcee'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'isparodist'); ?>
		<?php echo $form->textField($model,'isparodist'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'istwin'); ?>
		<?php echo $form->textField($model,'istwin'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'ismodel'); ?>
		<?php echo $form->textField($model,'ismodel'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'titsize'); ?>
		<?php echo $form->textField($model,'titsize',array('size'=>2,'maxlength'=>2)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'chestsize'); ?>
		<?php echo $form->textField($model,'chestsize',array('size'=>3,'maxlength'=>3)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'waistsize'); ?>
		<?php echo $form->textField($model,'waistsize',array('size'=>3,'maxlength'=>3)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'hipsize'); ?>
		<?php echo $form->textField($model,'hipsize',array('size'=>3,'maxlength'=>3)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'isdancer'); ?>
		<?php echo $form->textField($model,'isdancer'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'dancerlevel'); ?>
		<?php echo $form->textField($model,'dancerlevel',array('size'=>12,'maxlength'=>12)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'hasawards'); ?>
		<?php echo $form->textField($model,'hasawards'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'isstripper'); ?>
		<?php echo $form->textField($model,'isstripper'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'striptype'); ?>
		<?php echo $form->textField($model,'striptype',array('size'=>7,'maxlength'=>7)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'striplevel'); ?>
		<?php echo $form->textField($model,'striplevel',array('size'=>12,'maxlength'=>12)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'issinger'); ?>
		<?php echo $form->textField($model,'issinger'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'singlevel'); ?>
		<?php echo $form->textField($model,'singlevel',array('size'=>12,'maxlength'=>12)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'voicetimbre'); ?>
		<?php echo $form->textField($model,'voicetimbre'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'ismusician'); ?>
		<?php echo $form->textField($model,'ismusician'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'musicianlevel'); ?>
		<?php echo $form->textField($model,'musicianlevel',array('size'=>12,'maxlength'=>12)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'issportsman'); ?>
		<?php echo $form->textField($model,'issportsman'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'isextremal'); ?>
		<?php echo $form->textField($model,'isextremal'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'isathlete'); ?>
		<?php echo $form->textField($model,'isathlete'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'hasskills'); ?>
		<?php echo $form->textField($model,'hasskills'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'hastricks'); ?>
		<?php echo $form->textField($model,'hastricks'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'haslanuages'); ?>
		<?php echo $form->textField($model,'haslanuages'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'wantsbusinesstrips'); ?>
		<?php echo $form->textField($model,'wantsbusinesstrips'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'country'); ?>
		<?php echo $form->textField($model,'country',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'countryid'); ?>
		<?php echo $form->textField($model,'countryid',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'hasinshurancecard'); ?>
		<?php echo $form->textField($model,'hasinshurancecard'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'inshurancecardnum'); ?>
		<?php echo $form->textField($model,'inshurancecardnum',array('size'=>60,'maxlength'=>128)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'hasforeignpassport'); ?>
		<?php echo $form->textField($model,'hasforeignpassport'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'passportexpires'); ?>
		<?php echo $form->textField($model,'passportexpires',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'passportserial'); ?>
		<?php echo $form->textField($model,'passportserial',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'passportnum'); ?>
		<?php echo $form->textField($model,'passportnum',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'passportdate'); ?>
		<?php echo $form->textField($model,'passportdate',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'passportorg'); ?>
		<?php echo $form->textField($model,'passportorg',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'addressid'); ?>
		<?php echo $form->textField($model,'addressid',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'policyagreed'); ?>
		<?php echo $form->textField($model,'policyagreed'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'status'); ?>
		<?php echo $form->textField($model,'status',array('size'=>12,'maxlength'=>12)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'encrypted'); ?>
		<?php echo $form->textField($model,'encrypted'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->