<?php
/**
 * Один отзыв заказчика
 */
/* @var $this SaleController */
?>
<div class="single_news" style="width:1040px;">
    <img src="<?= $review['image']; ?>" alt="<?= $review['name']; ?>" title="<?= $review['name']; ?>"
        style="max-height:250px;max-width:250px;" class="ec-shadow-3px ec-round-the-corner" />
    <div class="news_content">
        <div class="news_profile">
            <span class="news_name" style="text-shadow: 0 1px 2px #E8F9FF; color: #1E3D52;"><?= $review['name']; ?></span>
            <br>
            <span><i style="text-shadow: 0 1px 2px #E8F9FF; color: #1E3D52;"><?= $review['position']; ?>
                <?php 
                if ( $review['company'] )
                {
                    echo ', '.$review['company'];
                }
                ?>
            </i></span>
            <div class="contact_info">
            <p style="text-indent:20px;line-height:1.2em;font-size:20px;margin-top:20px;text-shadow: 0 1px 2px #E8F9FF; color:#1E3D52;text-align:justify;">
            <?= $review['text']; ?>
            </p>
            </div>
        </div>
    </div>
</div>