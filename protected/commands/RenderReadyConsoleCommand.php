<?php
/**
 * Took the components necessary to render views from CController, adding it to a console command
 */
class RenderReadyConsoleCommand extends CConsoleCommand
{
    protected $_widgetStack;
    public function run($args) {
    }
    public function renderPartial($view,$data=null,$return=true)
    {
        if(($viewFile=$this->getViewFile($view))!==false)
        {
            $output=$this->renderFile($viewFile,$data,true);
            if($return)
                return $output;
            else
                echo $output;
        }
        else
            throw new CException(Yii::t('yii','{controller} cannot find the requested view "{view}".',
            array('{controller}'=>get_class($this), '{view}'=>$view)));
    }
    /**
     * File Render Methods
     */
    /**
     * Renders a view file.
     *
     * @param string view file path
     * @param array data to be extracted and made available to the view
     * @param boolean whether the rendering result should be returned instead of being echoed
     * @return string the rendering result. Null if the rendering result is not required.
     * @throws CException if the view file does not exist
     */
    public function renderFile($viewFile,$data=null,$return=false)
    {
        $widgetCount=count($this->_widgetStack);
        $content=$this->renderInternal($viewFile,$data,$return);
        if(count($this->_widgetStack)===$widgetCount)
            return $content;
        else
        {
            $widget=end($this->_widgetStack);
            throw new CException(Yii::t('yii','{controller} contains improperly nested widget tags in its view "{view}". A {widget} widget does not have an endWidget() call.',
            array('{controller}'=>get_class($this), '{view}'=>$viewFile, '{widget}'=>get_class($widget))));
        }
    }
    /**
     * Renders a view file.
     * This method includes the view file as a PHP script
     * and captures the display result if required.
     * @param string view file
     * @param array data to be extracted and made available to the view file
     * @param boolean whether the rendering result should be returned as a string
     * @return string the rendering result. Null if the rendering result is not required.
     */
    public function renderInternal($_viewFile_,$_data_=null,$_return_=false)
    {
        // we use special variable names here to avoid conflict when extracting data
        if(is_array($_data_))
            extract($_data_,EXTR_PREFIX_SAME,'data');
        else
            $data=$_data_;
        if($_return_)
        {
            ob_start();
            ob_implicit_flush(false);
            require($_viewFile_);
            return ob_get_clean();
        }
        else
            require($_viewFile_);
    }

    /**
     * File Retrieval Methods
     */
    public function getViewFile($viewName)
    {
        return $this->resolveViewFile($viewName,$this->getViewPath());
    }
    public function getViewPath()
    {
        return dirname(__FILE__).'/../views/email';
    }
    public function resolveViewFile($viewName,$viewPath)
    {
        if(empty($viewName))
            return false;
        $extension='.php';
        $viewFile=$viewPath.DIRECTORY_SEPARATOR.$viewName;
        if(is_file($viewFile.$extension))
            return Yii::app()->findLocalizedFile($viewFile.$extension);
        else if($extension!=='.php' && is_file($viewFile.'.php'))
            return Yii::app()->findLocalizedFile($viewFile.'.php');
        else
            return false;
    }
}