<?php
/**
 * Текст для команды для в письме-оповещении о срочном заказе 
 */
/* @var $this EMailCustomerOrder */
 
$comment = '';
if ( $this->order->comment )
{
    $comment = 'Комментарий к заказу: '. $this->order->comment;
}
?>
На сайте оформлен срочный заказ.<br>
Номер заказа: <b><?= $this->order->id; ?></b>.<br>
<br>
Контактная информация:<br>
<ul>
    <li>Имя: <?= $this->order->name; ?></li>
    <li>Телефон: <?= $this->order->phone; ?></li>
    <li>email: <?= $this->order->email; ?></li>
</ul>
<p><?= $comment; ?></p>