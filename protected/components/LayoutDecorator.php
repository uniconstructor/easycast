<?php

/**
 * DbContentDecorator class file.
 *
 * @author Smirnov Ilia <frost@easycast.ru>
 */

/**
 * Decorates the content it encloses with the specified view.
 */
class LayoutDecorator extends COutputProcessor
{
	/**
	 * @var int - id of the model
	 * {@link CWebModule::layout default layout}.
	 */
	public $templateId;
	/**
	 * @var array the variables (name=>value) to be extracted and made available in the decorative view.
	 */
	public $data=array();

	/**
	 * Processes the captured output.
     * This method decorates the output with the specified {@link view}.
	 * @param string $output the captured output to be processed
	 */
	public function processOutput($output)
	{
		$output = $this->decorate($output);
		return parent::processOutput($output);
	}

	/**
	 * Decorates the content by rendering a view and embedding the content in it.
	 * The content being embedded can be accessed in the view using variable <code>$content</code>
	 * The decorated content will be displayed directly.
	 * @param string $content the content to be decorated
	 * @return string the decorated content
	 */
	protected function decorate($content)
	{
		$owner=$this->getOwner();
		if($this->view===null)
			$viewFile=Yii::app()->getController()->getLayoutFile(null);
		else
			$viewFile=$owner->getViewFile($this->view);
		if($viewFile!==false)
		{
			$data=$this->data;
			$data['content']=$content;
			return $owner->renderFile($viewFile,$data,true);
		}
		else
			return $content;
	}
}