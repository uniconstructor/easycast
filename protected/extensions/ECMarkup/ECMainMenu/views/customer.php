<?php
/**
 * Содержимое главного меню заказчика:
 * два ряда картинок с подписями
 */
?>
<div class="mainmenu-inner">
    <table class="table">
        <tr>
            <?php // Выводим пункты для первой строки меню 
            foreach ( $items[0] as $item )
            {
                $this->render('item', array('item' => $item));
            }
            ?>
        </tr>
        <tr>
            <?php // Выводим пункты для второй строки меню 
            if ( isset($items[1]) )
            {
                foreach ( $items[1] as $item )
                {
                    $this->render('item', array('item' => $item));
                }
            }
            ?>
        </tr>
    </table>
</div>