<!-- vkontakte like button -->
<div class="vkontakte social-<?=$this->style;?>">
<div id="<?= urlencode($this->networks['vkontakte']['containerid']);?>"></div>
<script type="text/javascript">
    VK.Widgets.Like("<?= urlencode($this->networks['vkontakte']['containerid']);?>", 
            {
                type: "<?= urlencode($this->networks['vkontakte']['type']);?>"
            }
    );
</script>
</div>