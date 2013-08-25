<?php
/**
 * social.php
 *
 * @author Ovidiu Pop <matricks@webspider.ro>
 * @copyright 2011 Binary Technology
 * @license released under dual license BSD License and LGP License
 * @package social
 * @version 0.1
 */
class ESocial extends CInputWidget
{
    /**
     * @var bool
     */
    public $lazyLoading = false;
    /**
     * @var bool
     */
    public $renderAjaxData = false;
    /**
     * @var string
     */
    public $containerId = 'social_buttons_container';
	/**
	 * @var string buttons alignment - horizontal, vertical
	 */
	public $style = 'horizontal';//vertical
	/**
	 * @var array available social network buttons 
	 */
	public $networks = array(
		'twitter'=>array(
			'data-via'=>'', //http://twitter.com/#!/YourPageAccount if exists else leave empty
		), 
		'googleplusone'=>array(
			"size"=>"medium",
			"annotation"=>"bubble",
		), 
		'facebook'=>array(
			'href'=>'http://www.facebook.com/page',//asociate your page http://www.facebook.com/page 
			'action'=>'recommend',//recommend
			'colorscheme'=>'light',
			'width'=>'200px',
	    ),
        /**
         * vkontakte button
         * More information can be found: http://vk.com/developers.php?o=-1&p=%C4%EE%EA%F3%EC%E5%ED%F2%E0%F6%E8%FF+%EA+%E2%E8%E4%E6%E5%F2%F3+%CC%ED%E5+%ED%F0%E0%E2%E8%F2%F1%FF
         */
        'vkontakte'=>array(
            'apiid'       => '', // vkontakte API IP (gains after widget registration)
            'containerid' => 'vk_like', // id if the div-container. Must be unique for the page
            'scriptid'    => 'vkontakte-init-script', // YII unique script id
            'type'        => 'button', // type of the widget (full|button|mini|vertical)
            'text'        => 'EasyCast.ru - предложения о съемках',
        ),
        'mailru' => array(
            'type' => 'combo', // (combo|mm|ok)
        ),
	);

	/**
	 * The extension initialisation
	 *
	 * @return nothing
	 */
	public function init()
	{
		self::registerFiles();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 */
	public function run()
	{
	    self::renderSocial();
	}

	/**
	 * Register assets file
	 *
	 * @return nothing
	 */
	private function registerFiles()
	{
		$assets  = dirname(__FILE__).'/assets';
		$baseUrl = Yii::app()->assetManager->publish($assets);

		if ( is_dir($assets) )
        {
			Yii::app()->clientScript->registerCssFile($baseUrl . '/social.css');
		}else
		{
		    throw new Exception(Yii::t('social - Error: Couldn\'t find assets folder to publish.'));
		}

		if ( array_key_exists('googleplusone', $this->networks) )
        {
            Yii::app()->clientScript->registerScriptFile('https://apis.google.com/js/plusone.js?parsetags=explicit', CClientScript::POS_HEAD);
        }
	    
        if ( array_key_exists('vkontakte', $this->networks) )
        {
            Yii::app()->clientScript->registerScriptFile('http://vk.com/js/api/openapi.js?87', CClientScript::POS_HEAD);
            $vkInitScript = 'VK.init({apiId: "'.$this->networks['vkontakte']['apiid'].'", onlyWidgets: true});';
            Yii::app()->clientScript->registerScript('social', $vkInitScript, CClientScript::POS_HEAD);
        }
        
        if ( $this->lazyLoading )
        {// load ajax script instread of widgets
            $lazyLoadingScript = "\$.post('/site/loadSocial', function(data) {
                \$('#{$this->containerId}').html(data);
            });"; 
            Yii::app()->clientScript->registerScript('socialLazyLoading', $lazyLoadingScript, CClientScript::POS_END);
        }
	}

	/**
	 * Render social extension
	 *
	 * @return nothing
	 */
	private function renderSocial()
	{
		$rendered = '';
		foreach ( $this->networks as $network => $params )
		{
		    $rendered .= $this->render($network, array(), true);
		}
		if ( $this->renderAjaxData )
		{
		    echo $rendered;
		}else
		{
		    echo $this->render('social', array('rendered' => $rendered));
		}
	}
}