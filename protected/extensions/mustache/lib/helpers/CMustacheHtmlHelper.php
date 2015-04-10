<?php
/**
 * Implementation of the `mustache.helpers.CMustacheHtmlHelper` class.
 * @module helpers.CMustacheHtmlHelper
 */
Yii::import('mustache.helpers.CMustacheHelper');

/**
 * Provides a collection of helper methods for creating views.
 * @class mustache.helpers.CMustacheHtmlHelper
 * @extends mustache.helpers.CMustacheHelper
 * @constructor
 */
class CMustacheHtmlHelper extends CMustacheHelper {

  /**
   * Generates the JavaScript that initiates an AJAX request.
   * See: `CHtml::ajax()`
   * @property ajax
   * @type Closure
   * @final
   */
  public function getAjax() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'options');
      return CHtml::ajax($args['options']);
    };
  }

  /**
   * Generates a push button that can initiate AJAX requests.
   * See: `CHtml::ajaxButton()`
   * @property ajaxButton
   * @type Closure
   * @final
   */
  public function getAjaxButton() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'label', [
        'ajaxOptions'=>[],
        'htmlOptions'=>[],
        'url'=>''
      ]);

      return CHtml::ajaxButton($args['label'], $args['url'], $args['ajaxOptions'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a link that can initiate AJAX requests.
   * See: `CHtml::ajaxLink()`
   * @property ajaxLink
   * @type Closure
   * @final
   */
  public function getAjaxLink() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'text', [
        'ajaxOptions'=>[],
        'htmlOptions'=>[],
        'url'=>''
      ]);

      return CHtml::ajaxLink($args['text'], $args['url'], $args['ajaxOptions'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a push button that can submit the current form in POST method.
   * See: `CHtml::ajaxSubmitButton()`
   * @property ajaxSubmitButton
   * @type Closure
   * @final
   */
  public function getAjaxSubmitButton() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'label', [
        'ajaxOptions'=>[],
        'htmlOptions'=>[],
        'url'=>''
      ]);

      return CHtml::ajaxSubmitButton($args['label'], $args['url'], $args['ajaxOptions'], $args['htmlOptions']);
    };
  }

  /**
   * Generates the URL for the published assets.
   * See: `CHtml::asset()`
   * @property asset
   * @type Closure
   * @final
   */
  public function getAsset() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'path', [ 'hashByName'=>false ]);
      return CHtml::asset($args['path'], $args['hashByName']);
    };
  }

  /**
   * Generates an opening form tag.
   * See: `CHtml::beginForm()`
   * @property beginForm
   * @type Closure
   * @final
   */
  public function getBeginForm() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'action', [
        'htmlOptions'=>[],
        'method'=>'post'
      ]);

      return CHtml::beginForm($args['action'], $args['method'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a button.
   * See: `CHtml::button()`
   * @property button
   * @type Closure
   * @final
   */
  public function getButton() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'label', [ 'htmlOptions'=>[] ]);
      return CHtml::button($args['label'], $args['htmlOptions']);
    };
  }

  /**
   * Encloses the given string within a CDATA tag.
   * See: `CHtml::cdata()`
   * @property cdata
   * @type Closure
   * @final
   */
  public function getCdata() {
    return function($value, Mustache_LambdaHelper $helper) {
      return CHtml::cdata($helper->render($value));
    };
  }

  /**
   * Generates a check box.
   * See: `CHtml::checkBox()`
   * @property checkBox
   * @type Closure
   * @final
   */
  public function getCheckBox() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'name', [
        'checked'=>false,
        'htmlOptions'=>[]
      ]);

      return CHtml::checkBox($args['name'], $args['checked'], $args['htmlOptions']);
    };
  }

  /**
   * Creates an absolute URL for the specified route.
   * See: `CController->createAbsoluteUrl()`
   * @property createAbsoluteUrl
   * @type Closure
   * @final
   */
  public function getCreateAbsoluteUrl() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'route', [
        'ampersand'=>'&',
        'params'=>[]
      ]);

      $controller=Yii::app()->controller;
      $callback=($controller ? [ $controller, 'createAbsoluteUrl' ] : [ Yii::app(), 'createAbsoluteUrl' ]);
      return call_user_func($callback, $args['route'], $args['params'], $args['ampersand']);
    };
  }

  /**
   * Creates a relative URL for the specified route.
   * See: `CController->createUrl()`
   * @property createUrl
   * @type Closure
   * @final
   */
  public function getCreateUrl() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'route', [
        'ampersand'=>'&',
        'params'=>[]
      ]);

      $controller=Yii::app()->controller;
      $callback=($controller ? [ $controller, 'createUrl' ] : [ Yii::app(), 'createUrl' ]);
      return call_user_func($callback, $args['route'], $args['params'], $args['ampersand']);
    };
  }

  /**
   * Generates a hidden field for storing the token used to perform CSRF validation.
   * See: `CHttpRequest->csrfToken`
   * @property csrfTokenField
   * @type string
   * @final
   */
  public function getCsrfTokenField() {
    $request=Yii::app()->request;
    return !$request->enableCsrfValidation ? '' : CHtml::hiddenField($request->csrfTokenName, $request->csrfToken, [ 'id'=>false ]);
  }

  /**
   * Encloses the given CSS content with a CSS tag.
   * See: `CHtml::css()`
   * @property css
   * @type Closure
   * @final
   */
  public function getCss() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'text', [ 'media'=>'' ]);
      return CHtml::css($args['text'], $args['media']);
    };
  }

  /**
   * Generates a date field input.
   * See: `CHtml::dateField()`
   * @property dateField
   * @type Closure
   * @final
   */
  public function getDateField() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'name', [
        'htmlOptions'=>[],
        'value'=>''
      ]);

      return CHtml::dateField($args['name'], $args['value'], $args['htmlOptions']);
    };
  }

  /**
   * Generates an email field input.
   * See: `CHtml::emailField()`
   * @property emailField
   * @type Closure
   * @final
   */
  public function getEmailField() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'name', [
        'htmlOptions'=>[],
        'value'=>''
      ]);

      return CHtml::emailField($args['name'], $args['value'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a closing form tag.
   * See: `CHtml::endForm()`
   * @property endForm
   * @type string
   * @final
   */
  public function getEndForm() {
    return CHtml::endForm();
  }

  /**
   * Generates a file input.
   * See: `CHtml::fileField()`
   * @property fileField
   * @type Closure
   * @final
   */
  public function getFileField() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'name', [
        'htmlOptions'=>[],
        'value'=>''
      ]);

      return CHtml::fileField($args['name'], $args['value'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a hidden input.
   * See: `CHtml::hiddenField()`
   * @property hiddenField
   * @type Closure
   * @final
   */
  public function getHiddenField() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'name', [
        'htmlOptions'=>[],
        'value'=>''
      ]);

      return CHtml::hiddenField($args['name'], $args['value'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a button using HTML button tag.
   * See: `CHtml::htmlButton()`
   * @property htmlButton
   * @type Closure
   * @final
   */
  public function getHtmlButton() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'label', [ 'htmlOptions'=>[] ]);
      return CHtml::htmlButton($args['label'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a valid HTML identifier based on name.
   * See: `CHtml::idByName()`
   * @property idByName
   * @type Closure
   * @final
   */
  public function getIdByName() {
    return function($value, Mustache_LambdaHelper $helper) {
      return CHtml::idByName($helper->render($value));
    };
  }

  /**
   * Generates an image submit button.
   * See: `CHtml::imageButton()`
   * @property imageButton
   * @type Closure
   * @final
   */
  public function getImageButton() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'src', [ 'htmlOptions'=>[] ]);
      return CHtml::imageButton($args['src'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a label tag.
   * See: `CHtml::label()`
   * @property label
   * @type Closure
   * @final
   */
  public function getLabel() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'label', [
        'for'=>false,
        'htmlOptions'=>[]
      ]);

      return CHtml::label($args['label'], $args['for'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a hyperlink tag.
   * See: `CHtml::link()`
   * @property link
   * @type Closure
   * @final
   */
  public function getLink() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'text', [
        'htmlOptions'=>[],
        'url'=>'#'
      ]);

      return CHtml::link($args['text'], $args['url'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a link submit button.
   * See: `CHtml::linkButton()`
   * @property linkButton
   * @type Closure
   * @final
   */
  public function getLinkButton() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'label', [ 'htmlOptions'=>[] ]);
      return CHtml::linkButton($args['label'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a mailto link.
   * See: `CHtml::mailto()`
   * @property mailto
   * @type Closure
   * @final
   */
  public function getMailto() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'text', [
        'email'=>'',
        'htmlOptions'=>[]
      ]);

      return CHtml::mailto($args['text'], $args['email'], $args['htmlOptions']);
    };
  }

  /**
   * Inserts HTML line breaks before all newlines in a string.
   * See: `nl2br()`
   * @property nl2br
   * @type Closure
   * @final
   */
  public function getNl2br() {
    return function($value, Mustache_LambdaHelper $helper) {
      return preg_replace('/\r?\n/', CHtml::$closeSingleTags ? '<br />' : '<br>', $helper->render($value));
    };
  }

  /**
   * Generates a number field input.
   * See: `CHtml::numberField()`
   * @property numberField
   * @type Closure
   * @final
   */
  public function getNumberField() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'name', [
        'htmlOptions'=>[],
        'value'=>''
      ]);

      return CHtml::numberField($args['name'], $args['value'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a hidden field for storing persistent page states.
   * See: `CHtml::pageStateField()`
   * @property pageStateField
   * @type Closure
   * @final
   */
  public function getPageStateField() {
    return function($value, Mustache_LambdaHelper $helper) {
      return CHtml::pageStateField($helper->render($value));
    };
  }

  /**
   * Generates a password field input.
   * See: `CHtml::passwordField()`
   * @property passwordField
   * @type Closure
   * @final
   */
  public function getPasswordField() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'name', [
        'htmlOptions'=>[],
        'value'=>''
      ]);

      return CHtml::passwordField($args['name'], $args['value'], $args['htmlOptions']);
    };
  }

  /**
   * Returns a string that can be displayed on your Web page showing Powered-by-Yii information.
   * See: `Yii::powered()`
   * @property powered
   * @type string
   * @final
   */
  public function getPowered() {
    return Yii::powered();
  }

  /**
   * Generates a radio button.
   * See: `CHtml::radioButton()`
   * @property radioButton
   * @type Closure
   * @final
   */
  public function getRadioButton() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'name', [
        'checked'=>false,
        'htmlOptions'=>[]
      ]);

      return CHtml::radioButton($args['name'], $args['checked'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a range field input.
   * See: `CHtml::rangeField()`
   * @property rangeField
   * @type Closure
   * @final
   */
  public function getRangeField() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'name', [
        'htmlOptions'=>[],
        'value'=>''
      ]);

      return CHtml::rangeField($args['name'], $args['value'], $args['htmlOptions']);
    };
  }

  /**
   * Registers a `refresh` meta tag.
   * See: `CHtml::refresh()`
   * @property refresh
   * @type Closure
   * @final
   */
  public function getRefresh() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'seconds', [ 'url'=>'' ]);
      return CHtml::refresh($args['seconds'], $args['url']);
    };
  }

  /**
   * Generates a reset button.
   * See: `CHtml::resetButton()`
   * @property resetButton
   * @type Closure
   * @final
   */
  public function getResetButton() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'label', [ 'htmlOptions'=>[] ]);
      return CHtml::resetButton($args['label'], $args['htmlOptions']);
    };
  }

  /**
   * Encloses the given JavaScript within a script tag.
   * See: `CHtml::script()`
   * @property script
   * @type Closure
   * @final
   */
  public function getScript() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'text', [ 'htmlOptions'=>[] ]);
      return CHtml::script($args['text'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a search field input.
   * See: `CHtml::searchField()`
   * @property searchField
   * @type Closure
   * @final
   */
  public function getSearchField() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'name', [
        'htmlOptions'=>[],
        'value'=>''
      ]);

      CHtml::clientChange('change', $args['htmlOptions']);
      return CHtml::inputField('search', $args['name'], $args['value'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a stateful form tag.
   * See: `CHtml::statefulForm()`
   * @property statefulForm
   * @type Closure
   * @final
   */
  public function getStatefulForm() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'action', [
        'htmlOptions'=>[],
        'method'=>'post'
      ]);

      return CHtml::statefulForm($args['action'], $args['method'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a submit button.
   * See: `CHtml::submitButton()`
   * @property submitButton
   * @type Closure
   * @final
   */
  public function getSubmitButton() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'label', [ 'htmlOptions'=>[] ]);
      return CHtml::submitButton($args['label'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a telephone field input.
   * See: `CHtml::telField()`
   * @property telField
   * @type Closure
   * @final
   */
  public function getTelField() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'name', [
        'htmlOptions'=>[],
        'value'=>''
      ]);

      return CHtml::telField($args['name'], $args['value'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a text area input.
   * See: `CHtml::textArea()`
   * @property textArea
   * @type Closure
   * @final
   */
  public function getTextArea() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'name', [
        'htmlOptions'=>[],
        'value'=>''
      ]);

      return CHtml::textArea($args['name'], $args['value'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a text field input.
   * See: `CHtml::textField()`
   * @property textField
   * @type Closure
   * @final
   */
  public function getTextField() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'name', [
        'htmlOptions'=>[],
        'value'=>''
      ]);

      return CHtml::textField($args['name'], $args['value'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a time field input.
   * See: `CHtml::timeField()`
   * @property timeField
   * @type Closure
   * @final
   */
  public function getTimeField() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'name', [
        'htmlOptions'=>[],
        'value'=>''
      ]);

      return CHtml::timeField($args['name'], $args['value'], $args['htmlOptions']);
    };
  }

  /**
   * Generates a URL field input.
   * See: `CHtml::urlField()`
   * @property urlField
   * @type Closure
   * @final
   */
  public function getUrlField() {
    return function($value, Mustache_LambdaHelper $helper) {
      $args=$this->parseArguments($helper->render($value), 'name', [
        'htmlOptions'=>[],
        'value'=>''
      ]);

      return CHtml::urlField($args['name'], $args['value'], $args['htmlOptions']);
    };
  }
}
