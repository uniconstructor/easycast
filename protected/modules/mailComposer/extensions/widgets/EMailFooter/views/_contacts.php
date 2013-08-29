<?php
/**
 * Контакты: email и телефон
 * @var EMailFooter $this
 */
?>
<p id="street-address" class="footer-content-right" align="right">
<small>
<span>Кастинговое агентство <a href="http://easycast.ru" target="_blank">EasyCast</a></span><br>
<?php  
if ( $this->contactPhone )
{
    echo '<span>тел.: '.$this->contactPhone.'</span><br>';
}
if ( $this->contactEmail )
{
    echo '<span>email: '.$this->contactEmail.'</span>';
}
?>
</small>
</p>
