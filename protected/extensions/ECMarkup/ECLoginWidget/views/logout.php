<?php
/**
 * Аватарка пользователя, ФИО и кнопка выхода
 */
/* @var $this ECLoginWidget */
?>
<div class="enterbut">
	<div class="in_enterbut">
		<?= $this->_image; ?><a class="name_user" href="<?= $this->_mainUrl; ?>"><?= $this->_userName; ?></a>
		<a href="<?= $this->_logoutUrl; ?>" class="exit">Выйти</a>
	</div>
</div>