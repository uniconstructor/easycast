<?php
/**
 * И то чего вы все так долго ждали: разметка основной части страницы!
 * Да, именно здесь находится все основное содержимое страницы, все остальное - просто декорации
 * 
 * В разделе "ribbon" располагается верхняя навигация ("хлебные крошки")
 * В разделе "content", как следует из названия, лежит наше всё
 */
/* @var $this Controller */
?>
<!-- #MAIN_PANEL -->
<div id="main">
    <!-- RIBBON -->
    <div id="ribbon">
        <span class="ribbon-button-alignment"> 
            <span id="refresh" class="btn btn-ribbon" data-action="resetWidgets" data-title="refresh" 
            rel="tooltip" data-placement="bottom" data-original-title="<i class='text-warning fa fa-warning'></i> Warning! This will reset all your widget settings." data-html="true" data-reset-msg="Would you like to RESET all your saved widgets and clear LocalStorage?"><i class="fa fa-refresh"></i></span> 
		</span>
		<!-- You can also add more buttons to the
			ribbon for further usability

			Example below:
			<span class="ribbon-button-alignment pull-right" style="margin-right:25px">
				<a href="#" id="search" class="btn btn-ribbon hidden-xs" data-title="search"><i class="fa fa-grid"></i> Change Grid</a>
				<span id="add" class="btn btn-ribbon hidden-xs" data-title="add"><i class="fa fa-plus"></i> Add</span>
				<button id="search" class="btn btn-ribbon" data-title="search"><i class="fa fa-search"></i> <span class="hidden-mobile">Search</span></button>
			</span> 
		-->
        <!-- breadcrumb -->
        <ol class="breadcrumb">
            <!-- This is auto generated -->
        </ol>
        <!-- end breadcrumb -->
    </div>
    <!-- END RIBBON -->
    <!-- MAIN_CONTENT -->
    <div id="content">
        <?php
        // основное содержимое страницы: обновляется через AJAX 
        echo $content;
        ?>
    </div>
    <!-- END MAIN_CONTENT -->
</div>
<!-- END MAIN_PANEL -->
<?php
// стандартный JS-код темы оформления, который должен находится на каждой странице для
// корректной работы AJAX-навигации
$this->renderPartial('//layouts/ajax/_pageSetup');