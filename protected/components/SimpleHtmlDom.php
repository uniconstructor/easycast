<?php
/**
 * Simple html dom wrapper for Yii
 *
 *	Yii::import('ext.simple_html_dom.*');
 *	$dom = new SimpleHtmlDom();
 *	$dom->load($html);
 *	$ret = $dom->getRoot()->getElementsByTagName('input');
 *
 * @author tpruvot@github
 */

require __DIR__ . 'simpleHtmlDom/simple_html_dom.php';

/**
 * Simple html dom
 *
 * @package simple_html_dom
 */
class SimpleHtmlDom extends CComponent {
	protected $dom;

	public function __construct() {
		$this->dom = new simple_html_dom();
	}

	public function __call($name, array $arguments = array()) {
		return call_user_func_array(array($this->dom, $name), $arguments);
	}

	public function __set($name, $value)
	{
		$this->dom->$name = $value;
	}

	public function __get($name) {
		return $this->dom->$name;
	}

	/* Allow to use DOMElement methods */
	public function getRoot() {
		return $this->find('root', 0);
	}
}
