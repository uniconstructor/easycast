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
        <?php
            if ( Yii::app()->user->isGuest )
            {
                $this->render('newUser', array('newUser' => $newUser));
            }else
            {
                $this->render('myPage', array('newUser' => $newUser));
            }
        ?>
        </td>
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