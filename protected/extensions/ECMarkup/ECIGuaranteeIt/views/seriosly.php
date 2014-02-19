<?php
/**
 * Верстка страницы с текстом гарантий.
 * Серьезно.
 */
/* @var $this ECIguaranteeIt */

// начало всплывающего окна
$this->beginWidget('bootstrap.widgets.TbModal', array(
    'id'          => $this->modalId,
    'htmlOptions' => array(
        'style' => 'width:80%;left:30%;',
    ),
));
?>
<div class="modal-header">
    <a class="close white" data-dismiss="modal">&times;</a>
    <h3>Гарантии качества</h3>
</div>
<div class="modal-body" style="padding-left:10%;padding-right:10%;">
    <div class="row-fluid">
        <p>
        &laquo;Главный приоритет всех сотрудников easyCast &mdash; удивить заказчика уровнем профессионализма
        настолько, чтобы он принял решение &mdash; впредь работать только с нами&raquo;
        </p>
    </div>
    <div class="row-fluid">
        <div class="span6">
            <img style="max-width:500px;" src="<?= $this->assetUrl.'/IGuaranteeIt.jpg' ?>">
        </div>
        <div class="span6">
            <p>
            На сегодняшний день мы - нединственная компания в отрасли, развивающая програмную автоматизацию.
            Сегодня мы создаем &laquo;завтра&raquo; &mdash; надежную и понятную систему, которая решить все задачи
            по профессиональному поиску актеров, артистов всех жанров, различных типажей, моделей и артистов
            массовых сцен, происходящему в регионе Москвы и области.  
            </p>
        </div>
    </div>
    <div class="row-fluid">
        <p>
        Все наши самые смелые разработки созданы специально для того, чтобы повысить качество кастинговых услуг, 
        оказываемых в отрасли производства кино, телепроектов, сериалов, рекламы, клипов и флешмобов.
        </p>
    </div>
    <div class="row-fluid">
        <p>
        Мы не только понимаем всю степень ответственности в процессе оказания услуг, но всегда гарантируем
        заказчику выполнение наших обязательств на самом высоком уровне.
        </p>
    </div>
    <div class="row-fluid" style="vertical-align: bottom;">
        Управляющий партнер кастингового агентства &laquo;Изикэст&raquo;
        <span class="pull-right"><img src="<?= $this->assetUrl.'/signature.png' ?>">Николай Гришин</span>
    </div>
</div>
<div class="modal-footer">
    <?php
    // закрыть
    $this->widget('bootstrap.widgets.TbButton', array(
        'label'       => 'Закрыть',
        'htmlOptions' => array('data-dismiss' => 'modal'),
        'type'        => 'success',
    )); 
    ?>
</div>
<?php 
// конец всплывающего окна
$this->endWidget($this->modalId);
?>