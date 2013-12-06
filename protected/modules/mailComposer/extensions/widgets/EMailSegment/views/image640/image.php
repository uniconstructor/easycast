<?php
/**
 * Изображение в полную ширину письма (640 пикселей)
 */
/* @var $this EMailSegment */
?>
<table class="w<?= $this->blockWidth; ?>" width="<?= $this->blockWidth; ?>" border="0" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
            <td class="w<?= $this->blockWidth; ?>" width="<?= $this->blockWidth; ?>">
                <?php 
                // Выводим изображение
                if ( $this->imageTarget )
                {// со ссылкой
                    $this->render('misc/_clickableImage');
                }else
                {// без ссылки
                    $this->render('misc/_image', array(
                        'link'  => $this->imageLink,
                        'style' => $this->imageStyle,
                    ));
                }
                ?>
            </td>
        </tr>
    </tbody>
</table>