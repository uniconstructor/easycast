<?php
/**
 * Разметка верхней части страницы: заголовок и верхняя навигация
 * 
 * @todo разбить на более мелкие виджеты
 * @todo поиск по всем объектам
 * @todo языковые строки
 */
/* @var $this SmartAdminController */

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
        <!-- LOGO -->
        <span id="logo">
            <img alt="easyCast" src="<?= Yii::app()->baseUrl; ?>/images/logo.png">
        </span>
        <!-- END LOGO -->
        <span class="activity-dropdown" id="activity"><b class="badge"><?= $projectCount; ?></b></span>
        <!-- AJAX-DROPDOWN : control this dropdown height, look and feel from the LESS variable file -->
        <div class="ajax-dropdown">
            <div class="ajax-notifications custom-scroll">
                <!-- the ID links are fetched via AJAX to the ajax container "ajax-notifications" -->
				<div class="btn-group btn-group-justified" data-toggle="buttons">
					<label class="btn btn-default">
						<input type="radio" name="activity" id="ajax/notify/mail.html">
						Msgs (0)
					</label>
					<label class="btn btn-default">
						<input type="radio" name="activity" id="ajax/notify/notifications.html">
						Notify (0)
					</label>
					<label class="btn btn-default">
						<input type="radio" name="activity" id="ajax/notify/tasks.html">
						Tasks (0)
					</label>
				</div>
            </div>
            <!-- notification content -->
			<div class="ajax-notifications custom-scroll">
				<div class="alert alert-transparent">
					<h4>Click a button to show messages here</h4>
					This blank page message helps protect your privacy, 
					or you can show the first message here automatically.
				</div>
				<i class="fa fa-lock fa-4x fa-border"></i>
			</div>
            <!-- end notification content -->
            <!-- footer: refresh area -->
            <span>
                Last updated on: 12/12/2013 9:43AM
                <button type="button" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Loading..." class="btn btn-xs btn-default pull-right">
					<i class="fa fa-refresh"></i>
				</button>
            </span>
            <!-- end footer -->
        </div>
        <!-- END AJAX-DROPDOWN -->
    </div>
    <!-- #PROJECTS: projects dropdown -->
    <div id="project-context">
        <span class="label">Проекты:</span>
        <span class="popover-trigger-element dropdown-toggle" data-toggle="dropdown" id="project-selector">[Показать]</span>
        <!-- Suggestion: populate this list with fetch and push technique -->
        <ul class="dropdown-menu">
            <?= $projectList; ?> 
            <li class="divider"></li>
			<li>
				<a href="javascript:void(0);"><i class="fa fa-power-off"></i> Clear</a>
			</li>
        </ul>
        <!-- end dropdown-menu-->
    </div>
    <!-- end projects dropdown -->
    
    <!-- pulled right: nav area -->
    <div class="pull-right">
        <!-- collapse menu button -->
        <div class="btn-header pull-right" id="hide-menu">
            <span><a href="javascript:void(0);" title="CollapseMenu"><i class="fa fa-reorder"></i></a></span>
        </div>
        <!-- end collapse menu -->
    
        <!-- logout button -->
        <div class="btn-header transparent pull-right" id="logout">
            <span><a data-logout-msg="You can improve your security further after logging out by closing this opened browser" 
                href="login.html" title="Sign Out"><i class="fa fa-sign-out"></i></a>
            </span>
        </div>
        <!-- end logout button -->
    
        <!-- search mobile button (this is hidden till mobile view port) -->
        <div class="btn-header transparent pull-right" id="search-mobile">
            <span><a href="javascript:void(0)" title="Search"></a></span>
        </div>
        <!-- end search mobile button -->
    
        <!-- input: search field -->
        <form action="#ajax/search.html" class="header-search pull-right">
            <input id="search-fld" name="param" placeholder="Find reports and more" type="text">
            <a href="javascript:void(0);" id="cancel-search-js" title="Cancel Search"><i class="fa fa-search"></i></a>
        </form>
        <!-- end input: search field -->
    
        <!-- fullscreen button -->
        <div class="btn-header transparent pull-right" id="fullscreen">
            <span><a href="javascript:void(0);" onclick="launchFullscreen(document.documentElement);" title="Full Screen"><i class="fa fa-arrows-alt"></i></a></span>
        </div>
        <!-- end fullscreen button -->
        
        <!-- #Voice Command: Start Speech -->
		<!-- NOTE: Voice command button will only show in browsers that support it. Currently it is hidden under mobile browsers. 
				   You can take off the "hidden-sm" and "hidden-xs" class to display inside mobile browser-->
		<div id="speech-btn" class="btn-header transparent pull-right hidden-sm hidden-xs">
			<div> 
				<a href="javascript:void(0)" title="Voice Command" data-action="voiceCommand"><i class="fa fa-microphone"></i></a> 
				<div class="popover bottom"><div class="arrow"></div>
					<div class="popover-content">
						<h4 class="vc-title">Voice command activated <br><small>Please speak clearly into the mic</small></h4>
						<h4 class="vc-title-error text-center">
							<i class="fa fa-microphone-slash"></i> Voice command failed
							<br><small class="txt-color-red">Must <strong>"Allow"</strong> Microphone</small>
							<br><small class="txt-color-red">Must have <strong>Internet Connection</strong></small>
						</h4>
						<a href="javascript:void(0);" class="btn btn-success" onclick="commands.help()">See Commands</a> 
						<a href="javascript:void(0);" class="btn bg-color-purple txt-color-white" onclick="$('#speech-btn .popover').fadeOut(50);">Close Popup</a> 
					</div>
				</div>
			</div>
		</div>
		<!-- end voice command -->
        
        <!-- multiple lang dropdown : find all flags in the image folder -->
        <!--ul class="header-dropdown-list hidden-xs">
            <li>...</li>
        </ul-->
        <!-- end multiple lang -->
    </div>
    <!-- end pulled right: nav area -->
</header>
<!-- [END HEADER] -->