<?php
/**
 * Текст для команды при отправке заявки на расчет стоимости
*/
/* @var $this OrderDescription */
?>
К нам поступила новая заявка на расчет стоимости.<br>
Данные заявки:<br>
<ul>
    <li>Проект: <?= $orderData['projectname']; ?></li>
    <li>Тип проекта: <?= Project::model()->getTypetext($orderData['projecttype']); ?></li>
    <li>Кто требуется: <?= $orderData['categories']; ?></li>
    <?= $planDate; ?>
    <?= $daysNum; ?>
    <?= $duration; ?>
    <li><?= $eventTime; ?></li>
</ul>
Контактная информация:
<ul>
    <li>ФИО: <?= $orderData['name']; ?> <?= $orderData['lastname']; ?></li>
    <li>email: <?= $orderData['email']; ?></li>
    <li>Телефон: <?= $orderData['phone']; ?></li>
</ul>
<?= $comment; ?>