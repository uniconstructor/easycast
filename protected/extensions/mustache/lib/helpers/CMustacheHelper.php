<?php
/**
 * Implementation of the `mustache.helpers.CMustacheHelper` class.
 * @module helpers.CMustacheHelper
 */

/**
 * Provides the abstract base class for a view helper.
 * @class mustache.helpers.CMustacheHelper
 * @extends system.base.CComponent
 * @constructor
 */
abstract class CMustacheHelper extends CComponent {

  /**
   * Parses the arguments of a parametized helper.
   * Arguments can be specified as a single value, or as a string in JSON format.
   * @method parseArguments
   * @param {string} $text The section content specifying the helper arguments.
   * @param {string} $defaultArgument The name of the default argument. This is used when the section content provides a plain string instead of a JSON object.
   * @param {array} [$defaultValues] The default values of arguments. These are used when the section content does not specify all arguments.
   * @return {array} The parsed arguments as an associative array.
   */
  protected function parseArguments($text, $defaultArgument, array $defaultValues=[]) {
    $args=$defaultValues;

    $json=CJSON::decode($text);
    if(is_array($json)) return CMap::mergeArray($args, $json);

    $args[$defaultArgument]=$text;
    return $args;
  }
}
