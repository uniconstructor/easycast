<?php
/**
 * И то чего вы все так долго ждали: разметка основной части страницы!
 * Да, именно здесь находится все основное содержимое страницы, все остальное - просто декорации
 * 
 * В разделе "ribbon" располагается верхняя навигация ("хлебные крошки")
 * В разделе "content", как следует из название, лежит наше всё
 */
/* @var $this Controller */

// навигация, и все что выше
//$this->beginContent('//layouts/main');
?>
<!-- MAIN_PANEL -->
<div id="main">
    <!-- RIBBON -->
    <div id="ribbon">
        <span class="ribbon-button-alignment btn btn-ribbon" data-html="true" 
            data-original-title="message..." data-placement="bottom" data-title="refresh" id="refresh"></span>
        <!-- breadcrumb -->
        <ol class="breadcrumb">
            <!-- if you are using the AJAX version, the breadcrumb area will be empty -->
            <li>Blank Page</li>
        </ol>
        <!-- end breadcrumb -->
    </div>
    <!-- END RIBBON -->
    <!-- MAIN_CONTENT -->
    <div id="content">
        <?php echo $content; ?>
    </div>
    <!-- END MAIN_CONTENT -->
</div>
<!-- END MAIN_PANEL -->
<?php
// подвал страницы
//$this->endContent();