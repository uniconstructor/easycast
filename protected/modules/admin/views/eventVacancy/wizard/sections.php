<?php
/**
 * Шаг создания и выбора разделов заявки
 */
/* @var $this    EventVacancyController */
/* @var $vacancy EventVacancy */
/* @var $form    TbActiveForm */
?>
<div class="row-fluid">
    <div class="span6">
        <div class="row-fluid">
            <div class="title-page">
                <h2>Создать новую группу</h2>
                <h4 class="title-description">
                    Создайте новую группу для заявок если ни одна из существующих вам не подходит.
                </h4>
            </div>
            <?php 
            // Доступный список разделов (чтобы добавлять новые варианты если не хватает)
            $this->widget('admin.extensions.EditCategories.EditCategories', array(
                'parentId'   => 4,
            ));
            ?>
        </div>
        <div class="row-fluid">
            <div class="title-page">
                <h2>Используемые категории</h2>
                <h4 class="title-description">
                    Из этих списков берутся группы, по которым потом разбиваются заявки.
                </h4>
            </div>
            <?php 
            // Список групп разделов, которые используются в этом проекте
            $this->widget('admin.extensions.EditCategoryInstances.EditCategoryInstances', array(
                'objectType' => 'vacancy',
                'objectId'   => $vacancy->id,
                'parentId'   => 4,
            ));
            ?>
        </div>
    </div>
</div>