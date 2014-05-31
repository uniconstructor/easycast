<?php
/**
 * Страница редактирования анкеты
 */
/* @var $this QuestionaryController */
/* @var $questionary Questionary */


$this->breadcrumbs = array(
    CatalogModule::t('catalog') => array('/catalog'),
	$user->fullname  => array(Yii::app()->getModule('questionary')->profileUrl, 'id' => $questionary->id),
	Yii::t('coreMessages', 'edit'),
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
    ));
    ?>
</div>