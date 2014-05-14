<?php
/**
 * Страница редактирования анкеты
 */
/* @var $this QuestionaryController */
/* @var $questionary Questionary */


$this->breadcrumbs = array(
    CatalogModule::t('catalog') => array('/catalog'),
	$user->fullname  => array(Yii::app()->getModule('questionary')->profileUrl, 'id' => $questionary->id),
	Yii::t('coreMessages','edit'),
);

?>

<div class="container">
    <h1><?= QuestionaryModule::t('update_questionary'); ?></h1>
    <?php 
    echo $this->renderPartial('_form', array(
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
</div>