<?php
/**
 * 
 */
/* @var $this    EventVacancyController */
/* @var $vacancy EventVacancy */
/* @var $form    TbActiveForm */
?>
<div class="row-fluid">
    <div class="span6">
        <div class="row-fluid">
            <div class="title-page">
                <h2>Группы заявок</h2>
                <h4 class="title-description">
                    По этим разделам нужно будет распределить поступившие заявки. 
                    Каждая заявка может быть помещена сразу в несколько разделов.
                </h4>
            </div>
            <?php
            // Список разделов анкет, которые используются в этом проекте
            $noSectionInstances = $this->widget('ext.ECMarkup.ECAlert.ECAlert', array(
                'type'    => 'info',
                'message' => 'Перед добавлением разделов добавьте хотя бы одну слева и обновите страницу.',
            ), true);
            $this->widget('admin.extensions.EditSectionInstances.EditSectionInstances', array(
                'objectType' => 'vacancy',
                'objectId'   => $vacancy->id,
                'categories' => $vacancy->sectionCategories,
                'emptyText'  => $noSectionInstances,
            ));
            ?>
        </div>
    </div>
    
</div>