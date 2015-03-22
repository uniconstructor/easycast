<?php
/**
 * Код для отображения на сайте онлайн-консультанта (клиент zopim.com)
 */
?>
<!--Start of Zopim Live Chat Script-->
<noindex>
<script type="text/javascript">
window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');
$.src='//v2.zopim.com/?1jwtx0mz7NF3bfar8I0vxNEn7iLRzlDC';z.t=+new Date;$.
type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');
<?php

if ( ! Yii::app()->user->isGuest )
{// если пользователь уже зашел на сайт - автоматически подставим его имя и email в онлайн-чат
    $name  = Yii::app()->getModule('user')->user()->questionary->fullname;
    $email = Yii::app()->getModule('user')->user()->email;
    
    echo "\$zopim(function() {";
    if ( $email )
    {
        echo "\$zopim.livechat.setEmail('{$email}');";
    }
    if ( $name )
    {
        echo "\$zopim.livechat.setName('{$name}');";
    }
    echo "});";
}
?>
</script>
</noindex>
<!--End of Zopim Live Chat Script-->