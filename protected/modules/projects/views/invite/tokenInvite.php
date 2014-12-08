<?php
/**
 * Страница подачи заявки на участие (из письма участника)
 */
/* @var $this   InviteController */
/* @var $invite EventInvite */

// навигация
$this->breadcrumbs = array(
	'Приглашение',
);
?>
<div class="page-alternate">
    <div class="container">
        <div class="row">
            <div class="span12">
                <div class="title-page">
                    <h1 class="title">Приглашение</h1>
                    <!--h4 class="intro-description">Если ролей несколько - подать заявку можно на каждую.</h4-->
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span8 offset2">
                <?php 
                // виджет со списком приглашений
                $this->widget('application.modules.projects.extensions.TokenInvite.TokenInvite', array(
                    'key'    => $key,
                    'invite' => $invite
                ));
                // настройки оповещений участника (по типам проекта)
                if ( isset($invite->questionary) AND $invite->questionary instanceof Questionary )
                {/* @var $config Config */
                    $config = $invite->questionary->getConfigObject('projectTypesBlackList');
                    $accordionConfig = array(
                        'title'   => $config->title,
                        'content' => $this->widget('application.modules.questionary.extensions.widgets.QUserConfig.QUserConfig', array(
                            'questionary' => $invite->questionary,
                            'configName'  => 'projectTypesBlackList',
                        ), true),
                    );
                    if ( ! $config->isModifiedForModel($invite->questionary) )
                    {// настройка еще ни разу не редактировалась участником
                        $accordionConfig['collapse'] = false;
                    }
                    $this->widget('ext.ECMarkup.ECAccordion.ECAccordion', $accordionConfig);
                }
                ?>
            </div>
        </div>
    </div>
</div>