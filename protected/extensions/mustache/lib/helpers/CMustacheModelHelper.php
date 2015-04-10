<?php
/**
 * Implementation of the `mustache.helpers.CMustacheModelHelper` class.
 * @module helpers.CMustacheModelHelper
 */
Yii::import('mustache.helpers.CMustacheHelper');

/**
 * Provides a collection of helper methods for creating views based on a data model.
 * @class mustache.helpers.CMustacheModelHelper
 * @extends mustache.helpers.CMustacheHelper
 * @constructor
 * @param {system.base.CModel} $model The data model.
 */
class CMustacheModelHelper extends CMustacheHelper {

  public function __construct(CModel $model) {
    $this->model=$model;
  }

  /**
   * The underlying data model.
   * @property model
   * @type CModel
   * @private
   */
  private $model;

  /**
   * Calls the named method which is not a class method.
   * @method __call
   * @param {string} $name The method name.
   * @param {array} $arguments The method parameters.
   * @return {mixed} The method return value.
   */
  public function __call($name, $arguments) {
    try { return parent::__call($name, $arguments); }
    catch(CException $e) { return call_user_func_array([ $model, $name ], $arguments); }
  }

  /**
   * Returns a property value, an event handler list or a behavior based on its name.
   * @method __get
   * @param {string} $name The property name or event name.
   * @return {mixed} The property value, event handlers attached to the event, or the named behavior.
   */
  public function __get($name) {
    try { return parent::__get($name); }
    catch(CException $e) { return $this->model->$name; }
  }

  /**
   * Checks if a property value is `null`.
   * @method __isset
   * @param {string} $name The property name or event name.
   * @return {boolean} `true` if the property value is `null`, otherwise `false`.
   */
  public function __isset($name) {
    return parent::__isset($name) ? true : isset($this->model->$name);
  }

  /**
   * Returns a string that represents the data model.
   * @method __toString
   * @return {string} A string that represents the data model.
   */
  public function __toString() {
    return (string) $this->model;
  }

  /**
   * Generates a check box for a model attribute.
   * See: `CHtml::activeCheckBox()`
   * @property activeCheckBox
   * @type Closure
   * @final
   */
  public function getActiveCheckBox() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'attribute', [ 'htmlOptions'=>[] ]);
      return CHtml::activeCheckBox($this->model, $args['attribute'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a date field input for a model attribute.
   * See: `CHtml::activeDateField()`
   * @property activeDateField
   * @type Closure
   * @final
   */
  public function getActiveDateField() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'attribute', [ 'htmlOptions'=>[] ]);
      return CHtml::activeDateField($this->model, $args['attribute'], $args['htmlOptions']);
    };
  }

  /**
   * Generates an email field input for a model attribute.
   * See: `CHtml::activeEmailField()`
   * @property activeEmailField
   * @type Closure
   * @final
   */
  public function getActiveEmailField() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'attribute', [ 'htmlOptions'=>[] ]);
      return CHtml::activeEmailField($this->model, $args['attribute'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a file input for a model attribute.
   * See: `CHtml::activeFileField()`
   * @property activeFileField
   * @type Closure
   * @final
   */
  public function getActiveFileField() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'attribute', [ 'htmlOptions'=>[] ]);
      return CHtml::activeFileField($this->model, $args['attribute'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a hidden input for a model attribute.
   * See: `CHtml::activeHiddenField()`
   * @property activeHiddenField
   * @type Closure
   * @final
   */
  public function getActiveHiddenField() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'attribute', [ 'htmlOptions'=>[] ]);
      return CHtml::activeHiddenField($this->model, $args['attribute'], $args['htmlOptions']);
    };
  }

  /**
   * Generates input field identifier for a model attribute.
   * See: `CHtml::activeId()`
   * @property activeId
   * @type Closure
   * @final
   */
  public function getActiveId() {
    return function($value, Mustache_LambdaHelper $helper) {
      return CHtml::activeId($this->model, $helper->render($value));
    };
  }

  /**
   * Generates a label tag for a model attribute.
   * See: `CHtml::activeLabel()`
   * @property activeLabel
   * @type Closure
   * @final
   */
  public function getActiveLabel() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'attribute', [ 'htmlOptions'=>[] ]);
      return CHtml::activeLabel($this->model, $args['attribute'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a label tag for a model attribute.
   * See: `CHtml::activeLabelEx()`
   * @property activeLabelEx
   * @type Closure
   * @final
   */
  public function getActiveLabelEx() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'attribute', [ 'htmlOptions'=>[] ]);
      return CHtml::activeLabelEx($this->model, $args['attribute'], $args['htmlOptions']);
    };
  }

  /**
   * Generates input field name for a model attribute.
   * See: `CHtml::activeName()`
   * @property activeName
   * @type Closure
   * @final
   */
  public function getActiveName() {
    return function($value, Mustache_LambdaHelper $helper) {
      return CHtml::activeName($this->model, $helper->render($value));
    };
  }

  /**
   * Generates a number field input for a model attribute.
   * See: `CHtml::activeNumberField()`
   * @property activeNumberField
   * @type Closure
   * @final
   */
  public function getActiveNumberField() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'attribute', [ 'htmlOptions'=>[] ]);
      return CHtml::activeNumberField($this->model, $args['attribute'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a password field input for a model attribute.
   * See: `CHtml::activePasswordField()`
   * @property activePasswordField
   * @type Closure
   * @final
   */
  public function getActivePasswordField() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'attribute', [ 'htmlOptions'=>[] ]);
      return CHtml::activePasswordField($this->model, $args['attribute'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a radio button for a model attribute.
   * See: `CHtml::activeRadioButton()`
   * @property activeRadioButton
   * @type Closure
   * @final
   */
  public function getActiveRadioButton() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'attribute', [ 'htmlOptions'=>[] ]);
      return CHtml::activeRadioButton($this->model, $args['attribute'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a range field input for a model attribute.
   * See: `CHtml::activeRangeField()`
   * @property activeRangeField
   * @type Closure
   * @final
   */
  public function getActiveRangeField() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'attribute', [ 'htmlOptions'=>[] ]);
      return CHtml::activeRangeField($this->model, $args['attribute'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a search field input for a model attribute.
   * See: `CHtml::activeSearchField()`
   * @property activeSearchField
   * @type Closure
   * @final
   */
  public function getActiveSearchField() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'attribute', [ 'htmlOptions'=>[] ]);
      return CHtml::activeSearchField($this->model, $args['attribute'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a telephone field input for a model attribute.
   * See: `CHtml::activeTelField()`
   * @property activeTelField
   * @type Closure
   * @final
   */
  public function getActiveTelField() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'attribute', [ 'htmlOptions'=>[] ]);
      return CHtml::activeTelField($this->model, $args['attribute'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a text area input for a model attribute.
   * See: `CHtml::activeTextArea()`
   * @property activeTextArea
   * @type Closure
   * @final
   */
  public function getActiveTextArea() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'attribute', [ 'htmlOptions'=>[] ]);
      return CHtml::activeTextArea($this->model, $args['attribute'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a text field input for a model attribute.
   * See: `CHtml::activeTextField()`
   * @property activeTextField
   * @type Closure
   * @final
   */
  public function getActiveTextField() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'attribute', [ 'htmlOptions'=>[] ]);
      return CHtml::activeTextField($this->model, $args['attribute'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a time field input for a model attribute.
   * See: `CHtml::activeTimeField()`
   * @property activeTimeField
   * @type Closure
   * @final
   */
  public function getActiveTimeField() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'attribute', [ 'htmlOptions'=>[] ]);
      return CHtml::activeTimeField($this->model, $args['attribute'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a URL field input for a model attribute.
   * See: `CHtml::activeUrlField()`
   * @property activeUrlField
   * @type Closure
   * @final
   */
  public function getActiveUrlField() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'attribute', [ 'htmlOptions'=>[] ]);
      return CHtml::activeUrlField($this->model, $args['attribute'], $args['htmlOptions']);
    };
  }

  /**
   * Returns the text label for a model attribute.
   * See: `CModel->getAttributeLabel()`
   * @property attributeLabel
   * @type Closure
   * @final
   */
  public function getAttributeLabel() {
    return function($value, Mustache_LambdaHelper $helper) {
      return CHtml::encode($this->model->getAttributeLabel($helper->render($value)));
    };
  }

  /**
   * Creates an absolute URL for the specified route.
   * If the model is an instance of `CActiveRecord`, the resulting URL contains the model primary key as query parameters.
   * See: `CController->createAbsoluteUrl()`
   * @property createAbsoluteUrl
   * @type Closure
   * @final
   */
  public function getCreateAbsoluteUrl() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'route', [
        'ampersand'=>'&',
        'params'=>static::getQueryParams($this->model)
      ]);

      $controller=Yii::app()->controller;
      $callback=($controller ? [ $controller, 'createAbsoluteUrl' ] : [ Yii::app(), 'createAbsoluteUrl' ]);
      return call_user_func($callback, $args['route'], $args['params'], $args['ampersand']);
    };
  }

  /**
   * Creates a relative URL for the specified route.
   * If the model is an instance of `CActiveRecord`, the resulting URL contains the model primary key as query parameters.
   * See: `CController->createUrl()`
   * @property createUrl
   * @type Closure
   * @final
   */
  public function getCreateUrl() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'route', [
        'ampersand'=>'&',
        'params'=>static::getQueryParams($this->model)
      ]);

      $controller=Yii::app()->controller;
      $callback=($controller ? [ $controller, 'createUrl' ] : [ Yii::app(), 'createUrl' ]);
      return call_user_func($callback, $args['route'], $args['params'], $args['ampersand']);
    };
  }

  /**
   * Displays the first validation error for a model attribute.
   * See: `CHtml::error()`
   * @property error
   * @type Closure
   * @final
   */
  public function getError() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'attribute', [ 'htmlOptions'=>[] ]);
      return CHtml::error($this->model, $args['attribute'], $args['htmlOptions']);
    };
  }

  /**
   * Returns the first validation error of all model attributes.
   * See: `CModel->getErrors()`
   * @property errors
   * @type array
   * @final
   */
  public function getErrors() {
    return array_map(function($errors) { return $errors[0]; }, $this->model->errors);
  }

  /**
   * Displays a summary of validation errors for a model.
   * See: `CHtml::errorSummary()`
   * @property errorSummary
   * @type Closure
   * @final
   */
  public function getErrorSummary() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'header', [
        'footer'=>null,
        'htmlOptions'=>[]
      ]);

      return CHtml::errorSummary($this->model, $args['header'], $args['footer'], $args['htmlOptions']);
    };
  }

  /**
   * Generates HTML name for the model.
   * See: `CHtml::modelName()`
   * @property modelName
   * @type string
   * @final
   */
  public function getModelName() {
    return CHtml::modelName($this->model);
  }

  /**
   * Evaluates the value of a model attribute.
   * See: `CHtml::value()`
   * @property value
   * @type Closure
   * @final
   */
  public function getValue() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'attribute', [ 'defaultValue'=>null ]);
      return CHtml::encode(CHtml::value($this->model, $args['attribute'], $args['defaultValue']));
    };
  }

  /**
   * Gets the URL query parameters corresponding to the primary key of the specified model.
   * This methods returns an empty array if the model is not an instance of `CActiveRecord`.
   * @method getQueryParams
   * @param {system.base.CModel} $model The model providing the primary key.
   * @return {array} The query parameters corresponding to the model primary key, or an empty array if model has no primary key.
   */
  private static function getQueryParams(CModel $model) {
    if(!$model instanceof CActiveRecord) return [];

    $keys=$model->tableSchema->primaryKey;
    if(!is_array($keys)) $keys=[ $keys ];

    $values=$model->primaryKey;
    if(!is_array($values)) $values=[ $values ];

    return array_combine($keys, $values);
  }
}
