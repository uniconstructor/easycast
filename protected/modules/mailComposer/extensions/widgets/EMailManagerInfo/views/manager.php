<?php
/**
 * Виджет для отображения информации о менеджере (персонализация)
 * Выводит все контакты менеджера, его фотографию
 * Используется в коммерческом предложении и фотовызывном, а также в любых других письмах, которые
 * отправляются заказчикам
 */
/* @var $this ECMailManagerInfo */
?>
<!-- Персонализация -->
<tr bgcolor="#dbe4f1">
	<td height="180">
		<table border="0" cellpadding="0" cellspacing="0" align="left">
			<tbody>
				<tr>
					<td class="w20" width="20"></td>
					<td style="vertical-align:top" valign="top">
						<img src="<?= $managerPhoto; ?>" 
						  style="border:3px solid #C3C3C3;border-radius:55px;height:100px;width:100px;margin:16px 0px 10px 16px;">
					</td>
					<td class="w20" width="20"></td>
					<td>
						<div class="article-content" align="left" style="color:#6d6d6d;font-family:century gothic,Tahoma,sans-serif;">
                        <img style="padding-top:10px;" src="<?= $iTakeCareImage; ?>" width="100%">
                        <h3 style="font-size: 20px; font-weight: bold; color: #727272; margin: -3px 0px 6px 13px; ">
                            <?= $fullName; ?>
                            <span style="font-size:16px;font-weight:normal;font-style:italic;margin-left:10px;">
                            <?= $position; ?>
                            </span>
                        </h3>
                        <span style="display:block;margin:-3px 0px 6px 13px;font-size:14px;">
                            <?= $phone; ?> | <?= $email; ?> |  +7 495 227-5-226
                        </span>
                        <span style="display:block;width:100%;margin:-3px 0px 6px 13px;">
                            <a target="_blank" href="<?= $fbUrl; ?>" 
                                    style="color:#6D6D6D;font-size:13px;font-weight:normal;font-style:italic;">
                                <?= $fbUrl; ?>&nbsp;
                            </a>
                        </span>
                        </div>
					</td>
					<td class="w20" width="20"></td>
				</tr>
			</tbody>
		</table>
	</td>
</tr>