<?php
/**
 * Фрагмент страницы отображения одного отчета (заголовок с общими данными)
 * @var $this ReportView
 */
?>
<h2><?= $this->report->name; ?></h2>
<h3><?= Yii::app()->getDateFormatter()->format('d MMM HH:mm', $this->report->timemodified); ?></h3>