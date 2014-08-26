<?php
/**
 * Полная разметка одной заявки
 */
/* @var $this MpMemberData */

$collapseExtraStyle    = '';
$collapseSectionsStyle = '';
if ( ! $this->collapseExtra )
{
    $collapseExtraStyle = ' in';
}
if ( ! $this->collapseSections )
{
    $collapseSectionsStyle = ' in';
}
$collapseContainerOptions = array(
    'id' => 'collapse_accordion_wrapper_'.$this->id,
);
?>
<div id="<?= $this->wrapperId; ?>">
    <div class="row-fluid">
        <?php
        // сначала выводим свернутый блок с краткой информацией
        $this->render('_summary');
        
        echo 'Дата создания заявки: '.date('Y-m-d H:i', $this->member->timecreated);
        ?>
    </div>
    <?php
    // виджет для сворачивающихся блоков 
    //$collapse = $this->beginWidget('bootstrap.widgets.TbCollapse', $collapseContainerOptions);
    ?>
    <div class="accordion-group">
        <div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse" href="#collapse_extra_<?= $this->id; ?>">
                <b>Анкета участника</b>
            </a>
        </div>
        <div id="collapse_extra_<?= $this->id; ?>" class="accordion-body collapse <?= $collapseExtraStyle; ?>">
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
            <a class="accordion-toggle" data-toggle="collapse" href="#collapse_sections_<?= $this->id; ?>">
                <b>В каких разделах эта заявка?</b>
            </a>
        </div>
        <div id="collapse_sections_<?= $this->id; ?>" class="accordion-body collapse <?= $collapseExtraStyle; ?>">
            <div class="accordion-inner">
            <?php
            // блок со списком категорий и маркерами
            $this->render('_sections');
            ?>
            </div>
        </div>
    </div>
    <?php
    // конец виджета со сворачивающимися полями 
    //$this->endWidget();
    ?>
</div>