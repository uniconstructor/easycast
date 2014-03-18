<?php
/**
 * Список онлайн-сервисов с описанием
 */
/* @var $this EMailOffer */

$headerLinkOptions = array('style' => 'text-decoration:none;color:#2B6893;font-weight:400;');
$imageOptions      = array('style' => 'max-width:120px;');
$textStyle         = 'font-face:tahoma,sans-serif;color:#17414c;text-align:justify;font-weight:200;background-color:#fff;border-radius:10px;padding:8px;';

$searchImage  = CHtml::image($this->getImageUrl('/images/offer/services/serv1.png'), 'Поиск', $imageOptions);
$searchIcon   = CHtml::link($searchImage, $this->getSearchPageUrl());
$searchHeader = CHtml::link('Поиск по 25 критериям и 15 разделам', $this->getSearchPageUrl(), $headerLinkOptions);

$orderImage  = CHtml::image($this->getImageUrl('/images/offer/services/serv2.png'), 'Заказ', $imageOptions);
$orderIcon   = CHtml::link($orderImage, $this->getOrderPageUrl());
$orderHeader = CHtml::link('Заказ через персонального менеджера', $this->getOrderPageUrl(), $headerLinkOptions);

$castingImage  = CHtml::image($this->getImageUrl('/images/offer/services/serv3.png'), 'Кастинг', $imageOptions);
$castingIcon   = CHtml::link($castingImage, $this->getCastingPageUrl());
$castingHeader = CHtml::link('Онлайн кастинг', $this->getCastingPageUrl(), $headerLinkOptions);

$docsImage  = CHtml::image($this->getImageUrl('/images/offer/services/serv4.png'), 'Документооборот', $imageOptions);
$docsIcon   = $docsImage;
$docsHeader = CHtml::openTag('span', $headerLinkOptions).'Автоматизация документооборота'.CHtml::closeTag('span');
?>
<table class="w640" width="640" border="0" cellpadding="0" cellspacing="0">
<tbody>
	<tr>
		<td width="120" valign="middle"><?= $searchIcon; ?></td>
		<td width="10">&nbsp;</td>
		<td width="500" height="100" valign="middle">
            <h3><?= $searchHeader; ?></h3>
			<p style="<?= $textStyle; ?>">
			Ежедневно в нашей системе регистрируется множество актеров, моделей, артистов, ведущих, 
			танцоров, музыкантов и вокалистов, ведь мы предлагаем действительно удобный и надежный сервис. 
			Для удобства поиска все анкеты автоматически распределяются по 15 разделам с вкладками и фильтрами. 
			Автоматически обновляемый расширенный поиск быстро и легко найдет нужного артиста с вашей помощью: 
			просто укажите от 1 до 25 критериев поиска и найти самый сложный типаж станет легко! 
            </p>
		</td>
		<td width="10">&nbsp;</td>
	</tr>
	<tr>
		<td width="120" valign="middle"><?= $orderIcon; ?></td>
		<td width="10">&nbsp;</td>
		<td width="500" height="100" valign="middle">
            <h3><?= $orderHeader; ?></h3>
			<p style="<?= $textStyle; ?>">
			Этот сервис создан специально для режиссеров, продюсеров и кастинг-директоров, 
			которые предпочитают делегировать все кастинг-задачи и контролировать лишь конечный 
			результат работы в своем личном кабинете на нашем сайте. Для вашего удобства и 
			спокойствия - наши лучшие руководители проектов. Просто выбираете для своего проекта 
			персонального менеджера и заполняете короткую форму - остальное сделаем мы! 
            </p>
		</td>
		<td width="10">&nbsp;</td>
	</tr>
	<tr>
		<td width="120" valign="middle"><?= $castingIcon; ?></td>
		<td width="10">&nbsp;</td>
		<td width="500" height="100" valign="middle">
            <h3><?= $castingHeader; ?></h3>
			<p style="<?= $textStyle; ?>">
			Это новый, удобный и современный формат проведения кастинга. 
			Если вы цените свое время, то онлайн кастинг станет для вас лучшим помощником. 
			Вы можете провести полноценный кастинг не выходя из дома, офиса или стоя в пробке. 
			Нужно заполнить заявку на проведение онлайн-кастинга и ввести все необходимые сведения 
			о проекте. Система автоматически оповестит всех подходящих по параметрам пользователей, 
			получит от каждого видеоролик, и после этого предложит вам просмотреть и отобрать заявки. 
            </p>
		</td>
		<td width="10">&nbsp;</td>
	</tr>
	<tr>
		<td width="120" valign="middle"><?= $docsIcon; ?></td>
		<td width="10">&nbsp;</td>
		<td width="500" height="100" valign="middle">
            <h3><?= $docsHeader; ?></h3>
			<p style="<?= $textStyle; ?>">
			Наша умная система на основе сформированного заказа система автоматически генерирует 
			весь пакет документов, включая договор, ведомости, смету и даже фотовызывной, 
			содержащий всю необходимую информацию об актерах, утвержденных заказчиком. 
            </p>
		</td>
		<td width="10">&nbsp;</td>
	</tr>
</tbody>
</table>