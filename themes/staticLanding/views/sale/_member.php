<?php
/**
 * Отображение одного члена команды
 */
/* @var $this SaleController */
/* @var $member array */
?>
<div class="single_news">
    <noindex>
    <img src="<?= $member['image']; ?>" alt="<?= $member['name']; ?>" title="<?= $member['name']; ?>"/>
    <div class="news_content">
        <div class="news_top">
            <p class="lp-slogan-small">"Я возьму ваши сложности на себя"</p>
        </div>
        <div class="news_profile">
            <span class="news_name"><?= $member['name']; ?></span>
            <span><i><?= $member['position']; ?></i></span>
            <div class="contact_info">
                <span><?= $member['phone']; ?></span>|<span><?= $member['email']; ?></span>|<span> +7 495 227-5-226</span>
            </div>
            <div class="facebook_profile">
                <img src="<?php echo Yii::app()->theme->baseUrl; ?>/images/facebook_icon.png"/>
                <span style="font-size:22px;"><?= $member['facebook']; ?></span>
            </div> 
        </div>
    </div>
    </noindex>
</div>