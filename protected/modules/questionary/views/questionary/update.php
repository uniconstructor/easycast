<?php
/* @var $this QuestionaryController */
/* @var $questionary Questionary */


$this->breadcrumbs=array(
    CatalogModule::t('catalog') => array('/catalog'),
	$user->fullname  => array(Yii::app()->getModule('questionary')->profileUrl, 'id' => $questionary->id),
	Yii::t('coreMessages','edit'),
);

/*
$this->menu=array(
	array('label'=>QuestionaryModule::t('list_questionary'), 'url'=>array('index')),
	array('label'=>QuestionaryModule::t('view_questionary'), 'url'=>array('view', 'id' => $questionary->id)),
	// array('label'=>QuestionaryModule::t('manage_questionary'), 'url'=>array('admin')),
);*/
?>

<h1><?php echo QuestionaryModule::t('update_questionary'); ?></h1>

<?php echo $this->renderPartial('_form', array(
          'questionary'         => $questionary,
          'address'             => $address,
          'user'                => $user,
          'recordingConditions' => $recordingConditions,

          // сложные значения
          // Телеведущий
          'tvShow'           => $tvShow,
          'validatedTvShows' => $validatedTvShows,
          // Работа фотомоделью
          'photoModelJob'           => $photoModelJob,
          'validatedPhotoModelJobs' => $validatedPhotoModelJobs,
          // Работа промо-моделью
          'promoModelJob'           => $promoModelJob,
          'validatedPromoModelJobs' => $validatedPromoModelJobs,
          // Стили танца
          'danceType'           => $danceType,
          'validatedDanceTypes' => $validatedDanceTypes,
          // Музыкальные инструменты
          'instrument'           => $instrument,
          'validatedInstruments' => $validatedInstruments,
          // Звания, призы и награды
          'award'           => $award,
          'validatedAwards' => $validatedAwards,
          // Работа в театре
          'actorTheatre'           => $actorTheatre,
          'validatedActorTheatres' => $validatedActorTheatres,
     ));
?>