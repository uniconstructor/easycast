<?php
/**
 * Разметка для отображения одной анкеты в списке заявок на роль
 */
/* @var $this SmartMemberInfo */
/* @var $questionary Questionary */

// список полей отображаемых изначально
?>
<div class="col-sm-12">
    <div class="well well-sm">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="well well-light well-sm no-margin no-padding">
                    <div class="row">
                        <!--div class="col-sm-12">
                            <div class="carousel fade profile-carousel" id="myCarousel">
                                <div class="air air-bottom-right padding-10">
                                    <a class="btn txt-color-white bg-color-teal btn-sm" href="javascript:void(0);"><i class="fa fa-check"></i>
                                        Follow</a>&nbsp; <a class="btn txt-color-white bg-color-pinkDark btn-sm" href="javascript:void(0);"><i
                                        class="fa fa-link"></i> Connect</a>
                                </div>
                                <div class="air air-top-left padding-10">
                                    <h4 class="txt-color-white font-md">Jan 1, 2014</h4>
                                </div>
                                <ol class="carousel-indicators">
                                    <li class="active" data-slide-to="0" data-target="#myCarousel"></li>
                                    <li class="" data-slide-to="1" data-target="#myCarousel"></li>
                                    <li class="" data-slide-to="2" data-target="#myCarousel"></li>
                                </ol>
                                <div class="carousel-inner">
                                    <div class="item active">
                                        <img alt="" src="img/demo/s1.jpg">
                                    </div>
                                    <div class="item">
                                        <img alt="" src="img/demo/s2.jpg">
                                    </div>
                                    <div class="item">
                                        <img alt="" src="img/demo/m3.jpg">
                                    </div>
                                </div>
                            </div>
                        </div-->
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-3">
                                    <h4>Категории</h4>
                                    <?php
                                    // блок с галочками категорий
                                    $this->render('_sections', array(
                                        'projectMember' => $projectMember,
                                    ));
                                    ?>
                                </div>
                                <div class="col-sm-4">
                                    <?php
                                    // @todo убрать после отладки 
                                    echo CHtml::image('https://s3.amazonaws.com/img.easycast.ru/gallery/50256/78423catalog.jpg');
                                    ?>
                                    <?php
                                    // аватар пользователя
                                    // echo CHtml::image($questionary->getAvatarUrl('catalog'));
                                    ?>
                                    <h1>
                                        <?= $questionary->firstname; ?> 
                                        <span class="semi-bold"><?= $questionary->lastname; ?></span> <br> 
                                        <small><?= $questionary->age; ?>, 
                                        <?= $questionary->getCity(); ?>,<br> <?= $questionary->getRegionName(); ?></small>
                                    </h1>
                                    <ul class="list-inline friends-list">
                                        <?php 
                                        // миниатюры фото
                                        $photos = $questionary->getBootstrapPhotos('small');
                                        $limit  = 8;
                                        $count  = 0;
                                        foreach ( $photos as $photo )
                                        {
                                            $photo['link'] = 'https://s3.amazonaws.com/img.easycast.ru/gallery/50256/78424small.jpg';
                                            echo '<li>'.CHtml::image($photo['link'], '', array('min-width:50px;min-height:50px;')).'</li>';
                                            $count++;
                                            if ( $count > $limit )
                                            {
                                                break;
                                            }
                                        }
                                        ?>
										<li>
											<a href="javascript:void(0);">Все фотографии...</a>
										</li>
									</ul>
									<?php 
									// кнопки смены статуса заявки
									// кнопки для изменения статуса анкеты
									$this->widget('projects.extensions.MemberActions.MemberActions', array(
									    'member'             => $projectMember,
									    //'customerInvite'     => $this->customerInvite,
									    'forceDisplayStatus' => true,
									    'displayMode'        => 'row',
									));
									?>
                                </div>
                                <div class="col-sm-5">
                                    <!--h1>
                                        <small>Фото</small>
                                    </h1-->
                                    <?php 
                                    $items    = $questionary->getBootstrapPhotos('medium');
                                    $newItems = array();
                                    foreach ( $items as $item )
                                    {
                                        $item['link'] = 'https://s3.amazonaws.com/img.easycast.ru/gallery/50256/78423medium.jpg';
                                        $newItems[] = $item;
                                    }
                                    /*$this->widget('bootstrap.widgets.TbCarousel', array(
                                        'items' => $newItems,
                                    ));*/
                                    ?>
                                    <h1>
                                        <small>Видео</small>
                                    </h1>
                                    <?php
                                    // список загруженных видео
                                    $this->widget('ext.ECMarkup.ECUploadedVideo.ECUploadedVideo', array(
                                        'objectType' => 'questionary',
                                        'objectId'   => $projectMember->questionary->id,
                                        // @todo добавить параметр expires когда будет решена проблема с 
                                        //       подписанными ссылками на видео
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <hr>
                            <div class="padding-10">
                                <?php
                                // анкета участника
                                $this->render('_answersData');
                                ?>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->
                </div>
            </div>
        </div>
    </div>
</div>