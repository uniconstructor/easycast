<?php
/* @var $this QuestionaryController */
/* @var $questionary Questionary */

Yii::import('application.modules.catalog.CatalogModule');
Yii::import('application.modules.catalog.models.*');

$this->breadcrumbs = array();
$this->breadcrumbs[QuestionaryModule::t('catalog')] = array('/catalog/catalog');

if ( $sectionId = CatalogModule::getNavigationParam('sectionId') AND $sectionId != 1 )
{
    $tabName = CatalogModule::getNavigationParam('tab');
    if ( ! $page = CatalogModule::getNavigationParam('page') )
    {
        $page = 1;
    }
    $section = CatalogSection::model()->findByPk($sectionId);
    $this->breadcrumbs[$section->name] = array('/catalog/catalog',
        'sectionid' => $sectionId,
        'Questionary_page' => $page);
    if ( $tabName )
    {
        foreach ( $section->instances as $tabInstance )
        {
            if ( $tabInstance->tab->shortname == $tabName )
            {
                $this->breadcrumbs[$tabInstance->tab->name] = array(
                    '/catalog/catalog',
                        'sectionId' => $sectionId,
                        'tab'       => $tabName,
                        'Questionary_page' => $page
                );
                break;
            }
        }
    }
}
$this->breadcrumbs[] = $questionary->user->fullname;

// Кнопка "редактировать"
$editIcon = '';
if ( $canEdit )
{// анкету пользователя может редактировать только админ и сам пользователь
    $editIcon = Yii::app()->getModule('questionary')->_assetsUrl.'/images/edit.png';
    $editIcon = CHtml::image($editIcon, Yii::t('coreMessages', 'edit'));
    $editIcon = CHtml::link($editIcon, 
        Yii::app()->createUrl('//questionary/questionary/update', array('id' => $questionary->id)));
}

?>
<div class="row">
    <div class="span12" style="margin-top:-40px;margin-bottom:-10px;">
        <div id="order_message" 
            class="<?php echo $orderMessageClass; ?>" 
            style="margin-top:20px;margin-bottom:-5px;<?php echo $orderMessageStyle; ?>">
            <?php // сообщение о том что участник приглашен на съемки
                echo $orderMessage; 
                echo '<br><br>';
                echo $dismissButton;
            ?>
        </div>
        <h2 class="pull-right">
            <?php // Имя, фамилия, возраст 
                echo $questionary->user->fullname;
                if ( $questionary->age )
                {
                    echo ', '.$questionary->age;
                }
            ?>
            <?php // кнопки "редактировать" и "пригласить" 
                echo '&nbsp;'.$editIcon.$inviteButton;
            ?>
        </h2>
    </div>
</div>
<div class="row">
    <div class="span5">
        <div>
        <?php // Список фотографий пользователя
            $this->widget('ext.ECMarkup.EThumbCarousel.EThumbCarousel', array(
                'previews'    => $questionary->getBootstrapPhotos('small'),
                'photos'      => $questionary->getBootstrapPhotos('medium'),
                'largePhotos' => $questionary->getBootstrapPhotos('large'),
            ));
        ?>
        </div>
    </div>
    <div class="span7">
        <div class="row">
            <div class="span6">
                <?php 
                // выводим список умений и достижений участника 
                $this->widget('application.modules.questionary.extensions.widgets.QUserBages.QUserBages', array(
                    'bages' => $questionary->bages,
                ));
                ?>
            </div>
        </div>
        <div class="row">
            <div class="span7">
            <?php 
            // Выводим всю остальную информацию о пользователе
            $this->widget('application.modules.questionary.extensions.widgets.QUserInfo.QUserInfo', array(
                'questionary' => $questionary,
                'activeTab'   => $activeTab,
            ));
            ?>
            </div>
        </div>
    </div>
</div>
<?php  
if ( Yii::app()->user->checkAccess('Admin') )
{// если админы вводят много анкет подряд - упростим им задачу, добавив кнопку внизу
    $newUserLink = CHtml::link('<i class="icon-plus icon-large"></i>&nbsp;Добавить еще анкету', 
        Yii::app()->createUrl('//user/admin/create'/*, array('lastId' => $questionary->id)*/),
        array('class' => 'btn btn-large btn-warning'));
    echo '<div class="row span12" style="text-align:center;">'.$newUserLink.'</div>';
}
?>