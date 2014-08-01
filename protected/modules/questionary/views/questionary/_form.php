<?php
/**
 * Форма анкеты участника
 * 
 * @todo использовать на этой странице модель динамической формы анкеты, а не самой анкеты 
 */
/* @var $form TbActiveForm  */
/* @var $questionary Questionary */
/* @var $this QuestionaryController */

// Выбор страны и города
Yii::import('ext.CountryCitySelectorRu.*');

// Загружаем дополнительные стили для формы:
// @todo удалить файл эти стили после переработки верстки анкеты, так как они станут не нужны 
$assetsUrl = CHtml::asset($this->module->basePath . DIRECTORY_SEPARATOR . 'assets');
Yii::app()->clientScript->registerCssFile($assetsUrl . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 
    'questionary-form.css');

?>
<div class="form wide">
    <?php
    $clientScriptManager = Yii::app()->clientScript;
    
    // создаем объект виджета для выбора страны и города
    $countryConfig['country']['default'] = 'RU';
    $countryConfig['country']['topKeys'] = array('RU','UA','BY');
    if ( $questionary->isFirstSave() AND Yii::app()->user->checkAccess('Admin') )
    {// устанавливаем Москву городом по умолчанию для админов
        $countryConfig['city']['default'] = '4400';
    }
    $countrySelector = new CountryCitySelectorRu($countryConfig);
    $countrySelector->controller = &$this;

    // настраиваем стандартные галочки "да/нет"
    $toggleBoxJsOptions = array(
        'on_label'  => Yii::t('coreMessages', 'yes'),
        'off_label' => Yii::t('coreMessages', 'no'),
    );
    
    // начало виджета формы редактирования анкеты
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'questionary-form',
        'enableAjaxValidation'   => true,
        'enableClientValidation' => false,
        'clientOptions' => array(
            'validateOnSubmit' => true,
            'validateOnChange' => true,
        ),
    ));

    // Устанавливаем правила для отображения/скрытия разделов формы
    // Общие разделы (по умолчанию всегда свернуты)
    
    // Перечисляем все id разделов в массиве, чтобы позже одним циклом создать код для их сворачивания
    // @todo сейчас по умолчанию свернуты все основные блоки. Потом можно будет придумать
    //       в зависимости от каких условий их изначально сворачивать или разворачивать
    $formPartItems = array();
    
    // Основная информация
    $formPartItems[] = array(
        'itemSelector'  => '#base_information_part',
    );
    // Контакты
    $formPartItems[] = array(
        'itemSelector'  => '#contact_information_part',
    );
    // Внешность
    $formPartItems[] = array(
        'itemSelector'  => '#looks_part',
    );
    // Опыт работы, умения и навыки
    $formPartItems[] = array(
        'itemSelector'  => '#experience_jobs_and_skills_part',
    );
    // Паспортные данные
    $formPartItems[] = array(
        'itemSelector'  => '#passportdata_part',
    );
    // Звания, призы и награды
    $formPartItems[] = array(
        'itemSelector'  => '#conditions_part',
    );
    
    // создаем по 1 виджету, на каждый сворачивающийся раздел формы
    foreach ( $formPartItems as $formPartItem )
    {
        // Устанавливаем опции виджета в зависимости от раздела
        $slideToggleOptions = array(
            'itemSelector'  => $formPartItem['itemSelector'],
            // общее правило: id каждого заголовка раздела формы состоит из id самого раздела + слово "_label"
            'titleSelector' => $formPartItem['itemSelector'].'_label',
            // По умолчанию сворачиваем все разделы формы
            'collapsed'     => $formPartItem['itemSelector'],
            'duration'      => 400,
            'arrow'         => false,
        );
        if ( ( ! $questionary->firstname OR ! $questionary->lastname OR ! $questionary->gender ) AND 
            $formPartItem['itemSelector'] == '#base_information_part' )
        {// первый раздел анкеты всегда по умолчанию развернут если не заполнены самые основные поля
            unset($slideToggleOptions['collapsed']);
        }
        // создаем сам виджет
        $this->widget('ext.slidetoggle.ESlidetoggle', $slideToggleOptions);
        // добавляем всплывающие подсказки к каждому заголовку раздела формы
        $sectionTooltipJsId = '_qSectionTooltip'.$formPartItem['itemSelector'];
        $sectionTooltipJs   = '$("'.$slideToggleOptions['titleSelector'].' > a").tooltip({title:"Нажмите чтобы развернуть",placement:"top"})';
        $clientScriptManager->registerScript($sectionTooltipJsId, $sectionTooltipJs, CClientScript::POS_END);
    }

    ////////////////////////////////////
    // правила отображения и сокрытия //
    // второстепенных полей формы     //
    // при загрузке страницы          //
    ////////////////////////////////////
    
    // Все поля скрываются при помощи JS, чтобы форма работала и была видна полностью даже при отключенных скриптах

    // Элементы которые возможно надо скрыть
    $hidedFormSubSections = array('newhaircolor', 'actoruniversities', 'films', 'films_part', 
        'emceelist', 'parodist', 'twin', 'modelschools', 'modeljobs', 'photomodeljobs', 'promomodeljobs',
        'dancetypes', 'stripdata', 'vocaltypes', 'voicetimbres', 'singlevel', 'instruments',
        'sporttypes', 'extremaltypes', 'skills', 'tricks', 'languages', 'inshurancecardnum',
        'passportexpires', 'awards', 'musicuniversities', 'titsize', 'tvshows', 'amateuractor',
        'actortheatres');
    
    foreach ( $hidedFormSubSections as $subSection )
    {// Скрываем все поля которые надо скрыть
        if ( ! $questionary->isDisplayedSection($subSection) )
        {
            $hideSubsectionScriptId = '_qHideSubSection#'.$subSection;
            $hideSubsectionScript   = 'jQuery("#'.$subSection.'").hide();';
            $clientScriptManager->registerScript($hideSubsectionScriptId, $hideSubsectionScript, CClientScript::POS_END);
        }
    }
    
    // скрываем размер груди при указании мужского пола
    $hideSubsectionScriptId = '_qHideSubSection#titsize_toggle';
    $hideSubsectionScript   = 'jQuery("#Questionary_gender").change(function()
        {
            if (jQuery("#Questionary_gender").val() == "male")
            {
                jQuery("#titsize").hide();
            }else
            {
                jQuery("#titsize").show();
            }
        });';
    $clientScriptManager->registerScript($hideSubsectionScriptId, $hideSubsectionScript, CClientScript::POS_END);
    
    // выводим специальный скрытый элемент, который каждую минуту посылает запрос на сайт, чтобы при длительном
    // заполнении анкеты не произошла потеря сессии и все данные не пропали
    $this->widget('ext.EHiddenKeepAlive.EHiddenKeepAlive', array(
            'url'    => Yii::app()->createAbsoluteUrl('//site/keepAlive'),
            'period' => 45,
        )
    );
    ?>
	<p class="note">
        <?= Yii::t('coreMessages', 'form_required_fields', array('{mark}' => '<span class="required">*</span>')); ?>
    </p>
	<?php 
	
	echo $form->errorSummary($questionary, null, null, array('id' => 'questionary-form-upper-es')); 
	
    if ( Yii::app()->user->checkAccess('Admin') )
    {// рейтинг анкеты (выставляется только администраторами)
        echo $form->dropDownListRow($questionary,'rating', $questionary->getFieldVariants('rating'));
    }
    ?>

	<fieldset id="base_information_part">
	<legend id="base_information_part_label">
        <a class="btn btn-large btn-warning">
        <i class="icon-chevron-down"></i>&nbsp;<?php echo QuestionaryModule::t('base_information'); ?></a>
    </legend>

        <?= $form->textFieldRow($questionary, 'lastname',   array('size' => 60,'maxlength' => 128)); ?>
        <?= $form->textFieldRow($questionary, 'firstname',  array('size' => 60,'maxlength' => 128)); ?>
        <?= $form->textFieldRow($questionary, 'middlename', array('size' => 60,'maxlength' => 128)); ?>
    
        <?php 
        // дата рождения
        echo $form->datepickerRow($questionary, 'formattedBirthDate', array(
                'options' => array(
                    'language'  => 'ru',
                    'format'    => 'dd.mm.yyyy',
                    'startView' => 'decade',
                    'weekStart' => 1,
                    'startDate' => '-75y',
                    'endDate'   => '-1y',
                    'autoclose' => true,
                ),
            ),
            array(
                'prepend' => '<i class="icon-calendar"></i>'
            )
        );
        ?>
        
        <?php echo $form->dropDownListRow($questionary, 'gender', $questionary->getFieldVariants('gender')); ?>
        <?php echo $form->textFieldRow($questionary, 'height', array('size' => 3, 'maxlength' => 6)); ?>
        <?php echo $form->textFieldRow($questionary,'weight', array('size' => 3, 'maxlength' => 6)); ?>
        <?php echo $form->dropDownListRow($questionary, 'wearsize', $questionary->getFieldVariants('wearsize')); ?>
        <?php echo $form->dropDownListRow($questionary, 'shoessize', $questionary->getFieldVariants('shoessize')); ?>
        
        <?php 
        // город проживания
        // @todo сделать выпадающий список городов зависимым от списка стран
        echo $form->labelEx($questionary, 'cityid');
            $cityOptions = array(
                'sourceUrl' => Yii::app()->createUrl('questionary/questionary/ajax?type=city&parenttype=country&parentid=RU'),
            );
            $countrySelector->cityActiveField('cityid', $questionary, $cityOptions);
        echo $form->error($questionary, 'cityid');
        ?>
        <hr>
	</fieldset>

	<fieldset id="contact_information_part">
	    <legend id="contact_information_part_label">
	        <a class="btn btn-large btn-warning">
	        <i class="icon-chevron-down"></i>&nbsp;<?= QuestionaryModule::t('contact_information'); ?></a>
	    </legend>
        <?= $form->textFieldRow($questionary, 'mobilephone', array('size' => 32, 'maxlength' => 32)); ?>
        <?= $form->textFieldRow($questionary, 'homephone',   array('size' => 32, 'maxlength' => 32)); ?>
        <?= $form->textFieldRow($questionary, 'addphone',    array('size' => 32, 'maxlength' => 32)); ?>
        <?= $form->textFieldRow($questionary, 'vkprofile',   array('size' => 60, 'maxlength' => 255)); ?>
        <?= $form->textFieldRow($questionary, 'fbprofile',   array('size' => 60, 'maxlength' => 255)); ?>
        <?= $form->textFieldRow($questionary, 'okprofile',   array('size' => 60, 'maxlength' => 255)); ?>
        <hr>
	</fieldset>

	<fieldset id="looks_part">
	    <legend id="looks_part_label">
	        <a class="btn btn-large btn-warning">
	        <i class="icon-chevron-down"></i>&nbsp;<?= QuestionaryModule::t('looks'); ?></a>
        </legend>

        <?php 
        // Фотографии
        echo $form->labelEx($questionary, 'photos');
        // Рекомендации по добавлению фотографий
        $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
                array('field' => 'photos'));
        
        if ( $questionary->galleryBehavior->getGallery() === null )
        {// @todo эта ветка никогда не выполнится потому что анкета уже давно не может быть создана сама по себе
            // она создается автоматически и только вместе с пользователем
            // Нужно убрать это условие и проверить что все в порядке
            echo '<div class="alert">Сохраните анкету перед загрузкой фотографий</div>';
        }else
        {
            $this->widget('GalleryManager', array(
                 'gallery'         => $questionary->galleryBehavior->getGallery(),
                 'controllerRoute' => '/questionary/gallery'
            ));
        }
        echo $form->error($questionary, 'galleryid');
        ?>
        
        <fieldset class="qform_subsection">
            <legend><?= QuestionaryModule::t('video'); ?></legend>
            <?php
            // пояснение для списка видео
            $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription',
                array('field' => 'video')
            );
            // список видео
            $this->widget('ext.ECEditVideo.ECEditVideo', array(
                'questionary' => $questionary,
            ));
            
            ?>
        </fieldset>
        
		<?= $form->dropDownListRow($questionary, 'looktype', $questionary->getFieldVariants('looktype')); ?>
		<?= $form->dropDownListRow($questionary, 'haircolor', $questionary->getFieldVariants('haircolor')); ?>
		<?= $form->dropDownListRow($questionary, 'hairlength', $questionary->getFieldVariants('hairlength')); ?>
		<?= $form->dropDownListRow($questionary, 'eyecolor', $questionary->getFieldVariants('eyecolor')); ?>
		<?= $form->dropDownListRow($questionary, 'physiquetype', $questionary->getFieldVariants('physiquetype')); ?>
		
        <div>
        <fieldset id="addchars" class="qform_subsection">
            <legend id="addchars_label" class="qform_subsection_label">
                <?= QuestionaryModule::t('addchar_label'); ?>
            </legend>
            <?php
            // список особенностей внешности и доп. характеристик
            $this->widget('questionary.extensions.widgets.QEditAddChars.QEditAddChars', array(
                'questionary' => $questionary,
            ));
            ?>
        </fieldset>
        </div>

        <label><?= QuestionaryModule::t('parameters'); ?></label>
        <div class="form-inline qform_subsection">
            <?= $form->textFieldRow($questionary, 'chestsize', array('maxlength' => 6, 'style' => 'width:50px;')); ?>
            <?= $form->textFieldRow($questionary, 'waistsize', array('maxlength' => 6, 'style' => 'width:50px;')); ?>
            <?= $form->textFieldRow($questionary, 'hipsize',   array('maxlength' => 6, 'style' => 'width:50px;')); ?>
        </div>

    <div>
        <div id="titsize">
            <?= $form->dropDownListRow($questionary, 'titsize', $questionary->getFieldVariants('titsize')); ?>
        </div>
    </div>

    <?php 
    // есть ли у вас татуировки?
    echo $form->widgetRow('ext.ECMarkup.ECToggleInput.ECToggleInput', array(
        'model'     => $questionary,
        'attribute' => 'hastatoo',
    ));
    ?>
    <hr>
	</fieldset>

	<fieldset id="experience_jobs_and_skills_part">
        <legend id="experience_jobs_and_skills_part_label">
            <a class="btn btn-large btn-warning">
                <i class="icon-chevron-down"></i>&nbsp;<?= QuestionaryModule::t('experience_jobs_and_skills'); ?>
            </a>
        </legend>

        <?php 
        // Профессиональный актер
        $this->widget('ext.EToggleBox.EToggleBox', array(
              'model'     => $questionary,
              'attribute' => 'isactor',
              'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
                    // при включении: скрываем поле "непрофессиональный актер", показываем список вузов
                    // показываем фильмографию
                    // @todo выключаем пункт "непрофессиональный актер" (если он был выбран)
                    'after_on'  => 'js:function () {
                        $("#actoruniversities").fadeIn(200);
                        $("#amateuractor").fadeOut(200);
                        $("#films_part").fadeIn(200);
                        }',
                    // при выключении: показываем "непрофессиональный актер", скрываем фильмографию, 
                    // скрываем список актерских ВУЗов
                    'after_off' => 'js:function () {
                        $("#actoruniversities").fadeOut(200);
                        $("#amateuractor").fadeIn(200);
                        $("#films_part").fadeOut(200);
                        }'))
                     ));
         ?>
    
    <div>
        <fieldset id="actoruniversities" class="qform_subsection">
            <legend class="qform_subsection_label"><?php echo QuestionaryModule::t('actor_universities_label'); ?></legend>
            <?php
            // пояснение для списка учебных заведений
            $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
                array('field' => 'actoruniversity')
            );
            // список театральных ВУЗов
            $this->widget('questionary.extensions.widgets.QEditActorUniversities.QEditActorUniversities', 
                array('questionary' => $questionary)
            );
            ?>
        </fieldset>
    </div>
    
    <div>
        <div id="amateuractor">
            <?php 
            // непрофессиональный актер
            $this->widget('ext.EToggleBox.EToggleBox', array(
              'model'     => $questionary,
              'attribute' => 'isamateuractor',
              'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
                      'after_on'  => 'js:function () {$("#films_part").fadeIn(200);}',
                      'after_off' => 'js:function () {$("#films_part").fadeOut(200);}',
                     )
                ),
            ));
            // пояснение для списка фильмов
            $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription',
                array('field' => 'isamateuractor')
            );
            ?>
        </div>
    </div>
    
    <?php
    if ( Yii::app()->user->checkAccess('Admin') )
    {// медийный актер (проставляется только админами)
        $this->widget('ext.EToggleBox.EToggleBox', array(
            'model'     => $questionary,
            'attribute' => 'ismediaactor',
            'options'   => $toggleBoxJsOptions,
        ));
    }
    ?>
    
    <div>
        <div id="films_part">
            <?php 
            // Снимались ли вы в фильмах
            $this->widget('ext.EToggleBox.EToggleBox', array(
                'model'     => $questionary,
                'attribute' => 'hasfilms',
                'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
                        'after_on'  => 'js:function () {$("#films").fadeIn(200);}',
                        'after_off' => 'js:function () {$("#films").fadeOut(200);}',
                        )
                    ),
                )
            );
            ?>
            <div>
                <fieldset id="films" class="qform_subsection">
                    <legend class="qform_subsection_label"><?php echo QuestionaryModule::t('films_label'); ?></legend>
                    <?php
                    // пояснение для списка фильмов
                    $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
                        array('field' => 'hasfilms')
                    );
                    // список фильмов
                    $this->widget('questionary.extensions.widgets.QEditFilms.QEditFilms', array(
                           'questionary' => $questionary,
                        )
                    );
                    ?>
                </fieldset>
            </div>
        </div>
    </div>
    
    <?php 
    // Есть ли опыт работы в театре
    $this->widget('ext.EToggleBox.EToggleBox', array(
        'model'     => $questionary,
        'attribute' => 'istheatreactor',
        'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
                    'after_on'  => 'js:function () {$("#actortheatres").fadeIn(200);}',
                    'after_off' => 'js:function () {$("#actortheatres").fadeOut(200);}',
                )
            )
        ));
    ?>
    
    <div>
        <fieldset id="actortheatres" class="qform_subsection">
            <legend class="qform_subsection_label"><?php echo QuestionaryModule::t('theatres'); ?></legend>
            <?php
            // пояснение для списка театров
            $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
                array('field' => 'actortheatres')
            );
            // список театров
            $this->widget('questionary.extensions.widgets.QEditTheatres.QEditTheatres', array(
                'questionary' => $questionary,
            ));
            ?>
        </fieldset>
    </div>
    
    <?php 
    // Статист
    $this->widget('ext.EToggleBox.EToggleBox', array(
        'model'     => $questionary,
        'attribute' => 'isstatist',
        'options'   => $toggleBoxJsOptions));
    // пояснение для статиста
    $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
        array('field' => 'isstatist'));
    ?>
    
    <?php 
    // Актер массовых сцен
    $this->widget('ext.EToggleBox.EToggleBox', array(
        'model'     => $questionary,
        'attribute' => 'ismassactor',
        'options'   => $toggleBoxJsOptions));
    // пояснение для актера массовых сцен
    $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
        array('field' => 'ismassactor'));
    ?>

    <?php 
    // Ведущий
    $this->widget('ext.EToggleBox.EToggleBox', array(
        'model'     => $questionary,
        'attribute' => 'isemcee',
        'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
              'after_on'  => 'js:function () {$("#emceelist").fadeIn(200);}',
              'after_off' => 'js:function () {$("#emceelist").fadeOut(200);}'))
        ));
    // пояснение для ведущего
    $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
        array('field' => 'isemcee'));
    ?>
    <div>
	<fieldset id="emceelist" class="qform_subsection">
        <?php
        // мероприятия ведущего
        $this->widget('questionary.extensions.widgets.QEditEmceeEvents.QEditEmceeEvents', array(
               'questionary' => $questionary,
            )
        );
        ?>
	</fieldset>
    </div>
    
    <?php 
    // Телеведущий
    $this->widget('ext.EToggleBox.EToggleBox', array(
        'model'     => $questionary,
        'attribute' => 'istvshowmen',
        'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
            'after_on'  => 'js:function () {$("#tvshows").fadeIn(200);}',
            'after_off' => 'js:function () {$("#tvshows").fadeOut(200);}'))
        ));
    
    $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
        array('field' => 'istvshowmen')
    );
    ?>
	
    <div>
	<fieldset id="tvshows" class="qform_subsection">
    <?php
    // опыт работы телеведущим
    $this->widget('questionary.extensions.widgets.QEditTvshows.QEditTvshows', array(
        'questionary' => $questionary,
    ));
    ?>
	</fieldset>
    </div>

    <?php 
    // Пародист
    $this->widget('ext.EToggleBox.EToggleBox', array(
        'model'     => $questionary,
        'attribute' => 'isparodist',
        'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
              'after_on'  => 'js:function () {$("#parodist").fadeIn(200);}',
              'after_off' => 'js:function () {$("#parodist").fadeOut(200);}'))
        ));
    
    $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
        array('field' => 'isparodist')
    );
    ?>
	
    <div>
	<fieldset id="parodist" class="qform_subsection">
    	<?php 
    	// список образов для пародиста
    	$this->widget('questionary.extensions.widgets.QEditParodistList.QEditParodistList', array(
    	    'questionary' => $questionary,
    	));
        ?>
	</fieldset>
    </div>

    <?php 
	// Двойник
    $this->widget('ext.EToggleBox.EToggleBox', array(
        'model'     => $questionary,
        'attribute' => 'istwin',
        'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
            'after_on'  => 'js:function () {$("#twin").fadeIn(200);}',
            'after_off' => 'js:function () {$("#twin").fadeOut(200);}'))
        ));
        
    $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
        array('field' => 'istwin')
    );
    ?>
	
    <div>
	<fieldset id="twin" class="qform_subsection">
    	<?php 
    	// список образов двойника
    	$this->widget('questionary.extensions.widgets.QEditTwinList.QEditTwinList', array(
    	    'questionary' => $questionary,
    	));
    	?>
	</fieldset>
    </div>

    <?php 
	// Модель
    $this->widget('ext.EToggleBox.EToggleBox', array(
        'model'     => $questionary,
        'attribute' => 'ismodel',
        'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
            'after_on'  => 'js:function () {$("#modelschools").fadeIn(200);$("#modeljobs").fadeIn(200);}',
            'after_off' => 'js:function () {$("#modelschools").fadeOut(200);$("#modeljobs").fadeOut(200);}'))
        ));
    // пояснение для моделей
    $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
        array('field' => 'ismodel')
    );
    ?>
    <div>
	<fieldset id="modelschools" class="qform_subsection">
        <?php
        // Модельные школы
        $this->widget('questionary.extensions.widgets.QEditModelSchools.QEditModelSchools', array(
            'questionary' => $questionary,
        ));
        ?>
	</fieldset>
    </div>
    <div>
    <fieldset id="modeljobs" class="qform_subsection">
        <?php
        // опыт работы моделью
        $this->widget('questionary.extensions.widgets.QEditModelJobs.QEditModelJobs', array(
            'questionary' => $questionary,
        ));
        ?>
    </fieldset>
    </div>

    <?php 
    // Фотомодель
    $this->widget('ext.EToggleBox.EToggleBox', array(
        'model'     => $questionary,
        'attribute' => 'isphotomodel',
        'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
            'after_on'  => 'js:function () {$("#photomodeljobs").fadeIn(200);}',
            'after_off' => 'js:function () {$("#photomodeljobs").fadeOut(200);}'))
        ));
    $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
        array('field' => 'isphotomodel')
    );
    ?>
    <div>
    <fieldset id="photomodeljobs" class="qform_subsection">
        <?php
        // опыт работы фотомоделью
        $this->widget('questionary.extensions.widgets.QEditPhotoModelJobs.QEditPhotoModelJobs', array(
            'questionary' => $questionary,
        ));
        ?>
    </fieldset>
    </div>

    <?php // Промо-модель
    $this->widget('ext.EToggleBox.EToggleBox', array(
        'model'     => $questionary,
        'attribute' => 'ispromomodel',
        'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
            'after_on'  => 'js:function () {$("#promomodeljobs").fadeIn(200);}',
            'after_off' => 'js:function () {$("#promomodeljobs").fadeOut(200);}'))
        ));
        $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
            array('field' => 'ispromomodel'));
    ?>
    
    <div>
    <fieldset id="promomodeljobs" class="qform_subsection">
        <?php
        // опыт работы промо-моделью
        $this->widget('questionary.extensions.widgets.QEditPromoModelJobs.QEditPromoModelJobs', array(
            'questionary' => $questionary,
        ));
        ?>
    </fieldset>
    </div>

    <?php 
    // Танцор
    $this->widget('ext.EToggleBox.EToggleBox', array(
        'model'     => $questionary,
        'attribute' => 'isdancer',
        'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
            'after_on'  => 'js:function () {$("#dancetypes").fadeIn(200);}',
            'after_off' => 'js:function () {$("#dancetypes").fadeOut(200);}'))
        ));
        
        $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
                array('field' => 'isdancer')
        );
    ?>

    <div>
    <fieldset id="dancetypes" class="qform_subsection">
    <legend class="qform_subsection_label"><?php echo QuestionaryModule::t('dancetypes_label'); ?></legend>
        <?php
        // список стилей танца
        $this->widget('questionary.extensions.widgets.QEditDanceTypes.QEditDanceTypes', array(
            'questionary' => $questionary,
        ));
        ?>
    </fieldset>
    </div>

    <?php 
    // Стриптиз
    $this->widget('ext.EToggleBox.EToggleBox', array(
        'model'     => $questionary,
        'attribute' => 'isstripper',
        'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
            'after_on'  => 'js:function () {$("#stripdata").fadeIn(200);}',
            'after_off' => 'js:function () {$("#stripdata").fadeOut(200);}'))
        ));
    ?>
    
    <div>
	<fieldset id="stripdata" class="qform_subsection">
		<?php echo $form->dropDownListRow($questionary,'striptype', $questionary->getFieldVariants('striptype')); ?>
		<?php echo $form->error($questionary,'striptype'); ?>

		<?php echo $form->dropDownListRow($questionary,'striplevel', $questionary->getFieldVariants('level')); ?>
		<?php echo $form->error($questionary,'striplevel'); ?>
	</fieldset>
    </div>

    <?php // Вокал
    $this->widget('ext.EToggleBox.EToggleBox', array(
        'model'     => $questionary,
        'attribute' => 'issinger',
        'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
            // При выборе пункта "вокал" полявляются дополнительные поля "тип вокала", "тембр голоса",
            // "уровень вокала" и "музыкальные ВУЗы"
            'after_on'  => 'js:function () {
                  $("#vocaltypes").fadeIn(200);
                  $("#voicetimbres").fadeIn(200);
                  $("#singlevel").fadeIn(200);
                  $("#musicuniversities").fadeIn(200);}',
            // При выключении пункта "вокал" убираются все поля, с ним связанные.
            // Поле "Музыкальные ВУЗы" убирается только если ниже в форме не выбран пункт "музыкант"
            // (так как у нас используется один список музыкальных ВУЗов для певцов и для музыкантов)
            'after_off' => 'js:function () {
                  $("#vocaltypes").fadeOut(200);
                  $("#voicetimbres").fadeOut(200);
                  $("#singlevel").fadeOut(200);
                  if ($("#Questionary_ismusician").attr("checked") != "checked" &&
                      $("#Questionary_ismusician").attr("checked") != true )
                      {
                          $("#musicuniversities").fadeOut(200);                                                                                  
                      }
                  }'
                )
            ),
        )
    );
    ?>
    <div>
	<fieldset id="vocaltypes" class="qform_subsection">
	    <?php
	    // тип вокала
	    // echo $form->labelEx($questionary, 'type');
	    $this->widget('questionary.extensions.widgets.QEditVocalTypes.QEditVocalTypes', array(
            'questionary' => $questionary,
        ));
    	?>
	</fieldset>

    <fieldset id="voicetimbres" class="qform_subsection">
	    <?php
	    // тембр голоса
	    // echo $form->labelEx($questionary, 'voicetimbre');
	    $this->widget('questionary.extensions.widgets.QEditVoiceTimbres.QEditVoiceTimbres', array(
            'questionary' => $questionary,
        ));
    	?>
	</fieldset>

    <div id="singlevel" class="qform_subsection">
        <?php echo $form->dropDownListRow($questionary,'singlevel', $questionary->getFieldVariants('level')); ?>
    </div>
    </div>

    <?php 
	// Музыкант
    $this->widget('ext.EToggleBox.EToggleBox', array(
        'model'     => $questionary,
        'attribute' => 'ismusician',
        'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
              'after_on'  => 'js:function () {
                    $("#instruments").fadeIn(200);
                    $("#musicuniversities").fadeIn(200);
                    }',
              // Поле "Музыкальные ВУЗы" убирается только если выше в форме не выбран пункт "вокал"
              // (так как у нас используется один список музыкальных ВУЗов для певцов и для музыкантов)
              'after_off' => 'js:function () {
                    $("#instruments").fadeOut(200);
                    if ($("#Questionary_issinger").attr("checked") != "checked" &&
                        $("#Questionary_issinger").attr("checked") != true )
                        {
                            $("#musicuniversities").fadeOut(200);                                                                                  
                        }
                    }'
                )
            ),
        )
    );
    ?>

    <div>
    <fieldset id="instruments" class="qform_subsection">
        <legend class="qform_subsection_label"><?php echo QuestionaryModule::t('instruments_label'); ?></legend>
        <?php
        // список освоеных музыкальных инструментов
        $this->widget('questionary.extensions.widgets.QEditInstruments.QEditInstruments', array(
            'questionary' => $questionary,
        ));
        ?>
    </fieldset>
    </div>

    <div>
    <fieldset id="musicuniversities" class="qform_subsection">
        <legend class="qform_subsection_label">
            <?php echo QuestionaryModule::t('music_universities_label'); ?>
        </legend>
        <?php
        $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
            array('field' => 'musicuniversity')
        );
        // список музыкальных ВУЗов
        // Отображается если указан пункт "вокал" или "музыкант"
        $this->widget('questionary.extensions.widgets.QEditMusicUniversities.QEditMusicUniversities', array(
            'questionary' => $questionary,
        ));
        ?>
    </fieldset>
    </div>

    <?php 
    // Спортсмен
    $this->widget('ext.EToggleBox.EToggleBox', array(
        'model'     => $questionary,
        'attribute' => 'issportsman',
        'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
                'after_on'  => 'js:function () {$("#sporttypes").fadeIn(200);}',
                'after_off' => 'js:function () {$("#sporttypes").fadeOut(200);}',
                )
            ),
        )
    );
    $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
        array('field' => 'issportsman')
    );
    ?>
	
    <div>
	<fieldset id="sporttypes" class="qform_subsection">
    	<?php 
    	// виды спорта
    	$this->widget('questionary.extensions.widgets.QEditSportTypes.QEditSportTypes', array(
	        'questionary' => $questionary,
	    ));
    	?>
	</fieldset>
    </div>

    <?php
    // Экстремал
    $this->widget('ext.EToggleBox.EToggleBox', array(
        'model'     => $questionary,
        'attribute' => 'isextremal',
        'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
              'after_on'  => 'js:function () {$("#extremaltypes").fadeIn(200);}',
              'after_off' => 'js:function () {$("#extremaltypes").fadeOut(200);}'))
        )
    );
    $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
        array('field' => 'isextremal')
    );
    ?>
	
    <div>
	<fieldset id="extremaltypes" class="qform_subsection">
    	<?php 
    	// экстремальные виды спорта
    	$this->widget('questionary.extensions.widgets.QEditExtremalTypes.QEditExtremalTypes', array(
    	    'questionary' => $questionary,
    	));
    	?>
	</fieldset>
    </div>

    <?php 
	// Атлет
    $this->widget('ext.EToggleBox.EToggleBox', array(
            'model'     => $questionary,
            'attribute' => 'isathlete',
            'options'   => $toggleBoxJsOptions
        )
    );
    $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
        array('field' => 'isathlete'));
    ?>

    <?php 
    // Дополнительные умения и навыки 
    $this->widget('ext.EToggleBox.EToggleBox', array(
        'model'     => $questionary,
        'attribute' => 'hasskills',
        'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
            'after_on'  => 'js:function () {$("#skills").fadeIn(200);}',
            'after_off' => 'js:function () {$("#skills").fadeOut(200);}'))
       ));
    $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
        array('field' => 'hasskills'));
    ?>
	
    <div>
	<fieldset id="skills" class="qform_subsection">
    	<?php 
    	// умения и навыки
    	$this->widget('questionary.extensions.widgets.QEditSkills.QEditSkills', array(
    	    'questionary' => $questionary,
    	));
    	?>
	</fieldset>
    </div>

    <?php 
	// Выполнение трюков
    $this->widget('ext.EToggleBox.EToggleBox', array(
        'model'     => $questionary,
        'attribute' => 'hastricks',
        'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
              'after_on'  => 'js:function () {$("#tricks").fadeIn(200);}',
              'after_off' => 'js:function () {$("#tricks").fadeOut(200);}'))
        )
    );
    $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
        array('field' => 'hastricks'));
    ?>
    
	<div>
	<fieldset id="tricks" class="qform_subsection">
    	<?php 
    	// список трюков для каскадера
    	$this->widget('questionary.extensions.widgets.QEditTricks.QEditTricks', array(
    	    'questionary' => $questionary,
    	));
    	?>
	</fieldset>
    </div>

    <?php 
	// Иностранные языки
    $this->widget('ext.EToggleBox.EToggleBox', array(
        'model'     => $questionary,
        'attribute' => 'haslanuages',
        'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
            'after_on'  => 'js:function () {$("#languages").fadeIn(200);}',
            'after_off' => 'js:function () {$("#languages").fadeOut(200);}'))
        )
    );
    ?>

    <div>
	<fieldset id="languages" class="qform_subsection">
        <legend class="qform_subsection_label">
            <?php echo QuestionaryModule::t('languages_label'); ?>
        </legend>
        <?php
        // список иностранных языков
        $this->widget('questionary.extensions.widgets.QEditLanguages.QEditLanguages', array(
               'questionary' => $questionary,
            )
        );
        ?>
	</fieldset>
    </div>
    
    <?php 
    // Звания призы и награды
    $this->widget('ext.EToggleBox.EToggleBox', array(
        'model'     => $questionary,
        'attribute' => 'hasawards',
        'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
            'after_on'  => 'js:function () {$("#awards").fadeIn(200);}',
            'after_off' => 'js:function () {$("#awards").fadeOut(200);}'))
        ));
    ?>
       
   <div>
       <fieldset id="awards" class="qform_subsection">
           <?php
            // список достижений и наград
            $this->widget('questionary.extensions.widgets.QEditAwards.QEditAwards', array(
               'questionary' => $questionary,
            ));
           ?>
       </fieldset>
    </div>
    <hr>
	</fieldset>

    <fieldset id="conditions_part">
        <legend id="conditions_part_label">
            <a class="btn btn-large btn-warning"><i class="icon-chevron-down"></i>&nbsp;<?php echo QuestionaryModule::t('recording_conditions_label'); ?></a>
        </legend>
            <?php  
		    // Ночные съемки (да/нет)
            $this->widget('ext.EToggleBox.EToggleBox', array(
                'model'     => $recordingConditions,
                'attribute' => 'isnightrecording',
                'options'   => $toggleBoxJsOptions));
            // Ночные съемки (пояснение)
            $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
                array('field' => 'isnightrecording'));
            ?>
        
            <?php  
            // Съемка топлесс
            // @todo выводить только для женщин и только с возрастом больше 18
            $this->widget('ext.EToggleBox.EToggleBox', array(
                'model'     => $recordingConditions,
                'attribute' => 'istoplessrecording',
                'options'   => $toggleBoxJsOptions));
            // Съемка топлесс (пояснение)
            $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
                array('field' => 'istoplessrecording')
            );
            ?>
        
            <?php
            // Благотворительные акции
            $this->widget('ext.EToggleBox.EToggleBox', array(
                'model'     => $recordingConditions,
                'attribute' => 'isfreerecording',
                'options'   => $toggleBoxJsOptions));
            // Благотворительные акции (пояснение)
            $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
                array('field' => 'isfreerecording')
            );
            ?>
        
            <?php
            // Согласны ли вы ездить в коммандировки
            $this->widget('ext.EToggleBox.EToggleBox', array(
                'model'     => $recordingConditions,
                'attribute' => 'wantsbusinesstrips',
                'options'   => $toggleBoxJsOptions));
            ?>

            <?php 
            // Имеется ли загранпаспорт
            $this->widget('ext.EToggleBox.EToggleBox', array(
                'model'     => $recordingConditions,
                'attribute' => 'hasforeignpassport',
                'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
                      'after_on'  => 'js:function () {$("#passportexpires").fadeIn(200);}',
                      'after_off' => 'js:function () {$("#passportexpires").fadeOut(200);}'))
               ));
            ?>
    
        <div>
            <div id="passportexpires">
                <?php 
                /*echo $form->labelEx($recordingConditions, 'passportexpires');
                // Срок истечения загранпаспорта
                $this->widget('ext.ActiveDateSelect',
                    array(
                         'model'     => $recordingConditions,
                         'attribute' => 'passportexpires',
                         'reverse_years' => false,
                         'field_order'   => 'DMY',
                         'start_year' => date("Y", time()),
                         'end_year'   => date("Y", time()+20*365*24*3600),
                    )
                );
                echo $form->error($recordingConditions,'passportexpires');*/
                ?>
            </div>
        </div>
        
        <?php 
        // размер оплаты для участия в съемках 
        echo $form->textFieldRow($recordingConditions, 'salary', array('size'=>10, 'maxlength'=>10));
        // размер оплаты (пояснение)
        $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription',
            array('field' => 'salary'));
        ?>
        
        <?php 
        // дополнительные условия для участия в съемках 
        echo $form->textAreaRow($recordingConditions, 'custom');
        ?>
        <hr>
    </fieldset>
    
    <?php 
    // Блок полей, доступных только администраторам
    if ( Yii::app()->user->checkAccess('Admin') )
    {
        // Статус анкеты (только для админов)
        $allowedStatuses = $questionary->getAllowedStatuses();
        $dropDownStatuses = array();
        foreach ( $allowedStatuses as $allowedStatus )
        {
            $dropDownStatuses[$allowedStatus] = $questionary->getStatustext($allowedStatus);
        }
        echo $form->dropDownListRow($questionary, 'status', $dropDownStatuses);
        // Статус анкеты (пояснение)
        $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription',
            array('field' => 'status'));
        
        // Комментарий модератора или администратора (только для админов)
        // Используется при отправке анкеты на доработку
        // Присылается пользователю в письме
        echo $form->textAreaRow($questionary, 'admincomment');
        $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription',
            array('field' => 'admincomment'));
        
        // Комментарий к анкете - дополнительная информация об участнике
        // можно писать все что угодна, участник это поле не видит никогда
        // Выделяем его очень ярко, чтобы ни в коем случае не ошибиться
        ?>
        <div class="ec-round-the-corner" style="background-color:#aaa;padding:20px;">
        <br><br>
        <?php
        echo $form->labelEx($questionary, 'privatecomment');
        // Поле длинное, так что мы можем позволить себе RichText редактор
        $this->widget('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
            'model' => $questionary,
            'attribute' => 'privatecomment',
            'options' => array(
                'lang' => 'ru',
            ),
        ));
        echo $form->error($questionary, 'privatecomment');
        // Комментарий к анкете (пояснение)
        $this->widget('questionary.extensions.widgets.QFieldDescription.QFieldDescription',
            array(
                'field' => 'privatecomment',
                'type'  => 'info'));
        ?>
        <br><br>
        </div>
    <?php
    }// конец блока полей, доступного только администроторам
    ?>
    <div class="form-actions">
        <?php
        echo $form->errorSummary($questionary, null, null, array('id' => 'questionary-form-footer-es'));
        // Кнопка сохранения 
        $form->widget('bootstrap.widgets.TbButton',
            array(
                'buttonType'  => 'submit',
                'type'        => 'success',
                'size'        => 'large',
                'label'       => Yii::t('coreMessages', 'save'),
                'htmlOptions' => array('id' => 'save_questionary')
            ));
        ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->
<?php
if ( Yii::app()->user->checkAccess('Admin') )
{// @todo загрузка файлов видео пока что только для админов
    Yii::import("xupload.models.XUploadForm");
    $xUploadForm = new XUploadForm;
    
    $this->widget('xupload.XUpload', array(
        'url'             => Yii::app()->createUrl("//questionary/questionary/upload", array('objectId' => $questionary->id)),
        'model'           => $xUploadForm,
        'attribute'       => 'file',
        'autoUpload'      => true,
        'previewImages'   => false,
        'imageProcessing' => false,
        'multiple'        => false,
    ));
}
// Выводим здесь все всплывающие modal-формы для сложных значений
// Их оказалось нельзя выводить в середине формы анкеты потому что вложенные виджеты форм в Yii не допускаются
// Сами формы генерируются по ходу отрисовки формы и запоминаются в клипы, а затем выводятся здесь
$clips = Yii::app()->getModule('questionary')->formClips;
foreach ( $clips as $clip )
{
    echo $this->clips[$clip];
}