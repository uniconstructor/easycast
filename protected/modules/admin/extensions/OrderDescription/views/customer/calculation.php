<?php
/**
 * Текст для клиента при отправке заявки на расчет стоимости
*/
/* @var $this OrderDescription */
?>
Ваша заявка на расчет стоимости принята.<br>
Если нам потребуется дополнительная информация - мы свяжемся с вами по указанным в заявке контактам.<br>
Расчет стоимости будет выслан вам на этот адрес электронной почты, как только будет готов.<br>
<br>
Ваша заявка:<br>
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