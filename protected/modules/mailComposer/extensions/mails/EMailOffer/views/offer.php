<?php
/**
 * Вся верстка вертикального коммерческого предложения в одном файле
 */
/* @var $this EMailOffer */
?>
<tr bgcolor="#dbe4f1">
    <td id="header" class="w640" align="center" width="640" >
		<div style="text-align: left" align="center">
			<a href="<?= $orderUrl; ?>" target="_blank">
			<img id="customHeaderImage" src="<?= $this->getImageUrl('/images/offer/header.png'); ?>" class="w640" style="display: inline" align="top" border="0" width="640">
			</a>
		</div>
    </td>
</tr>
<tr bgcolor="#dbe4f1">
	<td>
		<!-- Слоган -->
		<div style="text-align: left" align="center">
			<img src="<?= $this->getImageUrl('/images/offer/slogan.png'); ?>" class="w640" style="display: inline" align="top" border="0" width="640">
		</div>
	</td>
</tr>
<tr bgcolor="#dbe4f1">
	<td>
		<!-- Услуги -->
		<table class="w640" width="640" border="0" cellpadding="0" cellspacing="0">
			<tbody>
				<tr style="vertical-align:bottom;">
					<td width="45" height="104" rowspan="2"></td>
					<td width="120">
                        <a href="<?= $this->getSectionUrl('actors'); ?>">
                            <img src="<?= $this->getImageUrl('/images/offer/s1.png'); ?>" width="120">
                        </a>
					</td>
					<td width="23" rowspan="2"></td>
					<td width="120">
					   <a href="<?= $this->getSectionUrl('ams'); ?>">
					       <img src="<?= $this->getImageUrl('/images/offer/s2.png'); ?>"  width="120" />
				       </a>
					</td>
					<td width="23" height="176" rowspan="2"></td>
					<td width="120">
                        <a href="<?= $this->getSectionUrl('models'); ?>">
                            <img src="<?= $this->getImageUrl('/images/offer/s3.png'); ?>" width="120">
                        </a>
					</td>
					<td width="23" height="176" rowspan="2"></td>
					<td width="120">
						<a href="<?= $this->getSectionUrl('types'); ?>">
                            <img src="<?= $this->getImageUrl('/images/offer/s4.png'); ?>"  width="120" />
						</a>
					</td>
					<td width="40" rowspan="2"></td>
				</tr>
				<tr valign="center">
					<td width="119" height="30" bgcolor="#47ad2c" align="center">
						<a href="<?= $this->getSectionUrl('actors'); ?>" style="text-decoration:none;">
						<font face="tahoma,sans-serif" color="#224c17" size="4">АКТЕРЫ</font></a>
					</td>
					<td width="120" height="30" bgcolor="#c87c1a" align="center">
						<a href="<?= $this->getSectionUrl('ams'); ?>" style="text-decoration:none;">
						<font face="tahoma,sans-serif" color="#65390d" size="2">АРТИСТЫ МАССОВЫХ СЦЕН</font></a>
					</td>
					<td width="120" height="30" bgcolor="#257cb7" align="center">
						<a href="<?= $this->getSectionUrl('models'); ?>" style="text-decoration:none;">
						<font face="tahoma,sans-serif" color="#09354f" size="4">МОДЕЛИ</font></a>
					</td>
					<td width="119" height="30" bgcolor="#7c2fce" align="center">
						<a href="<?= $this->getSectionUrl('types'); ?>" style="text-decoration:none;">
						<font face="tahoma,sans-serif" color="#2b0137" size="4">ТИПАЖИ</font></a>
					</td>
				</tr>
			</tbody>
		</table>
	</td>
</tr>
<tr bgcolor="#dbe4f1">
	<td height="20"></td>
</tr>
<tr bgcolor="#dbe4f1">
	<td>
		<!-- Кнопки -->
		<table class="w640" width="640"  border="0" cellpadding="0" cellspacing="0">
			<tbody>
				<tr>
					<td width="151" height="206" rowspan="3">
						<img src="<?= $this->getImageUrl('/images/offer/vizitka.png'); ?>">
					</td>
					<td width="339" height="68">
						<a href="<?= $orderUrl; ?>">
                            <img src="<?= $this->getImageUrl('/images/offer/zakaz.png'); ?>">
						</a>
					</td>
					<td width="151" rowspan="3">
						<img src="<?= $this->getImageUrl('/images/offer/ipad1.png'); ?>">
					</td>
				</tr>
				<tr>
					<td width="339" height="56">
						<a href="<?= $calculationUrl; ?>">
                            <img src="<?= $this->getImageUrl('/images/offer/price.png'); ?>">
						</a>
					</td>
				</tr>
				<tr>
					<td width="339" height="82">
						<a href="<?= $onlineCastingUrl; ?>">
                            <img src="<?= $this->getImageUrl('/images/offer/online.png'); ?>">
                        </a>
					</td>
				</tr>
			</tbody>
		</table>
	</td>
</tr>
<tr bgcolor="#dbe4f1">
	<td width="640" height="40" valign="middle" align="center">
		<!-- Текст про сервисы -->
		<font face="tahom,sans-serif" color="#17414c" size="6">ВАШИ ОНЛАЙН СЕРВИСЫ:</font>
	</td>
</tr>
<tr bgcolor="#dbe4f1">
    <td>
    	<table class="w640" width="640" border="0" cellpadding="0" cellspacing="0">
			<tbody>
			    <tr>
					<td colspan="3" height="20">
					   <img src="<?= $this->getImageUrl('/images/offer/textfon-t.png'); ?>"/>
				    </td>
				</tr>
				<tr>
					<td width="60" height="90">
					   <img src="<?= $this->getImageUrl('/images/offer/textfon-l.png'); ?>"/>
					</td>
					<td width="520" height="100" width="520" bgcolor="#FFFFFF" valign="middle" align="center">
						<!-- Текст про сервисы -->
						<font face="tahoma,sans-serif" color="#17414c" size="3">
						8-летний опыт и 2 года ит-разработок позволили нам запустить первый в России 
						автоматизированный ресурс для предоставления полного спектра кастинговых услуг 
						при помощи </font><br />
						<font face="tahoma,sans-serif" color="#55B0C6" size="3"><i>нескольких кликов<i></font>  
					</td>
					<td width="60">
					   <img src="<?= $this->getImageUrl('/images/offer/textfon-r.png'); ?>"/>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="20">
					   <img src="<?= $this->getImageUrl('/images/offer/textfon-b.png'); ?>"/>
				    </td>
				</tr>
			</tbody>
    	</table>
	</td>
</tr>
<tr bgcolor="#dbe4f1">
	<td>
		<!-- Сервисы -->
		<div style="text-align:left" align="center">
			<img src="<?= $this->getImageUrl('/images/offer/services.png'); ?>" class="w640" style="display: inline" align="top" border="0" width="640">
		</div>
	</td>
</tr>
<tr bgcolor="#dbe4f1">
	<td>
		<!-- Кнопка -->
		<table class="w640" width="640" border="0" cellpadding="0" cellspacing="0">
			<tbody>
				<tr>
					<td width="131" height="93">
						
					</td>
					<td width="380">
						<a href="<?= $tourUrl; ?>">
                            <img src="<?= $this->getImageUrl('/images/offer/tur_but.png'); ?>" 
                                style="display: inline" align="top" border="0" width="380">
                        </a>
					</td>
					<td width="132">
						
					</td>
				</tr>
			</tbody>
		</table>
	</td>
</tr>
<tr bgcolor="#dbe4f1">
	<td>
		<!-- Проекты -->
		<div style="text-align: left" align="center">
			<img src="<?= $this->getImageUrl('/images/offer/projects.png'); ?>" class="w640" style="display: inline" align="top" border="0" width="640">
		</div>
	</td>
</tr>