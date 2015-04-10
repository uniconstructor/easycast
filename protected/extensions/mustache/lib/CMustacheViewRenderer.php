<?php

/**
 * Implementation of the `mustache.CMustacheViewRenderer` class.
 * @module CMustacheViewRenderer
 */
Yii::import('mustache.CMustacheCache');
Yii::import('mustache.CMustacheLoader');
Yii::import('mustache.CMustacheLogger');
Yii::import('mustache.helpers.CMustacheFormatHelper');
Yii::import('mustache.helpers.CMustacheHtmlHelper');
Yii::import('mustache.helpers.CMustacheI18nHelper');
/**
 * View renderer allowing to use the [Mustache](http://mustache.github.io) template syntax.
 * @class mustache.CMustacheViewRenderer
 * @extends system.base.CApplicationComponent
 * @constructor
 */
class CMustacheViewRenderer extends CApplicationComponent implements IViewRenderer
{
    /**
     * The identifier of the cache application component that is used to cache the compiled views.
     * If `null` or empty, defaults to using a `system.caching.CFileCache` component.
     * @property cacheID
     * @type string
     * @default null
     */
    public $cacheID = null;
    /**
     * Value indicating whether to enable the caching of compiled views.
     * @property enableCaching
     * @type boolean
     * @default true
     */
    public $enableCaching = true;
    /**
     * The underlying Mustache template engine.
     * @property engine
     * @type Mustache_Engine
     * @private
     */
    private $engine;
    /**
     * The path alias of the directory containing the Mustache template engine.
     * @property enginePathAlias
     * @type string
     * @default "ext.mustache.mustache.src.Mustache"
     */
    public $enginePathAlias = 'ext.mustache.mustache.src.Mustache';
    /**
     * The extension name of the view files.
     * @property fileExtension
     * @type string
     * @default ".mustache"
     */
    public $fileExtension = '.mustache';
    /**
     * Values prepended to the context stack, so they will be available in any view loaded by this instance.
     * Always `null` until the component is fully initialized.
     * @property helpers
     * @type Mustache_HelperCollection
     */
    private $helpers = [];

    public function getHelpers()
    {
        return $this->isInitialized ? $this->engine->getHelpers() : null;
    }

    public function setHelpers(array $value)
    {
        if ( $this->isInitialized )
            $this->engine->setHelpers($value);
        else
            $this->helpers = $value;
    }

    /**
     * Initializes the application component.
     * @method init
     */
    public function init()
    {
        if ( !class_exists('Mustache_Autoloader', false) )
        {
            require_once Yii::getPathOfAlias($this->enginePathAlias).'/Autoloader.php';
            Yii::registerAutoloader([ new Mustache_Autoloader(), 'autoload']);
        }

        $helpers = [
            'app'    => Yii::app(),
            'format' => new CMustacheFormatHelper(),
            'html'   => new CMustacheHtmlHelper(),
            'i18n'   => new CMustacheI18nHelper()
        ];

        $options           = [
            'charset'      => Yii::app()->charset,
            'entity_flags' => ENT_QUOTES,
            'escape'       => function($value)
            {
                return CHtml::encode($value);
            },
            'helpers'          => CMap::mergeArray($helpers, $this->helpers),
            'logger'           => new CMustacheLogger(),
            'partials_loader'  => new CMustacheLoader($this->fileExtension),
            'strict_callables' => true
        ];

        if ( $this->enableCaching )
        {
            $cache = Yii::createComponent([
                    'class'     => 'system.caching.CFileCache',
                    'cachePath' => Yii::app()->runtimePath.'/views/mustache'
            ]);

            if ( $this->cacheID )
            {
                $component = Yii::app()->getComponent($this->cacheID);
                if ( $component instanceof ICache )
                    $cache     = $component;
            }

            if ( $cache instanceof CFileCache && !is_dir($cache->cachePath) )
                @mkdir($cache->cachePath, 0777, true);
            $options['cache'] = new CMustacheCache($cache);
        }

        $this->engine  = new Mustache_Engine($options);
        parent::init();
        $this->helpers = [];
    }

    /**
     * Renders a view file.
     * @method renderFile
     * @param {system.web.CBaseController} $context The controller or widget who is rendering the view file.
     * @param {string} $sourceFile The view file path.
     * @param {array} $data The data to be passed to the view.
     * @param {boolean} $return Whether the rendering result should be returned.
     * @return {string} The rendering result, or `null` if the rendering result is not needed.
     */
    public function renderFile($context, $sourceFile, $data, $return)
    {
        if ( !is_file($sourceFile) )
            throw new CException(Yii::t('yii', 'View file "{file}" does not exist.', [ '{file}' => $sourceFile]));

        $input  = file_get_contents($sourceFile);
        $values = CMap::mergeArray([ 'this' => $context], is_array($data) ? $data : []);
        $output = $this->engine->render($input, $values);

        if ( $return )
            return $output;
        echo $output;
    }

    /**
     * Render template from string
     * 
     * @param string $input  - template string
     * @param array  $data   - template data
     * @param bool   $return - return result if true
     * @return string|null
     */
    public function renderTemplate($input, $data, $return=false)
    {
        $this->init();
        if ( $return )
        {
            return $this->engine->render($input, $data);
        }
        echo $this->engine->render($input, $data);
    }
}
