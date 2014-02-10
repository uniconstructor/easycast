<?php
/**
 * Коммерческое предложение
 */
?>

<!-- ################-->
<!-- START TOP MENU -->
<!-- ################-->
<nav class="nav-top">
    <div class="navbar navbar-static-top" id="topnavbar">
        <!-- navbar-fixed-top -->
        <div class="navbar-inner" id="navbartop">
            <div class="container">
                <a class="brand" href="<?= Yii::app()->createAbsoluteUrl('//'); ?>">
                
                <img style="height:60px;margin-top:-10px;" alt="EasyCast" 
                    src="<?= Yii::app()->createAbsoluteUrl('//'); ?>/images/logo.png">
                </a>
                <div id="main-nav" class="scroller-spy">
                    <nav class="nav-collapse collapse">
                        <ul class="nav" id="nav" style="padding-top:10px;">
                            <li class="active"><a href="#header-section">Начало</a></li>
                            <li><a href="#features-section">Наши услуги</a></li>
                            <li><a href="#team-section">Наши проекты</a></li>
                            <li><a href="#portfolio-section">Команда</a></li>
                            <li><a href="#price-section">Отзывы</a></li>
                            <li><a href="#contact-section">Контакты</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- ################-->
    <!-- END TOP MENU -->
    <!-- ################-->
</nav>
<!-- END HEADER headertop NAV -->

<!-- TOP SECTION-->
<section class="headertop needhead" id="header-section">
    
    <div class="container">
        <div class="row-fluid">
            <p class="lead lead-text">
            Кастинговое агентство easyCast с 2005 года успешно обеспечивает актерами, 
            моделями, типажами и артистами массовых сцен самые масштабные и сложные съемки.
            Мы оказываем качественные услуги по поиску, отбору и администрированию
            всех вышеперечисленных персонажей для производителей рекламы, кино,
            телепроектов, сериалов и всех других видов аудиовизуальной продукции. 
            С помощью современных технологий мы создали мощнейшие инструменты
            кастинга, и даже самые сложные задачи с нами решаются быстро и удобно!
            </p>
        </div>
        
    </div>
    <!-- Список услуг -->
    <div class="lp-static-photos">
        <ul>
			<li>
				<a href="#"><img src="<?= Yii::app()->theme->baseUrl; ?>/images/services/01-Media.jpg" alt="" title=""/></a>
			</li>
			<li style="margin:0px 0px -16px -56px; z-index: 27; position: relative;">
				<a href="#"><img src="<?= Yii::app()->theme->baseUrl; ?>/images/services/02-Services.jpg" alt="" title=""/></a>
			</li>
			<li style="margin:0px 0px 0px -52px;  z-index: 40; position: relative;">
				<a href="#"><img src="<?= Yii::app()->theme->baseUrl; ?>/images/services/03-Models.jpg" alt="" title=""/></a>
			</li>
			<li style="margin:0px 0px 0px -50px;  z-index: 35; position: relative;">
				<a href="#"><img src="<?= Yii::app()->theme->baseUrl; ?>/images/services/04-Children.jpg" alt="" title=""/></a>
			</li>
			<li style="margin:0px 0px 0px -52px;  z-index: 40; position: relative;">
				<a href="#"><img src="<?= Yii::app()->theme->baseUrl; ?>/images/services/05-Castings.jpg" alt="" title=""/></a>
			</li>
		</ul>
		<ul style="margin-top: -65px;">
			<li style="margin:0px 0px 0px 5px;  z-index: 30; position: relative;">
				<a href="#"><img src="images/s6.png" alt="" title=""/></a>
			</li>
			<li style="margin:0px 0px 0px -56px; z-index: 28; position: relative;">
				<a href="#"><img src="images/s7.png" alt="" title=""/></a>
			</li>
			<li style="margin:3px 0px 0px -63px;  z-index: 40; position: relative;">
				<a href="#"><img src="images/s8.png" alt="" title=""/></a>
			</li>
			<li style="margin:0px 0px 0px -50px;  z-index: 34; position: relative;">
				<a href="#"><img src="images/s9.png" alt="" title=""/></a>
			</li>
			<li style="margin:0px 0px -10px -67px;  z-index: 40; position: relative;">
				<a href="#"><img src="images/s10.png" alt="" title=""/></a>
			</li>
		</ul> 
		<ul style="margin-top: -63px;">
			<li style="margin:0px 0px 0px -7px;  z-index: 27; position: relative;">
				<a href="#"><img src="images/s11.png" alt="" title=""/></a>
			</li>
			<li style="margin:0px 0px -35px -48px; z-index: 28; position: relative;">
				<a href="#"><img src="images/s12.png" alt="" title=""/></a>
			</li>
			<li style="margin:3px 0px -11px -53px;  z-index: 40; position: relative;">
				<a href="#"><img src="images/s13.png" alt="" title=""/></a>
			</li>
			<li style="margin:0px 0px -24px -57px;  z-index: 42; position: relative;">
				<a href="#"><img src="images/s14.png" alt="" title=""/></a>
			</li>
			<li style="margin:0px 0px -40px -67px;  z-index: 40; position: relative;">
				<a href="#"><img src="images/s15.png" alt="" title=""/></a>
			</li>
		</ul> 
	</div>
	
    <!-- Кнопки под списком услуг -->
    <div class="container">
        <div class="row-fluid" style="height:70px;"></div>
        <div class="row-fluid">
            <div class="span4 offset2">
                <span class="label label-warning lines-bg-color text-center emphasize-dark" 
                    style="background-color:#0b0;padding:20px;">
                    <a href="#" style="font-size:23px;text-decoration:none;color:#fff;line-height:1em;">Сделать заказ</a>
                </span>
                <p></p>
            </div>
            
            <div class="span4">
                <span class="label label-warning lines-bg-color text-center emphasize-dark" 
                    style="background-color:#55c;padding:20px;">
                    <a href="#" style="font-size:21px;text-decoration:none;color:#fff;line-height:1em;">Заказать обратный звонок</a>
                </span>
                <!--p>Оставьте свой номер телефона и мы свяжемся с вами для уточнения деталей</p-->
            </div>
            <div class="offset2">&nbsp;</div>
        </div>
    </div>
    <!-- / Список услуг-->
</section>
<!--/ TOP SECTION-->

<!-- SERVICES SECTION-->
<section id="features-section" class="section-2">
    <div class="bg-wraper parallax-point-event"></div>
        <div class="container">
            <div class="row-fluid">
                <header class="text-center">
                    <h3 style="color:#fff;font-size:36px;" class="top lead">Ваши онлайн-сервисы</h3>
                </header>
            </div>
            <div class="row-fluid">
                <p class="lead lead-text">
                Мы создали ресурс, 
                способный упорядочить сложные процессы поиска, отбора и утверждения артистов.
                Восьмилетний опыт и два года IT-разработок позволили нам запустить первый в 
                России автоматизированный ресурс для предоставления полного спектра кастинговых услуг.
                Сегодня сложные процессы поиска, отбора и утверждения артистов с нами - это надежный сервис, 
                упакованный в простое и понятное меню.
                </p>
            </div>
        </div>
        
        <!-- Сервисы списком -->
        <div class="container">
            <!-- Поиск -->
            <div class="row-fluid">
                <div class="span12">
                    <div class="lp-service-wrapper">
                        <div class="row-fluid">
                            <div class="span3">
                                <div class="lp-service-icon">&nbsp;</div>
                            </div>
                            <div class="span9">
                                <div class="lp-service-text drop-shadow bottom">
                                <h3 class="lp-service-header">Поиск</h3>
                                <p class="lead">Найдите нужного артиста. Это легко и быстро:
                                вы вводите параметры и ресурс выдаст вам список реальных людей, 
                                которые подходят под нужное описание. 
                                Наш поиск содержит <b>25</b> критериев чтобы вы могли найти самый сложный типаж.
                                </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Справка по поиску -->
            <div id="lp-search-help-slider ec-internal-wrapper" class="cbp-fwslider" ><!-- style="background-color: #ccc;" -->
				<ul>
					<li style="height:650px;">
					<?php 
					
					// получаем корневой раздел каталога ("вся база") для того чтобы искать по всем доступным анкетам
					$rootSection = CatalogSection::model()->findByPk(1);
					// виджет расширенной формы поиска (по всей базе)
					$this->widget('catalog.extensions.search.QSearchForm.QSearchForm', array(
					    'searchObject' => $rootSection,
					    'mode'         => 'filter',
					    // после отправки ajax-запроса поиска перенаправляет пользователя на страницу с фильтрами
					    'redirectUrl'  => '/catalog/catalog/search',
					    'refreshDataOnChange' => false,
					));
					?>
					</li>
					<li style="height:500px;"><a href="#"><img src="<?= Yii::app()->theme->baseUrl; ?>/images/examples/b2.jpg" alt="img01"/></a></li>
					<li style="height:500px;"><a href="#"><img src="<?= Yii::app()->theme->baseUrl; ?>/images/examples/b3.jpg" alt="img01"/></a></li>
					<li style="height:500px;"><a href="#"><img src="<?= Yii::app()->theme->baseUrl; ?>/images/examples/b4.jpg" alt="img01"/></a></li>
				</ul>
			</div>
            <div class="row-fluid">
                <div class="span12">
                    <div class="lp-service-wrapper">
                        <div class="row-fluid">
                            <div class="span3">
                                <div class="lp-service-icon">&nbsp;</div>
                            </div>
                            <div class="span9">
                                <div class="lp-service-text drop-shadow bottom">
                                <h3 class="lp-service-header">Каталог и детальные анкеты</h3>
                                <p class="lead">Используйте максимум информации при выборе.
                                На сегодняшний день на нашем ресурсе размещено свыше 2700 анкет и
                                мы постоянно пополняем нашу базу. 
                                В месяц регистрируется около 150 актеров, моделей, детей, 
                                ведущих и вокалистов, и эта цифра постоянно растет.
                                <br> 
                                Анкеты в нашем каталоге отсортированы по разделам, каждый из 
                                которых содержит вкладки и фильтры. Вся размещаемая информация проверяется нашими 
                                администраторами.
                                <br>
                                Несколько кликов - и актер на съемочной площадке!
                                </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <div class="lp-service-wrapper">
                        <div class="row-fluid">
                            <div class="span3">
                                <div class="lp-service-icon">&nbsp;</div>
                            </div>
                            <div class="span9">
                                <div class="lp-service-text drop-shadow bottom">
                                <h3 class="lp-service-header">Онлайн кастинг</h3>
                                <p class="lead">Используйте новый, удобный формат проведения кастинга.
                                Он позволяет выбрать необходимых персонажей не выходя из дома, офиса или стоя в пробке.
                                <b>Вы платите только за результат</b>, поэтому если вы не нашли нужных людей - 
                                то вы ничего не оплачиваете. 
                                Если вы цените свое время - то онлайн кастинг станет для вас лучшим помощником. 
                                <br>
                                Взгляните на нашу демонстрацию чтобы убедиться в этом.
                                </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <div class="lp-service-wrapper">
                        <div class="row-fluid">
                            <div class="span3">
                                <div class="lp-service-icon">&nbsp;</div>
                            </div>
                            <div class="span9">
                                <div class="lp-service-text drop-shadow bottom">
                                <h3 class="lp-service-header">Автоматизация документооборота</h3>
                                <p class="lead">Экономьте время при работе с документами. 
                                Наша система автоматически генерирует весь пакет документов, включая договор, 
                                ведомости, смету и даже фотовызывной, содержащий всю необходимую 
                                информацию об актерах.
                                </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>
<!-- SERVICES SECTION-->


<!-- Список проектов-->
<section id="slogan-section-3" class="slogan-section-3">
    <div class="container">
        <div class="row-fluid">
            <?php 
            $criteria = Yii::app()->getModule('projects')->getProjectsCriteria();
            $dataProvider = new CActiveDataProvider('Project',
                array(
                    'criteria'   => $criteria,
                    'pagination' => array('pageSize' => 36),
                )
            );
            $dataProvider->pagination = false;
            $this->widget('ext.CdGridPreview.CdGridPreview', array(
                'dataProvider'     => $dataProvider,
                'listViewLocation' => 'bootstrap.widgets.TbListView',
                //'htmlOptions'      => array('id' => 'og-grid'),
                'listViewOptions'  => array(
                    'template' => '{items}',
                ),
                'options' => array(
                    'textClass'   => 'well og-details-text',
                    'headerClass' => 'og-details-header ec-details-header',
                    //'detailsClass' => 'well',
                ),
            ));
            
            ?>
        </div>
    </div>
</section>
<!--/ Список проектов-->

