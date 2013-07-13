<?php
/**
 * Содержимое главного меню участника:
 * два ряда картинок с подписями
 */
?>
<div class="mainmenu-inner">
<table class="table">
    <tr>
        <td rowspan="2" class="mainmenu-new-user">
            <div class="main-menu-item">
                <div class="main-menu-item-image clearfix">
                    <a href="<?= $newUser->link; ?>" target="<?= $newUser->target; ?>"><?= $newUser->image; ?></a>

                    <div class="main-menu-item-label"><a href="<?= $newUser->link; ?>" target="<?= $newUser->target; ?>"><?= $newUser->label; ?></a></div>
                </div>
            </div>
        <?php
            
        ?>
        </td>
        <?php // Выводим пункты для первой строки меню 
            foreach ( $items[0] as $item )
            {
                $this->render('item', array('item'=>$item));
            }
        ?>
    </tr>
    <tr>
        <?php // Выводим пункты для второй строки меню 
            if ( isset($items[1]) )
            {
                foreach ( $items[1] as $item )
                {
                    $this->render('item', array('item'=>$item));
                }
            }
        ?>
    </tr>
</table>
</div>