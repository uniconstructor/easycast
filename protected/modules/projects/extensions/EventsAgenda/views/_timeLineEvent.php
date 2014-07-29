<?php
/**
 * Отображение одного события в списке
 */
/* @var $event     ProjectEvent */
/* @var $vacancies EventVacancy[] */
?>
<div class="row-fluid" style="line-height:1.1em;">
    <div class="span9" style="font-size:1em;">
        <?= $event->description; ?>
    </div>
    <div class="span3">
        <?php
        foreach ( $vacancies as $vacancy )
        {
            $this->render('_timeLineVacancy', array(
                'vacancy' => $vacancy,
            ));
        }
        ?>
    </div>
</div>
