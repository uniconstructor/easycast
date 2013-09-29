<?php
/**
 * Контакты: email и телефон
 * @var EMailFooter $this
 */
?>
<p id="street-address" class="footer-content-right" align="right">
<small style="color:#ffffff;">
<span>Кастинговое агентство <a style="color:#ffffff;" href="http://easycast.ru" target="_blank">easyCast</a></span><br>
<?php  
if ( $this->contactPhone )
{
    echo '<span style="color:#ffffff;">тел.: '.$this->contactPhone.'</span><br>';
}
if ( $this->contactEmail )
{
    echo '<span>email: <a style="color:#ffffff;" href="mailto:'.$this->contactEmail.'">'.$this->contactEmail.'</a></span>';
}
?>
</small>
</p>
