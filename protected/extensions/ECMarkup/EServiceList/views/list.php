<?php
/**
 * Список услуг в коммерческом предложении и на главной
 */
/* @var $this EServiceList */
$imgPathPrefix = Yii::app()->baseUrl.'/images/offer/services/';
?>
<div class="uslugi">
    <ul>
		<li>
			<a href="<?= $this->getServiceLink('media_actors'); ?>"><img src="<?= $imgPathPrefix; ?>s1.png" alt="" title=""/></a>
		</li>
		<li style="margin:0px 0px -16px -56px;z-index: 27; position: relative;">
			<a href="<?= $this->getServiceLink('professional_actors'); ?>"><img src="<?= $imgPathPrefix; ?>s2.png" alt="" title=""/></a>
		</li>
		<li style="margin:0px 0px 0px -52px;z-index: 40; position: relative;">
			<a href="<?= $this->getServiceLink('models'); ?>"><img src="<?= $imgPathPrefix; ?>s3.png" alt="" title=""/></a>
		</li>
		<li style="margin:0px 0px 0px -50px;z-index: 35; position: relative;">
			<a href="<?= $this->getServiceLink('children_section'); ?>"><img src="<?= $imgPathPrefix; ?>s4.png" alt="" title=""/></a>
		</li>
		<li style="margin:0px 0px 0px -52px;z-index: 40;position: relative;">
			<a href="<?= $this->getServiceLink('castings'); ?>"><img src="<?= $imgPathPrefix; ?>s5.png" alt="" title=""/></a>
		</li>
	</ul>
	<ul style="margin-top: -65px;">
		<li style="margin:0px 0px 0px 5px;z-index:30;position: relative;">
			<a href="<?= $this->getServiceLink('mass_actors'); ?>"><img src="<?= $imgPathPrefix; ?>s6.png" alt="" title=""/></a>
		</li>
		<li style="margin:0px 0px 0px -56px;z-index: 28;position: relative;">
			<a href="<?= $this->getServiceLink('emcees'); ?>"><img src="<?= $imgPathPrefix; ?>s7.png" alt="" title=""/></a>
		</li>
		<li style="margin:3px 0px 0px -63px;  z-index: 40; position: relative;">
			<a href="<?= $this->getServiceLink('singers'); ?>"><img src="<?= $imgPathPrefix; ?>s8.png" alt="" title=""/></a>
		</li>
		<li style="margin:0px 0px 0px -50px;  z-index: 34; position: relative;">
			<a href="<?= $this->getServiceLink('dancers'); ?>"><img src="<?= $imgPathPrefix; ?>s9.png" alt="" title=""/></a>
		</li>
		<li style="margin:0px 0px -10px -67px;  z-index: 40; position: relative;">
			<a href="<?= $this->getServiceLink('musicians'); ?>"><img src="<?= $imgPathPrefix; ?>s10.png" alt="" title=""/></a>
		</li>
	</ul> 
	<ul style="margin-top: -63px;">
		<li style="margin:0px 0px 0px -7px;  z-index: 27; position: relative;">
			<a href="<?= $this->getServiceLink('circus_actors'); ?>"><img src="<?= $imgPathPrefix; ?>s11.png" alt="" title=""/></a>
		</li>
		<li style="margin:0px 0px -35px -48px; z-index: 28; position: relative;">
			<a href="<?= $this->getServiceLink(''); ?>"><img src="<?= $imgPathPrefix; ?>s12.png" alt="" title=""/></a>
		</li>
		<li style="margin:3px 0px -11px -53px;  z-index: 40; position: relative;">
			<a href="<?= $this->getServiceLink(''); ?>"><img src="<?= $imgPathPrefix; ?>s13.png" alt="" title=""/></a>
		</li>
		<li style="margin:0px 0px -24px -57px;  z-index: 42; position: relative;">
			<a href="<?= $this->getServiceLink(''); ?>"><img src="<?= $imgPathPrefix; ?>s14.png" alt="" title=""/></a>
		</li>
		<li style="margin:0px 0px -40px -67px;  z-index: 40; position: relative;">
			<a href="<?= $this->getServiceLink(''); ?>"><img src="<?= $imgPathPrefix; ?>s15.png" alt="" title=""/></a>
		</li>
	</ul> 
</div>