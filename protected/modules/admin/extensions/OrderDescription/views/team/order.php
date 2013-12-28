<?php
/**
 * Текст для команды для в письме-оповещении для обычного заказа 
 */
/* @var $this EMailOrder */

$comment = '';
if ( $this->order->comment )
{
    $comment = 'Комментарий к заказу: '. $this->order->comment;
}
?>
На сайт поступил новый заказ.<br>
Его номер <b><?= $this->order->id; ?></b>.<br>
<br>
Контактная информация:<br>
<ul>
    <li>Имя: <?= $this->order->name; ?></li>
    <li>Телефон: +7 <?= $this->order->phone; ?></li>
    <li>email: <?= $this->order->email; ?></li>
</ul>
<p><?= $comment; ?></p>
Нужно связаться с заказчиком и уточнить детали, после чего сразу же закрыть задачу в Мегаплане.
