<?php
/**
 * Разметка верхней части страницы: заголовок и верхняя навигация
 * 
 * @todo разбить на более мелкие виджеты
 * @todo поиск по всем объектам
 */
/* @var $this Controller */

$projectCount = 0;
$projectList  = '';
$myProjects   = array();
$projects     = Project::model()->forLeader(Yii::app()->user->id)->
    withStatus(Project::STATUS_ACTIVE)->lastCreated()->findAll();
foreach ( $projects as $project )
{
    $myProjects[] = CHtml::link($project->name, 
        Yii::app()->createUrl('//admin/project/view', array('id' => $project->id)));
}
if ( ! empty($myProjects) )
{
    $projectCount = count($myProjects);
    $projectList  = '<li>'.implode("</li>\n <li>", $myProjects).'</li>';
}
?>
<!-- [BEGIN HEADER] -->
<header id="header">
    <div id="logo-group">
        <!-- PLACE YOUR LOGO HERE -->
        <span id="logo">
            <img alt="easyCast" src="<?= Yii::app()->baseUrl; ?>/images/logo.png">
        </span>
        <!-- END LOGO PLACEHOLDER -->
        <span class="activity-dropdown" id="activity"><b class="badge"><?= $projectCount; ?></b></span>
        <!-- AJAX-DROPDOWN : control this dropdown height, look and feel from the LESS variable file -->
        <div class="ajax-dropdown">
            <div class="ajax-notifications custom-scroll">
                
            </div>
            <!-- end notification content -->
            <!-- footer: refresh area -->
            <span>Last updated on: 12/12/2013 9:43AM</span>
            <!-- end footer -->
        </div>
        <!-- END AJAX-DROPDOWN -->
    </div>
    <!-- projects dropdown -->
    <div id="project-context">
        <span class="label">Мои текущие проекты:</span>
        <span class="popover-trigger-element dropdown-toggle" 
            data-toggle="dropdown" id="project-selector">[Посмотреть все]</span>
        <!-- Suggestion: populate this list with fetch and push technique -->
        <ul class="dropdown-menu">
            <?= $projectList; ?> 
        </ul>
        <!-- end dropdown-menu-->
    </div>
    <!-- end projects dropdown -->
    
    <!-- pulled right: nav area -->
    <div class="pull-right">
        <!-- collapse menu button -->
        <!--div class="btn-header pull-right" id="hide-menu">
            <span><a href="javascript:void(0);" title="CollapseMenu"></a></span>
        </div-->
        <!-- end collapse menu -->
    
        <!-- logout button -->
        <!--div class="btn-header transparent pull-right" id="logout">
            <span><a data-logout-msg="You can improve your security further after logging out by closing this opened browser" href="login.html"
                title="Sign Out"></a></span>
        </div-->
        <!-- end logout button -->
    
        <!-- search mobile button (this is hidden till mobile view port) -->
        <!--div class="btn-header transparent pull-right" id="search-mobile">
            <span><a href="javascript:void(0)" title="Search"></a></span>
        </div-->
        <!-- end search mobile button -->
    
        <!-- input: search field -->
        <!--form action="#ajax/search.html" class="header-search pull-right">
            <input id="search-fld" name="param" placeholder="Find reports and more" type="text">
            <a href="javascript:void(0);" id="cancel-search-js" title="Cancel Search"></a>
        </form-->
        <!-- end input: search field -->
    
        <!-- fullscreen button -->
        <!--div class="btn-header transparent pull-right" id="fullscreen">
            <span><a href="javascript:void(0);" onclick="launchFullscreen(document.documentElement);" title="Full Screen"></a></span>
        </div-->
        <!-- end fullscreen button -->
        
        <!-- multiple lang dropdown : find all flags in the image folder -->
        <!--ul class="header-dropdown-list hidden-xs">
            <li>...</li>
        </ul-->
        <!-- end multiple lang -->
    </div>
    <!-- end pulled right: nav area -->
</header>
<!-- [END HEADER] -->