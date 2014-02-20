<?php
/**
 * Коммерческое предложение в HTML (статический вариант, без эффектов)
 */
/* @var $this SaleController */

// получаем все ссылки
$baseUrl          = Yii::app()->createAbsoluteUrl('//');
$searchUrl        = Yii::app()->createAbsoluteUrl('//search');
$orderUrl         = Yii::app()->createAbsoluteUrl('//order');
$calculationUrl   = Yii::app()->createAbsoluteUrl('//calculation');
$onlineCastingUrl = Yii::app()->createAbsoluteUrl('//onlineCasting');


?>
<div class="ec-header">
	<div class="top">
		<div class="top_left">
			<span>+7 495 227-5-226</span>
			<span>easycast.ru</span>
		</div>
		<div class="top_center">
			<div class="logo">
				<a href="index.html"><img src="<?= $baseUrl; ?>/images/logo.png" alt="EasyCasting"/></a>
			</div>
		</div>
		<div class="top_right">
			<div class="join_but">
			     <button type="button" data-toggle="modal" data-target="#fastOrderModal" id="header-order-button" class="btn btn-primary">Сделать заказ</button>
			</div>
		</div>
	</div>
</div>
<?php
    // все modal-окна должны находится внутри элемента ec-header, чтобы он не закрывал их при появлении 
    // выводим скрытую форму срочного заказа: она появляется как modal-окно
    $this->widget('ext.ECMarkup.ECFastOrder.ECFastOrder');
    
    // modal-окно с гарантиями
    $this->widget('ext.ECMarkup.ECIGuaranteeIt.ECIGuaranteeIt');
    ?>
<div class="section_1" id="section1">
	<div class="ec-wrapper add_padding">
		<div class="banner">
			<p>Кастинговое агентство <strong>easyCast</strong> с 2005 года успешно обеспечивает актерами, 
            моделями, типажами и артистами массовых сцен самые масштабные и сложные съемки.
            Мы оказываем качественные услуги по поиску, отбору и администрированию
            всех вышеперечисленных персонажей для производителей рекламы, кино,
            телепроектов, сериалов и всех других видов аудиовизуальной продукции. 
            С помощью современных технологий мы создали мощнейшие инструменты
            кастинга, и даже самые сложные задачи с нами решаются быстро и удобно!</p>
			<p class="slogan">Все сложное с нами легко!</p>
		</div>
		<div class="uslugi">
			<ul>
				<li>
					<a href="<?= $calculationUrl; ?>" target="_blank"><img src="<?= $baseUrl; ?>/images/offer/services/s1.png"/></a>
				</li>
				<li style="margin:0px 0px -16px -56px;z-index:27;position:relative;">
					<a href="<?= $calculationUrl; ?>" target="_blank"><img src="<?= $baseUrl; ?>/images/offer/services/s2.png"/></a>
				</li>
				<li style="margin:0px 0px 0px -52px;z-index:40;position:relative;">
					<a href="<?= $calculationUrl; ?>" target="_blank"><img src="<?= $baseUrl; ?>/images/offer/services/s3.png"/></a>
				</li>
				<li style="margin:0px 0px 0px -50px;z-index:35;position:relative;">
					<a href="<?= $calculationUrl; ?>" target="_blank"><img src="<?= $baseUrl; ?>/images/offer/services/s4.png"/></a>
				</li>
				<li style="margin:0px 0px 0px -52px;z-index:40;position:relative;">
					<a href="<?= $calculationUrl; ?>" target="_blank"><img src="<?= $baseUrl; ?>/images/offer/services/s5.png"/></a>
				</li>
			</ul>
			<ul style="margin-top: -65px;">
				<li style="margin:0px 0px 0px 5px;z-index:30;position:relative;">
					<a href="<?= $calculationUrl; ?>" target="_blank"><img src="<?= $baseUrl; ?>/images/offer/services/s6.png"/></a>
				</li>
				<li style="margin:0px 0px 0px -56px;z-index:28;position:relative;">
					<a href="<?= $calculationUrl; ?>" target="_blank"><img src="<?= $baseUrl; ?>/images/offer/services/s7.png"/></a>
				</li>
				<li style="margin:3px 0px 0px -63px;z-index:40;position:relative;">
					<a href="<?= $calculationUrl; ?>" target="_blank"><img src="<?= $baseUrl; ?>/images/offer/services/s8.png"/></a>
				</li>
				<li style="margin:0px 0px 0px -50px;z-index:34;position:relative;">
					<a href="<?= $calculationUrl; ?>" target="_blank"><img src="<?= $baseUrl; ?>/images/offer/services/s9.png"/></a>
				</li>
				<li style="margin:0px 0px -10px -67px;z-index:40;position:relative;">
					<a href="<?= $calculationUrl; ?>" target="_blank"><img src="<?= $baseUrl; ?>/images/offer/services/s10.png"/></a>
				</li>
			</ul> 
			<ul style="margin-top: -63px;">
				<li style="margin:0px 0px 0px -7px;z-index:27;position:relative;">
					<a href="<?= $calculationUrl; ?>" target="_blank"><img src="<?= $baseUrl; ?>/images/offer/services/s11.png"/></a>
				</li>
				<li style="margin:0px 0px -35px -48px;z-index:28;position:relative;">
					<a href="<?= $calculationUrl; ?>" target="_blank"><img src="<?= $baseUrl; ?>/images/offer/services/s12.png"/></a>
				</li>
				<li style="margin:3px 0px -11px -53px;z-index:40;position:relative;">
					<a href="<?= $calculationUrl; ?>" target="_blank"><img src="<?= $baseUrl; ?>/images/offer/services/s13.png"/></a>
				</li>
				<li style="margin:0px 0px -24px -57px;z-index:42;position:relative;">
					<a href="<?= $calculationUrl; ?>" target="_blank"><img src="<?= $baseUrl; ?>/images/offer/services/s14.png"/></a>
				</li>
				<li style="margin:0px 0px -40px -67px;z-index:40;position:relative;">
					<a href="<?= $calculationUrl; ?>" target="_blank"><img src="<?= $baseUrl; ?>/images/offer/services/s15.png"/></a>
				</li>
			</ul> 
		</div>
        <div class="row-fluid">
		    <div class="span6">
                <div class="price">
                	<a href="<?= $orderUrl; ?>" data-toggle="modal" data-target="#fastOrderModal">Сделать заказ</a>
                </div>
            </div>
            <div class="span6">
                <div class="order">
                	<a href="<?= $calculationUrl; ?>" target="_blank">Расчитать стоимость</a>
                </div>
            </div>
		</div>
	</div>
</div>
<div class="section_2" id="section2" style="margin-top: 55px;">
    <!-- сервисы -->
	<div class="ec-wrapper">
        <h2 style="font-size:42px;text-align:center;text-transform:uppercase;margin-bottom:40px;font-weight:200;">Ваши онлайн-сервисы</h2>
        <div class="banner">
			<p>Мы рады вам сообщить, что благодаря восьмилетнему опыту работы<br>
			и двум годам сложнейших IT-разработок мы создали ресурс,<br>
			способный упорядочить все сложные процессы поиска, оповещения,<br> 
			отбора и утверждения артистов в простой сервис:<br> 
			несколько кликов - и все, кто вам нужен - в кадре!
			</p>
		</div>
		<div class="lp-service-list">
            <div class="lp-service-item">
                <div class="lp-service-icon-container">
                    <a href="<?= $searchUrl; ?>" target="_blank"><img class="lp-service-icon" 
                        src="<?= Yii::app()->theme->baseUrl; ?>/images/serv1.png"></a>
                </div>
                <div class="lp-service-info">
                    <h4 class="lp-service-name"><a href="<?= $searchUrl; ?>" target="_blank">Поиск по 25 критериям и 15 разделам</a></h4>
                    <p class="lp-service-text">
                    Ежедневно в нашей системе регистрируется множество актеров, моделей, артистов, 
                    ведущих, танцоров, музыкантов и вокалистов, ведь мы предлагаем действительно 
                    удобный и надежный сервис. Для удобства поиска все анкеты автоматически распределяются 
                    по 15 разделам с вкладками и фильтрами. Автоматически обновляемый расширенный поиск 
                    быстро и легко найдет нужного артиста с вашей помощью: просто укажите от 1 до 25 критериев 
                    поиска и найти самый сложный типаж станет легко!
                    </p>
                </div>
            </div>
            <div class="lp-service-item">
                <div class="lp-service-icon-container">
                    <a href="<?= $orderUrl; ?>" data-toggle="modal" data-target="#fastOrderModal"><img class="lp-service-icon" 
                        src="<?= Yii::app()->theme->baseUrl; ?>/images/serv2.png"></a>
                </div>
                <div class="lp-service-info">
                    <h4 class="lp-service-name"><a href="<?= $orderUrl; ?>" data-toggle="modal" data-target="#fastOrderModal">Заказ через персонального менеджера</a></h4>
                    <p class="lp-service-text"> 
                    Данный сервис создан специально для режиссеров, продюсеров и кастинг-директоров, 
                    которые предпочитают делегировать все кастинг-задачи и контролировать лишь конечный 
                    результат работы в своем личном кабинете на нашем сайте. Для вашего удобства и 
                    спокойствия - наши лучшие руководители проектов. Просто выбираете для своего проекта 
                    персонального менеджера и заполняете короткую форму - остальное сделаем мы! 
                    </p>
                </div>
            </div>
            <div class="lp-service-item">
                <div class="lp-service-icon-container">
                    <a href="<?= $onlineCastingUrl; ?>"><img class="lp-service-icon" 
                        src="<?= Yii::app()->theme->baseUrl; ?>/images/serv3.png"></a>
                </div>
                <div class="lp-service-info">
                    <h4 class="lp-service-name"><a href="<?= $onlineCastingUrl; ?>">Онлайн кастинг</a></h4>
                    <p class="lp-service-text">
                    Это новый, удобный и современный формат проведения кастинга. 
                    Если вы цените свое время, то онлайн кастинг станет для вас лучшим помощником. 
                    Вы можете провести полноценный кастинг не выходя из дома, офиса или стоя в пробке. 
                    Нужно заполнить заявку на проведение онлайн-кастинга и ввести все необходимые 
                    сведения о проекте. Система автоматически оповестит всех подходящих по параметрам 
                    пользователей, получит от каждого видеоролик, и после этого предложит вам 
                    просмотреть и отобрать заявки.
                    </p>
                </div>
            </div>
            <div class="lp-service-item">
                <div class="lp-service-icon-container">
                    <a href="#"><img class="lp-service-icon" 
                        src="<?= Yii::app()->theme->baseUrl; ?>/images/serv4.png"></a>
                </div>
                <div class="lp-service-info">
                    <h4 class="lp-service-name"><a href="#">Автоматизация документооборота</a></h4>
                    <p class="lp-service-text" style="padding-top:25px;">
                        Наша умная система на основе сформированного заказа система автоматически 
                        генерирует весь пакет документов, включая договор, 
                        ведомости, смету и даже фотовызывной, содержащий всю необходимую 
                        информацию об актерах, утвержденных заказчиком.
                    </p>
                </div>
            </div>
		</div>
		<div class="row-fluid">
		    <div class="span6">
                <div class="price">
                	<a href="<?= $orderUrl; ?>" data-toggle="modal" data-target="#fastOrderModal">Сделать заказ</a>
                </div>
            </div>
            <div class="span6">
                <div class="order">
                	<a href="<?= $calculationUrl; ?>" target="_blank">Расчитать стоимость</a>
                </div>
            </div>
		</div>
		<!-- отзывы -->
		<h2 style="font-size:42px;text-align:center;text-transform:uppercase;margin-bottom:40px;font-weight:200;margin-top:50px;">Отзывы</h2>
    	<div class="container3">
            <?php 
            $this->renderPartial('_reviews');
            ?>
        </div>
        <!--/ отзывы -->
	</div>
	<!--/ сервисы -->
</div>
<div class="section_3" id="section3" style="margin-top: 55px;">
    <div class="ec-wrapper">
    <div class="lp3_title">
        <p style="vertical-align: top;">
            <span><img style="vertical-align:top;" src="<?php echo Yii::app()->theme->baseUrl; ?>/images/100.png"/></span> 
            причин работать с нами<br>
            <span style="color:#777;display:block;font-size:16px;margin-top:-30px;margin-left:140px;text-transform: uppercase;">
                Проектов, созданных с использованием наших услуг
            </span>
        </p>
    </div>
        <!-- список проектов -->
        <?php 
        // извлекаем 100 проектов по рейтингу
        $criteria        = Yii::app()->getModule('projects')->getProjectsCriteria();
        $criteria->limit = 100;
        $dataProvider = new CActiveDataProvider('Project', array(
            'criteria'   => $criteria,
            'pagination' => false,
        ));
        // раскрывающийся список проектов
        $this->widget('ext.CdGridPreview.CdGridPreview', array(
            'dataProvider'     => $dataProvider,
            'listViewLocation' => 'bootstrap.widgets.TbListView',
            'listViewOptions'  => array(
                'template' => '{items}',
            ),
            'options' => array(
                'textClass'   => 'well og-details-text',
                'headerClass' => 'og-details-header ec-details-header',
            ),
            'previewHtmlOptions' => array(
                'style' => 'min-height:100px;max-width:100px;min-width:100px;border-radius:10px;',
                'class' => 'ec-shadow-3px',
            ),
        ));
        ?>
        <!--/ список проектов -->
    <div class="quality">
        <a href="#IGuaranteeItModal" style="font-size: 28px;" data-toggle="modal">Почему именно мы&nbsp;?</a>
    </div>
    <div class="container3">
        <!-- команда -->
        <?php 
        $this->renderPartial('_team');
        ?>
        <!--/ команда -->
    	</div>	
    </div>
</div>
<script>
    
</script>