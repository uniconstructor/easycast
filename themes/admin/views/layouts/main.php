<?php
/**
 * Главный файл разметки темы оформления "SmartAdmin". Тема построена на базе TwitterBootstrap.
 * Содержит все необходимые скрипты и стили.
 * Пакеты скриптов и стилей Yii, jQuery и Bootstrap должны быть переопределены при использовании этой темы.
 * 
 * Примечания:
 * - вся основная конфигурация лежит в /js/app.js или /js/app.config.js
 * - виджеты JARVIS по умолчанию отключены в мобильной версии (см. app.js)
 * - все скрипты подключаются только внизу страницы, не перемещайте их
 * - для темы glass можно сменить задний фон: для этого замените свойство background.
 *   Размер картинки для фона должен быть 1920х1080, можно использовать фотографию
 * 
 * @see документация по этой теме оформления и всем ее виджетам лежит в ветке dev проекта easycast
 * @see http://192.241.236.31/themes/preview/smartadmin/1.4.1/ajaxversion/#ajax/dashboard.html
 * @see http://wrapbootstrap.com/preview/WB0573SK0
 * 
 * @todo настраиваемый стиль страницы (от smart-style-5 до smart-style-1)
 * @todo управление боковым меню: свернуть/восстановить
 */
/* @var $this SmartAdminController */
/* @var $content string */

?><!DOCTYPE html>
<html class="smart-style-2" lang="ru">
    <?php 
    // заголовок страницы
    $this->renderPartial('//layouts/head');
    ?>
    <body class="smart-style-2 fixed-navigation">
        <?php 
        // шапка страницы + верхняя панель с инструментами
        $this->renderPartial('//layouts/header');
        // главное меню
        $this->renderPartial('//layouts/navigation');
        // основное содержимое страницы
        $this->renderPartial('//layouts/panel', array(
            'content' => $content,
        ));
        // footer
        $this->renderPartial('//layouts/footer');
        ?>
        <!-- #SHORTCUT AREA : With large tiles (activated via clicking user name tag)
			 Note: These tiles are completely responsive, you can add as many as you like -->
		<div id="shortcut">
			<ul>
				<li>
					<a href="#ajax/inbox.html" class="jarvismetro-tile big-cubes bg-color-blue"> <span class="iconbox"> <i class="fa fa-envelope fa-4x"></i> <span>Mail <span class="label pull-right bg-color-darken">14</span></span> </span> </a>
				</li>
				<li>
					<a href="#ajax/calendar.html" class="jarvismetro-tile big-cubes bg-color-orangeDark"> <span class="iconbox"> <i class="fa fa-calendar fa-4x"></i> <span>Calendar</span> </span> </a>
				</li>
				<li>
					<a href="#ajax/gmap-xml.html" class="jarvismetro-tile big-cubes bg-color-purple"> <span class="iconbox"> <i class="fa fa-map-marker fa-4x"></i> <span>Maps</span> </span> </a>
				</li>
				<li>
					<a href="#ajax/invoice.html" class="jarvismetro-tile big-cubes bg-color-blueDark"> <span class="iconbox"> <i class="fa fa-book fa-4x"></i> <span>Invoice <span class="label pull-right bg-color-darken">99</span></span> </span> </a>
				</li>
				<li>
					<a href="#ajax/gallery.html" class="jarvismetro-tile big-cubes bg-color-greenLight"> <span class="iconbox"> <i class="fa fa-picture-o fa-4x"></i> <span>Gallery </span> </span> </a>
				</li>
				<li>
					<a href="#ajax/profile.html" class="jarvismetro-tile big-cubes selected bg-color-pinkDark"> <span class="iconbox"> <i class="fa fa-user fa-4x"></i> <span>My Profile </span> </span> </a>
				</li>
			</ul>
		</div>
		<!-- END SHORTCUT AREA -->
        <?php
        // полный набор скриптов для работы темы оформления
        $this->renderPartial('//layouts/scripts');
        $csfrVarOptions = array(
            'id' => '_ecYiiCsrfContainer',
        );
        $ajaxVarOptions = array(
            'id' => '_ecYiiAjaxVarContainer',
        );
        echo CHtml::hiddenField(Yii::app()->request->csrfTokenName, Yii::app()->request->csrfTokenName, $csfrVarOptions);
        echo CHtml::hiddenField('ajax', 1, $ajaxVarOptions);
        ?>
    </body>
</html>