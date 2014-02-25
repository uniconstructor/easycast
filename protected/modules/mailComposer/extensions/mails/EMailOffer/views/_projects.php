<?php
/**
 * Список проектов
 */
/* @var $this EMailOffer */

$criteria        = Yii::app()->getModule('projects')->getProjectsCriteria();
$criteria->limit = 100;
$projects = new CActiveDataProvider('Project', array(
    'criteria'   => $criteria,
    'pagination' => false,
));
?>
<table class="w640" width="640" border="0" cellpadding="0" cellspacing="0">
<tbody>
	<tr>
		<td width="30">&nbsp;</td>
		<td width="580">
			<table width="580" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td><img src="<?= $this->getImageUrl('/images/offer/100.png'); ?>"></td>
                    <td style="text-align:justify;">
                        <span style="width:100%;color:#1E3D52;font-family:century gothic,sans-serif;font-weight:bold;font-size:25px;">
                        ПРИЧИН РАБОТАТЬ С НАМИ
                        </span><br>
                        <span style="width:100%;color:#777777;font-weight:bold;font-size:12px;">
                        ПРОЕКТОВ СОЗДАННЫХ С ИСПОЛЬЗОВАНИЕМ НАШИХ УСЛУГ</span>
                    </td>
                </tr>
			</table>
		</td>
		<td width="30">&nbsp;</td>
	</tr>
</tbody>
</table>
<!-- Список проектов -->
<table class="w640" width="640" border="0" cellpadding="0" cellspacing="0">
<tbody>
	<tr>
		<?php 
		$i = 0;
		foreach ( $projects->getData() as $project )
		{
		    $logo = $this->createProjectLogo($project);
		    echo '<td width="64">'.$logo.'</td>';
		    if ( $i % 10 == 0 AND $i > 0 )
		    {// выводим по 10 проектов в ряд
		        echo '</tr><tr>';
		    }
		    $i++;
		}
		?>
	</tr>
</tbody>
</table>