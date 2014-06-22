<?php
/**
 * Верхняя полоска навигации с выбором статусов и маркеров
 * @todo отображать количество
 * @todo вычислять и отображать прогрессбар для неразобранных
 */
/* @var $this MemberProcessor */

?>
<div id="MpNavbar" class="navbar navbar-static">
    <div class="navbar-inner" style="padding-left:0;padding-right:0;">
        <div class="container" style="width:auto;">
            <div class="row-fluid" style="padding-bottom:3px;">
                <div class="span5 text-center">
                <?php 
                if ( $this->section )
                {
                    foreach ( $this->markers as $marker )
                    {
                        echo $this->getToggleButton($marker);
                    }
                }
                ?>
                </div>
                <div class="span2 text-center">
                    <h4><?= $this->sectionName; ?>
                    <b><?= $this->getMemberCount($this->sectionInstanceId); ?></b>
                </h4>
                </div>
                <div class="span5">
                <?php
                foreach ( $this->statuses as $status )
                {
                    echo $this->getToggleButton($status);
                }
                ?>
                </div>
            </div>
            <!--div class="row-fluid text-center">
                
            </div-->
        </div>
    </div>
</div>
