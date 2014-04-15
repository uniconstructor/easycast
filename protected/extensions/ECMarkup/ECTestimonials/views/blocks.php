<?php
/**
 * Отзывы от заказчиков - длинным списком
 */
/* @var $this ECTestimonials */

$baseImageUrl = Yii::app()->createAbsoluteUrl('/images/offer/reviews/').'/';
$imageOptions = array(
    'style' => 'max-height:250px;max-width:250px;',
    'class' => 'ec-shadow-3px ec-round-the-corner',
);
?>
<div class="row-fluid" style="padding-bottom:20px;">
    <div class="span3 text-center">
        <?= CHtml::image($baseImageUrl.'7.jpg', 'Тина Канделаки', $imageOptions); ?>
    </div>
    <div class="span9">
        <h3 class="profile-name">Тина Канделаки</h3>
        <p class="profile-description" style="font-weight:500;color:#497A89;">
            Продюсер, телеведущая, общественный деятель
        </p>
        <p class="profile-description">
            Я знаю easyCast очень давно, и являюсь свидетелем их карьеры. 
            Это очень приятно, так как они развивались на моих глазах и доросли до компании, 
            которой можно доверить под ключ организовать огромный процесс. Очень мало в наше время людей, 
            с навыками делать что-то эффективно и доводить дело до конца, взять и качественно выполнить 
            все поставленные задачи. Команда easyCast это умеет. Я это давно наблюдаю, давно это вижу и 
            более того, я с easyCast работаю.
        </p>
    </div>
</div>
<div class="row-fluid" style="padding-bottom:20px;">
    <div class="span3 text-center">
        <?= CHtml::image($baseImageUrl.'5.jpg', 'Антон Федотов', $imageOptions); ?>
    </div>
    <div class="span9">
        <h3 class="profile-name">Антон Федотов</h3>
        <p class="profile-description" style="font-weight:500;color:#497A89;">
            Режиссер-постановщик
        </p>
        <p class="profile-description">
            Всегда остаюсь довольным работой этой команды.
            Второй план в кино считаю крайне важным, а для реализации замысла нужны толковые люди, иногда -
            много людей, и порой добиться от них выполнения задачи, дело не простое. Поэтому берегите нервы,
            деньги и реализуйте самые смелые идеи в кино, а easyCast вам в этом поможет!
        </p>
    </div>
</div>
<div class="row-fluid" style="padding-bottom:20px;">
    <div class="span3 text-center">
        <?= CHtml::image($baseImageUrl.'8.jpg', 'Константин Маньковский', $imageOptions); ?>
    </div>
    <div class="span9">
        <h3 class="profile-name">Константин Маньковский</h3>
        <p class="profile-description" style="font-weight:500;color:#497A89;">
            Продюсер, ПЦ Среда
        </p>
        <p class="profile-description">
            Сотрудничал с easyCast на проекте &laquo;Большая разница&raquo;,
            а так же на многих других проектах. Самое главное - я
            всегда получал именно то, что хотел. Малейшие недочеты исправлялись с фантастической скоростью.
            На нашем рынке не много таких профессионалов! С радостью буду продолжать сотрудничество!
        </p>
    </div>
</div>
<div class="row-fluid" style="padding-bottom:20px;">
    <div class="span3 text-center">
        <?= CHtml::image($baseImageUrl.'13.jpg', 'Антон Калинкин', $imageOptions); ?>
    </div>
    <div class="span9">
        <h3 class="profile-name">Антон Калинкин</h3>
        <p class="profile-description" style="font-weight:500;color:#497A89;">
            Генеральный продюсер
        </p>
        <p class="profile-description">
            Команда easycast действительно удивляет не только качеством услуг, 
            но и уровнем организации. Кастинги, которые они проводят выше всяких похвал. 
            С первого проекта, на который мы пригласили ребят, я принял решение сотрудничать 
            исключительно с ними.
        </p>
    </div>
</div>
<div class="row-fluid" style="padding-bottom:20px;">
    <div class="span3 text-center">
        <?= CHtml::image($baseImageUrl.'14.jpg', 'Сергей Кальварский', $imageOptions); ?>
    </div>
    <div class="span9">
        <h3 class="profile-name">Сергей Кальварский</h3>
        <p class="profile-description" style="font-weight:500;color:#497A89;">
            Генеральный продюсер
        </p>
        <p class="profile-description">
            Знаю эту компанию более 7 лет. Они блестяще справились более, чем с десятком моих проектов. 
            Вижу как из года в год компания развивается и с каждым годом справляется все с более 
            сложными задачами. Браво!
        </p>
    </div>
</div>
<div class="row-fluid" style="padding-bottom:20px;">
    <div class="span3 text-center">
        <?= CHtml::image($baseImageUrl.'6.jpg', 'Dino Mc 47', $imageOptions); ?>
    </div>
    <div class="span9">
        <h3 class="profile-name">Dino Mc 47</h3>
        <p class="profile-description" style="font-weight:500;color:#497A89;">
            Российский рэп-исполнитель
        </p>
        <p class="profile-description">
            Кастинг-агентсво Easycast мне действительно очень сильно помогают воплащать 
            мои творческие задумки в реальность. Для моих клипов я часто подбираю нестандартых, 
            аутентичных персонажей, они быстро и качественно справляются со всеми поставленными задачами.
            Мои друзья-мои партнеры. Настоящие профессионалы в своем деле.
        </p>
    </div>
</div>
<div class="row-fluid" style="padding-bottom:20px;">
    <div class="span3 text-center">
        <?= CHtml::image($baseImageUrl.'2.jpg', 'Константин Афанасьев', $imageOptions); ?>
    </div>
    <div class="span9">
        <h3 class="profile-name">Константин Афанасьев</h3>
        <p class="profile-description" style="font-weight:500;color:#497A89;">
            Продюсер
        </p>
        <p class="profile-description">
            Я знаю эту команду не год и даже не два.
            Я сотрудничаю с ними более 7 лет. Структурность, система отчетности, сроки, качество оказываемых
            услуг в easyCast на высоком уровне. За годы нашей совместной работы, эти ребята не только ни разу
            не подвели меня, но и спасали в таких ситуациях, в которых казалось, что поможет только чудо.
        </p>
    </div>
</div>