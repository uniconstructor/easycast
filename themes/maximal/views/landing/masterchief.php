<?php
/**
 * Разметка для страницы регистрации на проект "МастерШеф"
 */
// head-раздел и мета-теги
$this->beginContent('//layouts/main');
?>
<section>
    <img style="max-width: 100%;" alt="baner-master-chef" src="<?= Yii::app()->baseUrl; ?>/masterchief/size_3_baner-master-chef-3.jpg">
</section>
<div class="bodyBackgroundContainer">
    <section class="bgSection">
        <div class="container">
            <div class="row">
                <div class="span12">
                    <div class="widgetContainer">
                        <div class="imageContainer">
                            <div class="noLinkImage text-center">
                                
                            </div>
                        </div>
                    </div>
                    <div class="widgetContainer">
                        <div class="headingContainer heading-3 text-center">
                            <h3>Телеканал СТС объявляет кастинг участников на самое кулинарное шоу мира!</h3>
                        </div>
                    </div>
                    <div class="widgetContainer">
                        <div class="headingContainer heading-3 text-">
                            <h3 class="text-center">Как стать участником проекта?</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="bgSection">
        <div class="container">
            <div class="row">
                <div class="span12">
                    <div class="textContainer">
                        <p class="text-info" style="font-style: italic;">Будьте самим собой! Мы хотим узнать, какая вы личность. Не бойтесь показать себя! Будьте честным! Мы ищем
                            реальных людей, с реальными историями и реальными талантами. Докажите почему мы должны выбрать именно вас! Для
                            этого вам нужно:</p>
                    </div>
                    <div>
                        <div class="row">
                            <div class="span2">
                                <div class="widgetContainer">
                                    <div class="logoContainer text-center">
                                        <a href="http://easycast.ru/" title="video-icon"><img
                                            src="<?= Yii::app()->baseUrl; ?>/masterchief/size_3_video-icon.png" title="video-icon"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="span10">
                                <div class="widgetContainer">
                                    <div class="spaceContainer xs"></div>
                                </div>
                                <div class="widgetContainer">
                                    <div class="headingContainer heading-3 text-">
                                        <h3>Снять видео о процессе вашего кулинарного творчества</h3>
                                    </div>
                                </div>
                                <div class="widgetContainer">
                                    <div class="textContainer">
                                        <p>Попросите кого-то заснять как вы готовите какое-то изысканное блюдо, комментируя свое творчество.
                                            Расскажите о себе и о ваших целях участия в проекте. Отнеситесь к этой задаче творчески.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="row">
                            <div class="span2">
                                <div class="widgetContainer">
                                    <div class="logoContainer text-center">
                                        <a href="http://easycast.ru/" title="photo-icon"><img
                                            src="<?= Yii::app()->baseUrl; ?>/masterchief/size_3_mail-icon.png" title="photo-icon"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="span10">
                                <div class="widgetContainer">
                                    <div class="spaceContainer xs"></div>
                                </div>
                                <div class="widgetContainer">
                                    <div class="headingContainer heading-3 text-">
                                        <h3>Подать заявку на участие в кастинге</h3>
                                    </div>
                                </div>
                                <div class="widgetContainer">
                                    <div class="textContainer">
                                        <p>Подробно заполните анкету, приложив свое&nbsp;фото и загрузив&nbsp;видео, на котором
                                            заснят&nbsp;процесс приготовления вами вашего фирменного блюда.&nbsp;</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="row">
                            <div class="span2">
                                <div class="widgetContainer">
                                    <div class="logoContainer text-center">
                                        <a href="http://easycast.ru/" title="tv-icon"><img
                                            src="<?= Yii::app()->baseUrl; ?>/masterchief/size_3_tv-icon.png" title="tv-icon"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="span10">
                                <div class="widgetContainer">
                                    <div class="spaceContainer xs"></div>
                                </div>
                                <div class="widgetContainer">
                                    <div class="headingContainer heading-3 text-left">
                                        <h3>Стать участником проекта!</h3>
                                    </div>
                                </div>
                                <div class="widgetContainer">
                                    <div class="textContainer">
                                        <p>
                                            Если вы проявите усердие и ваша заявка окажется&nbsp;в числе лучших - мы свяжемся с вами для
                                            того, чтобы провести онлайн-интервью по Skype и сообщить всю необходимую&nbsp;информацию об
                                            участии в финальном этапе кастинга.<br>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    // основное содержимое страницы
                    echo $content;
                    ?>
                </div>
            </div>
        </div>
    </section>
    <div class="bodyBackgroundOverlay"></div>
</div>
<?php
// все скрипты, которые должны быть подключены внизу страницы CClientScript::POS_END
$this->endContent();