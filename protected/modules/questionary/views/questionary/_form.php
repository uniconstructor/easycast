<?php
/**
 * Форма анкеты участника
 * 
 * @var QuestionaryController $this
 * @var Questionary $questionary
 * @var TbActiveForm $form
 * 
 * @todo не отображается список ошибок в конце формы
 */

// Подключаем расширения для полей формы

// Выбор даты по календарю
Yii::import('ext.ActiveDateSelect');
// Выбор страны и города
Yii::import('ext.CountryCitySelectorRu.*');

?>

<div class="form wide">

    <?php
    $clientScriptManager = Yii::app()->clientScript;
    
    // создаем объект виджета для выбора страны и города
    $countryConfig['country']['default'] = 'RU';
    $countryConfig['country']['topKeys'] = array('RU','UA','BY');
    if ( $questionary->isFirstSave() AND Yii::app()->user->isSuperuser )
    {// устанавливаем Москву городом по умолчанию для админов
        $countryConfig['city']['default'] = '4400';
    }
    $countrySelector = new CountryCitySelectorRu($countryConfig);
    $countrySelector->controller = &$this;

    // настраиваем стандартные галочки "да/нет"
    $toggleBoxJsOptions = array(
        'on_label'  => Yii::t('coreMessages','yes'),
        'off_label' => Yii::t('coreMessages','no'),
    );
    
    // начало виджета формы редактирования анкеты
    /* @var $form TbActiveForm  */
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'questionary-form',
        'enableAjaxValidation'   => true,
        'enableClientValidation' => false,
        'clientOptions' => array(
            'validateOnSubmit' => true,
            'validateOnChange' => true,
        ),
        //'htmlOptions' => array('class' => 'well'),
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
        $sectionTooltipJs = '$("'.$slideToggleOptions['titleSelector'].' > a").tooltip({title:"Нажмите чтобы развернуть",placement:"top"})';
        $clientScriptManager->registerScript($sectionTooltipJsId, $sectionTooltipJs, CClientScript::POS_END);
    }

    ////////////////////////////////////
    // правила отображения и сокрытия //
    // второстепенных полей формы     //
    // при загрузке страницы          //
    ////////////////////////////////////
    
    // Все поля скрываются при помощи JS, чтобы форма работала и была видна полностью даже при отключенных скриптах

    // Элементы которые возможно надо скрыть
    $hidedFormSubSections = array('newhaircolor', 'actoruniversities', 'films', 'films_part', 'emceelist', 'parodist',
              'twin', 'modelschools', 'modeljobs', 'photomodeljobs', 'promomodeljobs',
              'dancetypes', 'stripdata', 'vocaltypes', 'voicetimbres', 'singlevel', 'instruments',
              'sporttypes', 'extremaltypes', 'skills', 'tricks', 'languages', 'inshurancecardnum',
              'passportexpires', 'awards', 'musicuniversities', 'titsize', 'tvshows', 'amateuractor',
              'actortheatres');
    // Элементы которые возможно надо свернуть
    //$collapsedFormSubSections = array('addchars');
    
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
    
    // Не разрешаем сохранять форму, пока пользователь не даст согласия с условиями сайта
    // @todo изменить проверку прав
    /*if ( ! $user->policyagreed AND ! Yii::app()->user->isSuperuser )
    {// отключаем кнопку сохранения только если пользователь не согласен с условиями сайта,
        // и он не админ 
        $hideSubmitScriptId = '_qHideSubmit';
        $hideSubmitScript   = '$("#save_questionary").addClass("disabled");$("#save_questionary").attr("disabled", "disabled");';
        $clientScriptManager->registerScript($hideSubmitScriptId, $hideSubmitScript, CClientScript::POS_END);
    }*/
    
    // выводим специальный скрытый элемент, который каждую минуту посылает запрос на сайт, чтобы при длительном
    // заполнении анкеты не произошла потеря сессии и все данные не пропали
    $this->widget('ext.EHiddenKeepAlive.EHiddenKeepAlive', array(
            'url'    => Yii::app()->createAbsoluteUrl('//site/keepAlive'),
            'period' => 45,
        )
    );
    ?>
	<p class="note">
        <?php echo Yii::t('coreMessages', 'form_required_fields', array('{mark}' => '<span class="required">*</span>')); ?>
    </p>
	<?php echo $form->errorSummary($questionary, null, null, array('id' => 'questionary-form-upper-es')); ?>
    
    <?php 
    // id анкеты
    echo CHtml::hiddenField('qid', $questionary->id);
    
    // рейтинг анкеты (выставляется только администрацией)
    if ( Yii::app()->user->isSuperuser )
    {
        echo $form->dropDownListRow($questionary,'rating', $questionary->getFieldVariants('rating'));
        echo $form->error($questionary,'rating');
    }
    ?>

	<fieldset id="base_information_part">
	<legend id="base_information_part_label">
        <a class="btn btn-large btn-warning">
        <i class="icon-chevron-down"></i>&nbsp;<?php echo QuestionaryModule::t('base_information'); ?></a>
    </legend>

        <?php echo $form->textFieldRow($questionary,'lastname',array('size' => 60,'maxlength' => 128)); ?>
    
        <?php echo $form->textFieldRow($questionary,'firstname',array('size' => 60,'maxlength' => 128)); ?>
    
        <?php echo $form->textFieldRow($questionary,'middlename',array('size' => 60,'maxlength' => 128)); ?>
    
        <?php 
        // дата рождения
        echo $form->datepickerRow(
            $questionary,
            'formattedBirthDate', array(
                'options' => array(
                    'language'  => 'ru',
                    'format'    => 'dd.mm.yyyy',
                    'startView' => 'decade',
                    'weekStart' => 1,
                    'startDate' => '-75y',
                    'endDate'   => '-1y',
                    'autoclose' => true,
                ),
                'hint'    => 'Нажмите на название месяца или на год, чтобы изменить его',
                'prepend' => '<i class="icon-calendar"></i>'
            )
        );
        ?>
        
        <label><?php echo QuestionaryModule::t('playage_label'); ?></label>
        <div class="form-inline qform_subsection">
            <?php echo $form->textFieldRow($questionary,'playagemin', array('maxlength'=>3, 'style'=>'width:30px;')); ?>
            <?php echo $form->textFieldRow($questionary,'playagemax', array('maxlength'=>3, 'style'=>'width:30px;')); ?>
        </div>
    
        <?php echo $form->dropDownListRow($questionary, 'gender', $questionary->getFieldVariants('gender')); ?>
    
        <?php echo $form->textFieldRow($questionary, 'height', array('size' => 3, 'maxlength' => 6)); ?>
    
        <?php echo $form->textFieldRow($questionary,'weight', array('size' => 3, 'maxlength' => 6)); ?>
        
        <?php echo $form->dropDownListRow($questionary, 'wearsize', $questionary->getFieldVariants('wearsize')); ?>
    
        <?php echo $form->dropDownListRow($questionary, 'shoessize', $questionary->getFieldVariants('shoessize')); ?>
    
        <?php echo $form->labelEx($questionary,'cityid'); ?>
        <?php
            $cityOptions = array(
                'sourceUrl'=>Yii::app()->createUrl('questionary/questionary/ajax?type=city&parenttype=country&parentid=RU'),
                );
            $countrySelector->cityActiveField('cityid', $questionary, $cityOptions);
        ?>
        <?php echo $form->error($questionary,'cityid'); ?>
        <hr>
	</fieldset>

	<fieldset id="contact_information_part">
	    <legend id="contact_information_part_label">
	        <a class="btn btn-large btn-warning">
	        <i class="icon-chevron-down"></i>&nbsp;<?php echo QuestionaryModule::t('contact_information'); ?></a>
	    </legend>
        <?php echo $form->textFieldRow($questionary, 'mobilephone', array('size'=>32,'maxlength'=>32)); ?>
        <?php echo $form->textFieldRow($questionary, 'homephone', array('size'=>32,'maxlength'=>32)); ?>
        <?php echo $form->textFieldRow($questionary, 'addphone', array('size'=>32,'maxlength'=>32)); ?>
        <?php echo $form->textFieldRow($questionary, 'vkprofile', array('size'=>60,'maxlength'=>255)); ?>
        <?php echo $form->textFieldRow($questionary, 'fbprofile', array('size'=>60,'maxlength'=>255)); ?>
        <?php echo $form->textFieldRow($questionary, 'okprofile', array('size'=>60,'maxlength'=>255)); ?>
        <hr>
	</fieldset>

	<fieldset id="looks_part">
	    <legend id="looks_part_label">
	        <a class="btn btn-large btn-warning">
	        <i class="icon-chevron-down"></i>&nbsp;<?php echo QuestionaryModule::t('looks'); ?></a>
        </legend>

        <?php echo $form->labelEx($questionary,'photos'); ?>
        <?php
        // Рекомендации по добавлению фотографий
        $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
                array('field' => 'photos'));
        
        if ( $questionary->galleryBehavior->getGallery() === null )
        {
            echo '<div class="alert">Сохраните анкету перед загрузкой фотографий</div>';
        }else
        {
            $this->widget('GalleryManager', array(
                 'gallery' => $questionary->galleryBehavior->getGallery(),
                 'controllerRoute' => '/questionary/gallery'
            ));
        }
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
        
		<?php // тип внешности
		    echo $form->dropDownListRow($questionary,'looktype', $questionary->getFieldVariants('looktype'));
		?>
		
		<?php // страна рождения
		    echo $form->labelEx($questionary,'nativecountryid'); 
		    echo $countrySelector->countryActiveField('nativecountryid', $questionary); 
		    echo $form->error($questionary,'nativecountryid'); 
		?>

		<?php echo $form->dropDownListRow($questionary,'haircolor', $questionary->getFieldVariants('haircolor')); ?>
		<?php echo $form->dropDownListRow($questionary,'hairlength', $questionary->getFieldVariants('hairlength')); ?>
		<?php echo $form->dropDownListRow($questionary,'eyecolor', $questionary->getFieldVariants('eyecolor')); ?>
		<?php echo $form->dropDownListRow($questionary,'physiquetype', $questionary->getFieldVariants('physiquetype')); ?>
		
        <div>
        <fieldset id="addchars" class="qform_subsection">
            <legend id="addchars_label" class="qform_subsection_label">
                <?php echo QuestionaryModule::t('addchar_label'); ?>
            </legend>
            <?php 
                $this->widget(
                'application.modules.questionary.extensions.QEditAddChars.QEditAddChars',
                     array(
                        'data'           => $questionary->getFieldVariants('addchar', false),
                        'SelectedValues' => $questionary->addchars,
                     ));
            ?>
            <?php echo $form->error($questionary,'addchar'); ?>
        </fieldset>
        </div>

        <label><?php echo QuestionaryModule::t('parameters'); ?></label>
        <div class="form-inline qform_subsection">
            <?php echo $form->textFieldRow($questionary,'chestsize',array('maxlength'=>6, 'style'=>'width:50px;')); ?>
            <?php echo $form->textFieldRow($questionary,'waistsize',array('maxlength'=>6, 'style'=>'width:50px;')); ?>
            <?php echo $form->textFieldRow($questionary,'hipsize',array('maxlength'=>6, 'style'=>'width:50px;')); ?>
        </div>

    <div>
        <div id="titsize">
            <?php echo $form->dropDownListRow($questionary, 'titsize', $questionary->getFieldVariants('titsize')); ?>
            <?php echo $form->error($questionary,'titsize'); ?>
        </div>
    </div>

    <?php 
    // татуировки
    $this->widget('ext.EToggleBox.EToggleBox', array(
        'model'     => $questionary,
        'attribute' => 'hastatoo',
        'options'   => $toggleBoxJsOptions,
    ));
    ?>
    <hr>
	</fieldset>

	<fieldset id="experience_jobs_and_skills_part">
        <legend id="experience_jobs_and_skills_part_label">
            <a class="btn btn-large btn-warning">
                <i class="icon-chevron-down"></i>&nbsp;<?php echo QuestionaryModule::t('experience_jobs_and_skills'); ?>
            </a>
        </legend>

        <?php // Профессиональный актер
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
            echo $form->errorSummary( $validatedActorUniversities);
            // пояснение для списка учебных заведений
            $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
                array('field' => 'actoruniversity'));
            // список театральных ВУЗов
            $this->widget('questionary.extensions.widgets.QEditActorUniversities.QEditActorUniversities', array(
                    'questionary' => $questionary,
                )
            );
            ?>
        </fieldset>
    </div>
    
    <div>
        <div id="amateuractor">
            <?php // непрофессиональный актер
            $this->widget('ext.EToggleBox.EToggleBox', array(
              'model'     => $questionary,
              'attribute' => 'isamateuractor',
              'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
                      'after_on'  => 'js:function () {$("#films_part").fadeIn(200);}',
                      'after_off' => 'js:function () {$("#films_part").fadeOut(200);}'
                     )
                ),
            ));
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
                    $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
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
              'after_off' => 'js:function () {$("#actortheatres").fadeOut(200);}'))
        ));
    ?>
    
    <div>
        <fieldset id="actortheatres" class="qform_subsection">
            <legend class="qform_subsection_label"><?php echo QuestionaryModule::t('theatres'); ?></legend>
            <?php
            echo $form->errorSummary( $validatedActorUniversities);
            // пояснение для списка театров
            $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
                array('field' => 'actortheatres')
            );
            // список театров
            $actorTheatreFormConfig = $actorTheatre->formConfig();
            $this->widget('ext.multimodelform.MultiModelForm',
                array(
                   'addItemText'   => Yii::t('coreMessages','add'),
                   'removeText'    => Yii::t('coreMessages','delete'),
                   'removeConfirm' => QuestionaryModule::t('multimodel_remove_confirm'),
                   'id'            => 'id_actortheatre', //the unique widget id
                   'formConfig'    => $actorTheatreFormConfig, //the form configuration array
                   'model'         => $actorTheatre, //instance of the form model
    
                   'validatedItems' => $validatedActorTheatres,
    
                   // ранее сохраненные театры
                   'data' => $questionary->theatres,
                    
                    // JS для корректного копирования элементов combobox
                   'jsAfterNewId' => 
                        MultiModelForm::afterNewIdComboBox($actorTheatreFormConfig['elements']['theatreid'], 
                            'theatreid', 'name'), 
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
    $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
        array('field' => 'isstatist'));
    ?>
    
    <?php 
    // Актер массовых сцен
    $this->widget('ext.EToggleBox.EToggleBox', array(
        'model'     => $questionary,
        'attribute' => 'ismassactor',
        'options'   => $toggleBoxJsOptions));
    // пояснение для актера массовых сцен
    $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
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
    $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
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
    
    $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
        array('field' => 'istvshowmen'));
    ?>
	
    <div>
	<fieldset id="tvshows" class="qform_subsection">
    <?php
    // опыт работы телеведущим
    echo $form->errorSummary($validatedTvShows);
    $this->widget('ext.multimodelform.MultiModelForm',array(
               'addItemText'   => QuestionaryModule::t('tvshowmen_add_tvshow'),
               'removeText'    => Yii::t('coreMessages','delete'),
               'removeConfirm' => QuestionaryModule::t('multimodel_remove_confirm'),
               'id'            => 'id_tvshow', //the unique widget id
               'formConfig'    => $tvShow->formConfig(), //the form configuration array
               'model'         => $tvShow, //instance of the form model
               
               'validatedItems' => $validatedTvShows,

               // Ранее добавленные шоу телеведущего
               'data' => $questionary->tvshows,
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
    
    $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
        array('field' => 'isparodist'));
    ?>
	
    <div>
	<fieldset id="parodist" class="qform_subsection">
    	<?php 
    	$this->widget(
    	'application.modules.questionary.extensions.QEditParodistList.QEditParodistList',
        	 array(
        	    'SelectedValues' => $questionary->parodistlist,
        	    'textFieldLabel' => QuestionaryModule::t('parodist_images'),
        	    'hideSelect'     => 'asmSelect1',
        	 ));
    	?>
    	<?php echo $form->error($questionary,'parodist'); ?>
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
        
    $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
        array('field' => 'istwin'));
    ?>
	
    <div>
	<fieldset id="twin" class="qform_subsection">
    	<?php 
    	// список образов двойника
    	$this->widget(
    	'application.modules.questionary.extensions.QEditTwinList.QEditTwinList',
        	 array(
        	    'SelectedValues' => $questionary->twinlist,
        	    'textFieldLabel' => QuestionaryModule::t('twin_images'),
        	    'hideSelect'     => 'asmSelect2',
        	 ));
    	?>
    	<?php echo $form->error($questionary,'twin'); ?>
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
    $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
            array('field' => 'ismodel'));
    ?>
	
    <div>
	<fieldset id="modelschools" class="qform_subsection">
        <?php
        // Модельные школы
        $this->widget('questionary.extensions.widgets.QEditModelSchools.QEditModelSchools', array(
            'questionary' => $questionary,
            )
        );
        ?>
	</fieldset>
    </div>
    
    <div>
    <fieldset id="modeljobs" class="qform_subsection">
        <?php
        // опыт работы моделью
        $this->widget('questionary.extensions.widgets.QEditModelJobs.QEditModelJobs', array(
            'questionary' => $questionary,
            )
        );
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
 
    $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
        array('field' => 'isphotomodel')
    );
    ?>

    <div>
    <fieldset id="photomodeljobs" class="qform_subsection">
        <?php
        // опыт работы фотомоделью
        echo $form->errorSummary($validatedPhotoModelJobs);
        $this->widget('ext.multimodelform.MultiModelForm', 
            array(
               'addItemText'   => Yii::t('coreMessages','add'),
               'removeText'    => Yii::t('coreMessages','delete'),
               'removeConfirm' => QuestionaryModule::t('multimodel_remove_confirm'),
               'id'            => 'id_photomodeljob', //the unique widget id
               'formConfig'    => $photoModelJob->formConfig(), //the form configuration array
               'model'         => $photoModelJob, //instance of the form model
               
               'validatedItems' => $validatedPhotoModelJobs,

               // сохраненная ранее информация о работе фотомоделью
               'data' => $questionary->photomodeljobs,
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
        
        $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
                array('field' => 'ispromomodel'));
    ?>
    
    <div>
    <fieldset id="promomodeljobs" class="qform_subsection">
        <?php
        // опыт работы промо-моделью
        echo $form->errorSummary($validatedPromoModelJobs);
        $this->widget('ext.multimodelform.MultiModelForm', 
            array(
               'addItemText'   => Yii::t('coreMessages','add'),
               'removeText'    => Yii::t('coreMessages','delete'),
               'removeConfirm' => QuestionaryModule::t('multimodel_remove_confirm'),
               'id'            => 'id_promomodeljob', //the unique widget id
               'formConfig'    => $promoModelJob->formConfig(), //the form configuration array
               'model'         => $promoModelJob, //instance of the form model
               
               'validatedItems' => $validatedPromoModelJobs,

               // сохраненные ранее мероприятия промо-модели
               'data' => $questionary->promomodeljobs,
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
        
        $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
                array('field' => 'isdancer'));
    ?>

    <div>
    <fieldset id="dancetypes" class="qform_subsection">
    <legend class="qform_subsection_label"><?php echo QuestionaryModule::t('dancetypes_label'); ?></legend>
        <?php
        // список стандартных стилей танца
        echo $form->errorSummary($validatedDanceTypes);
        $danceTypeFormConfig = $danceType->formConfig();
        $this->widget('ext.multimodelform.MultiModelForm',array(
            'addItemText'   => Yii::t('coreMessages','add'),
            'removeText'    => Yii::t('coreMessages','delete'),
            'removeConfirm' => QuestionaryModule::t('multimodel_remove_confirm'),
            'id'            => 'id_dancetype', //the unique widget id
            'formConfig'    => $danceType->formConfig(), //the form configuration array
            'model'         => $danceType, //instance of the form model
            //if submitted not empty from the controller,
            //the form will be rendered with validation errors
            'validatedItems' => $validatedDanceTypes,
            // ранее сохраненные стили танца
            'data' => $questionary->dancetypes,
            // JS для корректного копирования элементов combobox
            'jsAfterNewId' => 
                MultiModelForm::afterNewIdComboBox($danceTypeFormConfig['elements']['dancetype'], 
                    'dancetype', 'name'),
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
	    echo $form->labelEx($questionary,'type');
    	$this->widget(
    	'application.modules.questionary.extensions.QEditVocalTypes.QEditVocalTypes',
        	 array(
        	    'data'           => $questionary->getFieldVariants('vocaltype', false),
        	    'SelectedValues' => $questionary->vocaltypes,
        	 ));
    	echo $form->error($questionary,'vocaltype');
    	?>
	</fieldset>

    <fieldset id="voicetimbres" class="qform_subsection">
	    <?php
	    // тембр голоса 
	    echo $form->labelEx($questionary,'voicetimbre');
    	$this->widget(
    	'application.modules.questionary.extensions.QEditVoiceTimbres.QEditVoiceTimbres',
        	 array(
        	    'data'           => $questionary->getFieldVariants('voicetimbre', false),
        	    'SelectedValues' => $questionary->voicetimbres,
        	 )
        );
    	echo $form->error($questionary,'voicetimbre'); ?>
	</fieldset>

    <div id="singlevel" class="qform_subsection">
        <?php echo $form->dropDownListRow($questionary,'singlevel', $questionary->getFieldVariants('level')); ?>
        <?php echo $form->error($questionary,'singlevel'); ?>
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
        // список стандартных музыкальных инструментов
        echo $form->errorSummary($validatedInstruments);
        $instrumentFormConfig = $instrument->formConfig();
        $this->widget('ext.multimodelform.MultiModelForm',array(
               'addItemText'   => Yii::t('coreMessages','add'),
               'removeText'    => Yii::t('coreMessages','delete'),
               'removeConfirm' => QuestionaryModule::t('multimodel_remove_confirm'),
               'id'            => 'id_instrument', //the unique widget id
               'formConfig'    => $instrumentFormConfig, //the form configuration array
               'model'         => $instrument, //instance of the form model
               //if submitted not empty from the controller,
               //the form will be rendered with validation errors
               'validatedItems' => $validatedInstruments,

               // ранее сохраненные музыкальные инструменты
               'data' => $questionary->instruments,

               // JS для корректного копирования элементов combobox
               'jsAfterNewId' => 
                    MultiModelForm::afterNewIdComboBox($instrumentFormConfig['elements']['instrument'], 
                        'instrument', 'name'),
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
        // сообщения об ошибках при заполнении списка ВУЗов
        echo $form->errorSummary( $validatedMusicUniversities);
        $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
            array('field' => 'musicuniversity'));
        
        // список музыкальных ВУЗов
        // Отображается если указан пункт "вокал" или "музыкант"
        $musicUniversityFormConfig = $musicUniversity->formConfig();
        $this->widget('ext.multimodelform.MultiModelForm',array(
               'addItemText'   => Yii::t('coreMessages','add'),
               'removeText'    => Yii::t('coreMessages','delete'),
               'removeConfirm' => QuestionaryModule::t('multimodel_remove_confirm'),
               'id'            => 'id_musicuniversity', //the unique widget id
               'formConfig'    => $musicUniversityFormConfig, //the form configuration array
               'model'         => $musicUniversity, //instance of the form model

               //if submitted not empty from the controller,
               //the form will be rendered with validation errors
               'validatedItems' =>  $validatedMusicUniversities,

               // ранее сохраненные музыкальные ВУЗы
               'data' => $questionary->musicuniversities,
               
               // JS для корректного копирования элементов combobox
               'jsAfterNewId' => 
                    MultiModelForm::afterNewIdComboBox($musicUniversityFormConfig['elements']['universityid'], 
                            'universityid', 'name'),
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
    $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
        array('field' => 'issportsman'));
    ?>
	
    <div>
	<fieldset id="sporttypes" class="qform_subsection">
    	<?php 
    	// виды спорта
    	$this->widget(
    	'application.modules.questionary.extensions.QEditSportTypes.QEditSportTypes', array(
        	    'data'           => $questionary->getFieldVariants('sporttype', false),
        	    'SelectedValues' => $questionary->sporttypes,
        	    'textFieldLabel' => 'Другие виды спорта:',
        	 )
        );
    	echo $form->error($questionary,'sporttype');
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
    $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
        array('field' => 'isextremal'));
    ?>
	
    <div>
	<fieldset id="extremaltypes" class="qform_subsection">
    	<?php 
    	// экстремальные виды спорта
    	$this->widget(
    	   'application.modules.questionary.extensions.QEditExtremalTypes.QEditExtremalTypes', array(
        	    'data'           => $questionary->getFieldVariants('extremaltype', false),
        	    'SelectedValues' => $questionary->extremaltypes,
        	    'textFieldLabel' => 'Другие виды спорта:',
        	 )
        );
    	echo $form->error($questionary, 'extremaltype');
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
    $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
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
    $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
        array('field' => 'hasskills'));
    ?>
	
    <div>
	<fieldset id="skills" class="qform_subsection">
    	<?php 
    	// умения и навыки
    	$this->widget(
    	   'application.modules.questionary.extensions.QEditSkills.QEditSkills', array(
        	    'data'           => $questionary->getFieldVariants('skill', false),
        	    'SelectedValues' => $questionary->skills,
        	    'textFieldLabel' => 'Другие умения:',
        	 )
        );
    	echo $form->error($questionary,'skill');
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
    $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
        array('field' => 'hastricks'));
    ?>
    
	<div>
	<fieldset id="tricks" class="qform_subsection">
    	<?php 
    	// список трюков для каскадера
    	$this->widget(
    	'application.modules.questionary.extensions.QEditTricks.QEditTricks',
        	 array(
        	    'data'           => $questionary->getFieldVariants('trick', false),
        	    'SelectedValues' => $questionary->tricks,
        	    'textFieldLabel' => 'Выполняемые трюки:',
        	    'hideSelect'     => 'asmSelect8',
        	 )
        );
    	echo $form->error($questionary, 'trick');
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
           // сообщения об ошибках при заполнении списка званий и наград
           echo $form->errorSummary($validatedAwards);
           // список званий, призов и наград
           $this->widget('ext.multimodelform.MultiModelForm',array(
                  'addItemText'   => Yii::t('coreMessages','add'),
                  'removeText'    => Yii::t('coreMessages','delete'),
                  'removeConfirm' => QuestionaryModule::t('multimodel_remove_confirm'),
                  'id'            => 'id_award', //the unique widget id
                  'formConfig'    => $award->formConfig(), //the form configuration array
                  'model'         => $award, //instance of the form model
                  //if submitted not empty from the controller,
                  //the form will be rendered with validation errors
                  'validatedItems' => $validatedAwards,

                  // ранее сохраненные призы и награды
                  'data' => $questionary->awards,
             ));
           ?>
       </fieldset>
    </div>
    <hr>
	</fieldset>

	<fieldset id="passportdata_part">
	    <legend id="passportdata_part_label">
	        <a class="btn btn-large btn-warning">
	           <i class="icon-chevron-down"></i>&nbsp;<?php echo QuestionaryModule::t('passport_data'); ?>
           </a>
	    </legend>
    	<?php
	    $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription',
	        array('field' => 'passportdata'));
    	?>
		<?php echo $form->textFieldRow($questionary,'passportserial',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->textFieldRow($questionary,'passportnum',array('size'=>10,'maxlength'=>10)); ?>
	
		<?php 
		// дата выдачи загранпаспорта
		echo $form->labelEx($questionary,'passportdate');
		$this->widget('ext.ActiveDateSelect',
		array(
		    'model'         => $questionary,
		    'attribute'     => 'passportdate',
		    'reverse_years' => false,
		    'field_order'   => 'DMY',
		    'start_year'    => date("Y", time() - 40 * 365 * 24 * 3600),
		    'end_year'      => date("Y", time()),
		));
		echo $form->error($questionary,'passportdate');
		?>
		<?php echo $form->textFieldRow($questionary, 'passportorg', array('size' => 60, 'maxlength' => 255)); ?>

        <?php echo $form->labelEx($questionary,'countryid'); ?>
        <?php echo $countrySelector->countryActiveField('countryid', $questionary); ?>
        <?php echo $form->error($questionary,'countryid'); ?>

        <?php 
		// Имеется ли медицинская страховка
        $this->widget('ext.EToggleBox.EToggleBox', array(
            'model'     => $questionary,
            'attribute' => 'hasinshurancecard',
            'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
                  'after_on'  => 'js:function () {$("#inshurancecardnum").fadeIn(200);}',
                  'after_off' => 'js:function () {$("#inshurancecardnum").fadeOut(200);}'))
             ));
        // пояснение про медицинскую страховку
        $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
            array('field' => 'hasinshurancecard'));
        ?>

        <div>
            <div id="inshurancecardnum">
                <?php
                // номер медицинской страховки 
                echo $form->textFieldRow($questionary, 'inshurancecardnum', array('size' => 60,'maxlength' => 128));
                ?>
            </div>
        </div>
        
        <?php echo $form->textFieldRow($questionary, 'inn', array('size' => 32,'maxlength' => 32)); ?>
	
	<h4><?php echo QuestionaryModule::t('address'); ?></h4>
        
		<?php echo $form->textFieldRow($address, 'postalcode', array('size' => 10, 'maxlength' => 10)); ?>

		<?php echo $form->labelEx($address,'countryid'); ?>
		<?php echo $countrySelector->countryActiveField('countryid', $address); ?>
		<?php echo $form->error($address,'countryid'); ?>

		<?php echo $form->labelEx($address,'cityid'); ?>
		<?php 
    		$cityOptions = array(
    		    'sourceUrl'=>Yii::app()->createUrl('questionary/questionary/ajax?type=city&parenttype=country&parentid=RU'),
    		);
    		$countrySelector->cityActiveField('cityid', $address, $cityOptions);
		?>
		<?php echo $form->error($address,'cityid'); ?>

		<?php echo $form->textFieldRow($address,'streetname',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->textFieldRow($address,'number',array('size'=>3,'maxlength'=>16)); ?>
		<?php echo $form->textFieldRow($address,'housing',array('size'=>3,'maxlength'=>16)); ?>
		<?php echo $form->textFieldRow($address,'apartment',array('size'=>3,'maxlength'=>16)); ?>
		<hr>
	</fieldset><!-- Конец полей паспортных данных -->

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
            $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
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
            $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
                array('field' => 'istoplessrecording'));
            ?>
        
            <?php
            // Благотворительные акции
            $this->widget('ext.EToggleBox.EToggleBox', array(
                'model'     => $recordingConditions,
                'attribute' => 'isfreerecording',
                'options'   => $toggleBoxJsOptions));
            // Благотворительные акции (пояснение)
            $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription', 
                array('field' => 'isfreerecording'));
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
                echo $form->labelEx($recordingConditions,'passportexpires');
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
                echo $form->error($recordingConditions,'passportexpires');
                ?>
            </div>
        </div>
        
        <?php 
        // размер оплаты для участия в съемках 
        echo $form->textFieldRow($recordingConditions, 'salary', array('size'=>10,'maxlength'=>10));
        // размер оплаты (пояснение)
        $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription',
            array('field' => 'salary'));
        ?>
        
        <?php 
        // дополнительные условия для участия в съемках 
        echo $form->textAreaRow($recordingConditions,'custom');
        ?>
        <hr>
    </fieldset>
    
    <?php
    // Согласие с политикой сайта
    // @todo переписать проверку прав
    // @todo вставить ссылку или текстовый блок с условиями соглашения
    if ( ! $user->policyagreed AND ! Yii::app()->user->checkAccess('Admin') )
    {// не показывается админам и тем, кто уже согласился с условиями
        /*$this->widget('ext.EToggleBox.EToggleBox', array(
            'model'     => $user,
            'attribute' => 'policyagreed',
            'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
                'after_off'  => 'js:function () {
                        $("#save_questionary").addClass("disabled");
                        $("#save_questionary").attr("disabled", "disabled");
                    }',
                'after_on' => 'js:function () {
                        $("#save_questionary").removeClass("disabled");
                        $("#save_questionary").removeAttr("disabled");
                    }'
            ))
        ));*/
    }
    ?>
    
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
        $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription',
            array('field' => 'status'));
        
        // Комментарий модератора или администратора (только для админов)
        // Используется при отправке анкеты на доработку
        // Присылается пользователю в письме
        echo $form->textAreaRow($questionary, 'admincomment');
        $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription',
            array('field' => 'admincomment'));
        
        // Комментарий к анкете - дополнительная информация об участнике
        // можно писать все что угодна, участник это поле не видит никогда
        // Выделяем его очень ярко, чтобы ни в коем случае не ошибиться
        ?>
        <div class="ec-round-the-corner" style="background-color:#aaa;padding:20px;">
        <br><br>
        <?php
        echo $form->labelEx($questionary,'privatecomment');
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
        $this->widget('application.modules.questionary.extensions.widgets.QFieldDescription.QFieldDescription',
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
// место для отладки

?>