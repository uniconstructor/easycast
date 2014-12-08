<?php 
/**
 * Разметка для сетки виджетов: эта специальная разметка содержит особый раздел "widget-grid":
 * все виджеты помещенные в эту секуию будут автоматически инициализированы и плавно загружены
 * через AJAX по мере готовности
 * Контейнер с таким id может быть на странице только один, поэтому все виджеты нужно помещать в него
 * Используйте эту разметку для больших информационых панелей
 * 
 * @todo вынести заголовок в отдельный layout, и строить его из $this->breadcrumps
 * @todo создать в контроллере отдельную переменную для хранения виджетов и записывать их код туда
 *       После этого добавить на эту страницу дополнительный блок для отдельной информации
 * @todo вынести разметку сетки отдельный виджет (должен быть устроен как форма: beginWidget()/endWidget())
 * @todo вынести sparks-графики в отдельный виджет 
 */
/* @var $this    Controller */
/* @var $content string */
?>
<!-- Bread crumb is created dynamically -->
<!-- row -->
<div class="row">
	<!-- col -->
	<div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
		<h1 class="page-title txt-color-blueDark">
			<!-- PAGE HEADER -->
			<i class="fa-fw fa fa-home"></i> 
				<?= $this->pageHeader; ?>
			<span>  
				<?= $this->subTitle; ?>
			</span>
		</h1>
	</div>
	<!-- end col -->
	<!-- right side of the page with the sparkline graphs -->
	<!-- col -->
	<div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">
		<!-- sparks -->
		<!--ul id="sparks">
			<li class="sparks-info">
				<h5> My Income <span class="txt-color-blue">$47,171</span></h5>
				<div class="sparkline txt-color-blue hidden-mobile hidden-md hidden-sm">
					1300, 1877, 2500, 2577, 2000, 2100, 3000, 2700, 3631, 2471, 2700, 3631, 2471
				</div>
			</li>
			<li class="sparks-info">
				<h5> Site Traffic <span class="txt-color-purple"><i class="fa fa-arrow-circle-up" data-rel="bootstrap-tooltip" title="Increased"></i>&nbsp;45%</span></h5>
				<div class="sparkline txt-color-purple hidden-mobile hidden-md hidden-sm">
					110,150,300,130,400,240,220,310,220,300, 270, 210
				</div>
			</li>
			<li class="sparks-info">
				<h5> Site Orders <span class="txt-color-greenDark"><i class="fa fa-shopping-cart"></i>&nbsp;2447</span></h5>
				<div class="sparkline txt-color-greenDark hidden-mobile hidden-md hidden-sm">
					110,150,300,130,400,240,220,310,220,300, 270, 210
				</div>
			</li>
		</ul-->
		<!-- end sparks -->
	</div>
	<!-- end col -->
</div>
<!-- end row -->
<!--
	The ID "widget-grid" will start to initialize all widgets below 
	You do not need to use widgets if you dont want to. Simply remove 
	the <section></section> and you can use wells or panels instead 
-->
<!-- widget grid -->
<section id="widget-grid" class="">
	<!-- row -->
	<?= $content; ?>
	<!-- end row -->
</section>
<!-- end widget grid -->
<?php 
// js, обязательный для работы всех страниц темы SmartAdmin: 
// отвечает за инициализацию всех элементов и за подгрузку содержимого страницы по AJAX
$this->renderPartial('//layouts/ajax/_pageSetup');