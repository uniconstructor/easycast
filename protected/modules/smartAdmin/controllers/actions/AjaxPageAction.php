<?php

/**
 * Отображает одну страницу админки, загружая ее через AJAX
 */
class AjaxPageAction extends CViewAction
{
    /**
	 * @var string the name of the GET parameter that contains the requested view name.
	 */
	public $viewParam = 'view';
    /**
	 * @var string the base path for the views. Defaults to 'pages'.
	 * The base path will be prefixed to any user-specified page view.
	 * For example, if a user requests for <code>tutorial.chap1</code>, the corresponding view name will
	 * be <code>pages/tutorial/chap1</code>, assuming the base path is <code>pages</code>.
	 * The actual view file is determined by {@link CController::getViewFile}.
	 * @see CController::getViewFile
	 */
	public $basePath  = '';
    /**
	 * @var mixed the name of the layout to be applied to the views.
	 * This will be assigned to {@link CController::layout} before the view is rendered.
	 * Defaults to null, meaning the controller's layout will be used.
	 * If false, no layout will be applied.
	 */
	public $layout    = '//layouts/ajax/_page';
	/**
	 * @var boolean whether the view should be rendered as PHP script or static text. Defaults to false.
	 */
	public $renderAsText = false;
    
}

