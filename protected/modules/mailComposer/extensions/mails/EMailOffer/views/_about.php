<?php
/**
 * 4 колонки с текстом о компании в начале письма
 */
/* @var $this EMailOffer */
?>
<table class="w640" width="640" border="0" cellpadding="0" cellspacing="0">
    <tbody>
    <tr>
        <td width="30">&nbsp;</td>
        <td width="580">
            <p style="text-align:justify;font-weight:200;text-shadow: 0 1px 2px #E8F9FF;">
            <?= $greeting ?>
            </p>
        </td>
        <td width="30">&nbsp;</td>
    </tr>
    </tbody>
</table>
<table class="w640" width="640" border="0" cellpadding="0" cellspacing="0" style="text-align:center;">
    <tbody>
    <tr>
        <td width="160"><img src="<?= $this->getImageUrl('/images/offer/icon-thumbs-up.png'); ?>" style="height:64px;width:64px;display:inline;"></td>
        <td width="160"><img src="<?= $this->getImageUrl('/images/offer/icon-star3.png'); ?>" style="height:64px;width:64px;display:inline;"></td>
        <td width="160"><img src="<?= $this->getImageUrl('/images/offer/icon-film.png'); ?>" style="height:64px;width:64px;display:inline;"></td>
        <td width="160"><img src="<?= $this->getImageUrl('/images/offer/icon-mobile2.png'); ?>" style="height:64px;width:64px;display:inline;"></td>
    </tr>
    <tr>
        <td>10 лет опыта</td>
        <td>Подбираем любых артистов</td>
        <td>Для любых съемок</td>
        <td>Используем современные технологии</td>
    </tr>
    <tr style="font-weight:200;text-shadow: 0 1px 2px #E8F9FF;vertical-align:top;font-size:12px;">
        <td>Кастинговое агентство easyCast успешно работает с 2004 года.</td>
        <td>Актеры всех категорий, модели, артисты циркового жанра, всевозможные типажи, групповка и массовка.</td>
        <td>Обслуживаем фильмы, рекламу, сериалы, телепроекты, клипы и другие съемки.</td>
        <td>Мы создали мощнейшие инструменты кастинга и автоматизировали 80% своей работы.</td>
    </tr>
    </tbody>
</table>