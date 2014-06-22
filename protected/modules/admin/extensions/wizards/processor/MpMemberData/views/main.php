<?php
/**
 * Полная разметка одной заявки
 */
/* @var $this MpMemberData */
?>
<div id="<?= $this->wrapperId; ?>">
    <div class="row-fluid">
        <?php
        // сначала выводим свернутый блок с краткой информацией
        $this->render('_summary');
        ?>
    </div>
        <?php $collapse = $this->beginWidget('bootstrap.widgets.TbCollapse'); ?>
    <div class="accordion-group">
        <div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse_extra_<?= $this->id; ?>">
                <b>Анкета участника</b>
            </a>
        </div>
        <div id="collapse_extra_<?= $this->id; ?>" class="accordion-body collapse">
            <div class="accordion-inner">
            <?php
            // дополнительные поля заявки
            $this->render('_extra');
            ?>
            </div>
        </div>
    </div>
    <div class="accordion-group">
        <div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse_sections_<?= $this->id; ?>">
                <b>В каких разделах эта заявка?</b>
            </a>
        </div>
        <div id="collapse_sections_<?= $this->id; ?>" class="accordion-body collapse">
            <div class="accordion-inner">
            <?php
            // блок со списком категорий и маркерами
            $this->render('_sections');
            ?>
            </div>
        </div>
    </div>
    <?php $this->endWidget(); ?>
</div>