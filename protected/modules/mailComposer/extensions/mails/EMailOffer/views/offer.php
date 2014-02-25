<?php
/**
 * Верстка коммерческого предложения в письме
 * 
 */
/* @var $this EMailOffer */

/**
 * Текст приветствия для заказчика: 
 * Михаил, здравствуйте.
 * Меня зовут Маруся Нилсен, я руководитель проектов кастингового агенства easyCast.
 * Мы предоставляем широкий спектр услуг - от букинга западных звезд до артистов массовых сцен.
 * Прошу вас рассмотреть наше коммерческое предложение и при желании оценить нашу систему easycast.ru.
 * [Мы работаем "на голову выше" своих конкурентов благодаря интенсивному развитию автоматизации и процессов 
 * командообразования, поэтому с удовольствием удивим вас действительно качественной работой! ]
 */
?>
<tr bgcolor="#dbe4f1">
    <td id="header" align="center" width="640px">
		<div style="text-align: left" align="center">
			<a href="<?= Yii::app()->createAbsoluteUrl('/sale'); ?>" target="_blank">
			<img id="customHeaderImage" src="<?= $this->getImageUrl('/images/mail-header.png'); ?>" 
                class="w640" style="display: inline" align="top" border="0" width="640">
			</a>
		</div>
    </td>
</tr>
<!-- Вступление -->
<tr bgcolor="#dbe4f1">
    <td>
    <table width="640px" border="0" cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
            <td width="30">&nbsp;</td>
            <td width="580">
                <p style="text-align:center;font-weight:200;text-shadow: 0 1px 2px #E8F9FF;">
                <?= $greeting ?>
                Кастинговое агентство easyCast с 2005 года успешно обеспечивает актерами, моделями, 
                типажами и артистами массовых сцен самые масштабные и сложные съемки. 
                Мы оказываем качественные услуги по поиску, отбору и администрированию всех вышеперечисленных 
                персонажей для производителей рекламы, кино, телепроектов, сериалов и всех других видов 
                аудиовизуальной продукции. С помощью современных технологий мы создали мощнейшие инструменты 
                кастинга, и даже самые сложные задачи с нами решаются быстро и удобно!
                </p>
            </td>
            <td width="30">&nbsp;</td>
        </tr>
        </tbody>
    </table>
    </td>
</tr>
<!-- Слоган -->
<tr bgcolor="#dbe4f1">
	<td>
		<div style="text-align: left" align="center">
			<img src="<?= $this->getImageUrl('/images/offer/slogan.png'); ?>" class="w640" style="display:inline;" align="top" border="0" width="640">
		</div>
	</td>
</tr>
<!-- Услуги -->
<tr bgcolor="#dbe4f1">
	<td>
	<?php $this->render('_services'); ?>
	</td>
</tr>
<tr bgcolor="#dbe4f1">
	<td height="20"></td>
</tr>
<!-- Кнопки заказа и расчета стоимости -->
<tr bgcolor="#dbe4f1">
	<td>
		<?php $this->render('_buttons'); ?>
	</td>
</tr>
<!-- Сервисы -->
<tr bgcolor="#dbe4f1">
	<td width="640" height="40" valign="middle" align="center">
		<h2 style="color:#1E3D52;font-family:century gothic,sans-serif;font-weight:400;font-size:25px;">
		ВАШИ ОНЛАЙН-СЕРВИСЫ</h2>
	</td>
</tr>
<tr bgcolor="#dbe4f1">
    <td>
        <!-- Текст про сервисы -->
    	<table class="w640" width="640" border="0" cellpadding="0" cellspacing="0">
		<tbody>
			<tr>
				<td width="30">&nbsp;</td>
				<td width="580" height="100" width="520" valign="middle">
					<p style="font-face:century gothic,sans-serif;color:#17414c;text-align:center;font-weight:200;text-shadow: 0 1px 2px #E8F9FF;">
					Мы рады вам сообщить, что благодаря восьмилетнему опыту работы
                    и двум годам сложнейших IT-разработок мы создали ресурс,
                    способный упорядочить все сложные процессы поиска, оповещения,
                    отбора и утверждения артистов в простой сервис:
                    несколько кликов - и все, кто вам нужен - в кадре!
                    </p>
				</td>
				<td width="30">&nbsp;</td>
			</tr>
		</tbody>
    	</table>
    	<!-- Список сервисов с описанием -->
    	<?php $this->render('_online'); ?>
	</td>
</tr>
<!-- Кнопки заказа и расчета стоимости -->
<tr bgcolor="#dbe4f1">
	<td>
	<?php $this->render('_buttons'); ?>
	</td>
</tr>
<!-- отзывы -->
<tr bgcolor="#dbe4f1">
	<td>
		<table class="w640" width="640" border="0" cellpadding="0" cellspacing="0">
		<tbody>
			<tr>
                <td width="10">&nbsp;</td>
				<td width="120"><?= CHtml::image($this->getImageUrl('/images/offer/reviews/7.jpg'), '', 
				    array('style' => 'width:130px;box-shadow:0 0 5px 3px rgba(0, 0, 0, 0.35);border-radius:10px;')) ?></td>
			    <td width="10">&nbsp;</td>
				<td width="460" height="100" width="520" valign="middle">
				    <h3><span style="color:#1E3D52;font-family:century gothic,sans-serif;font-weight:400;font-size:25px;">
				    Тина Канделаки
				    </span>
				    <span style="color:#1E3D52;font-family:century gothic,sans-serif;font-weight:200;font-size:20px;">
				    журналистка, телеведущая, продюсер, общественный деятель
				    </span>
				    </h3>
					<p style="font-face:century gothic,sans-serif;color:#17414c;text-align:center;font-weight:200;text-shadow: 0 1px 2px #E8F9FF;">
					Я знаю easyCast очень давно, и являюсь свидетелем их карьеры. Это очень приятно, 
					так как они развивались на моих глазах и доросли до компании, которой можно доверить 
					под ключ организовать огромный процесс. Очень мало в наше время людей, с навыками делать 
					что-то эффективно и доводить дело до конца, взять и качественно выполнить все 
					поставленные задачи. Команда easyCast это умеет. Я это давно наблюдаю, давно это вижу и, 
					более того, я с easyCast работаю.
                    </p>
				</td>
				<td width="30">&nbsp;</td>
			</tr>
		</tbody>
    	</table>
	</td>
</tr>
<!-- Проекты -->
<tr bgcolor="#dbe4f1">
	<td>
	<?php $this->render('_projects'); ?>	
	</td>
</tr>