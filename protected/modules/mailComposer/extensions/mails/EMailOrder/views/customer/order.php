<?php
/**
 * Текст для клиента для в письме-подтверждении обычного заказа 
 */
/* @var $this EMailCustomerOrder */

$comment = '';
if ( $this->order->comment )
{
    $comment = 'Комментарий к заказу: '. $this->order->comment;
}
?>
Добрый день.<br><br>
Мы получили ваш заказ и скоро свяжемся с вами, чтобы подтвердить его.<br>
Номер заказа: <b><?= $this->order->id; ?></b>.<br>
<br>
Пожалуйста, проверьте правильность введенных вами данных:<br>
<ul>
    <li>Ваше имя: <?= $this->order->name; ?></li>
    <li>Телефон: <?= $this->order->phone; ?></li>
</ul>
<p><?= $comment; ?></p>