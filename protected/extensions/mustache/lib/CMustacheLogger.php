<?php
/**
 * Implementation of the `mustache.CMustacheLogger` class.
 * @module CMustacheLogger
 */

/**
 * Component used to log messages from the view engine to the application logger.
 * @class mustache.CMustacheLogger
 * @extends Mustache_Logger_AbstractLogger
 * @constructor
 */
class CMustacheLogger extends Mustache_Logger_AbstractLogger {

  /**
   * The category used when logging messages.
   * @property CATEGORY
   * @type string
   * @static
   * @final
   */
  const CATEGORY='mustache';

  /**
   * Mappings between Mustache levels and Yii ones.
   * @property levels
   * @type array
   * @static
   * @private
   */
  private static $levels=[
    Mustache_Logger::ALERT=>CLogger::LEVEL_ERROR,
    Mustache_Logger::CRITICAL=>CLogger::LEVEL_ERROR,
    Mustache_Logger::DEBUG=>CLogger::LEVEL_TRACE,
    Mustache_Logger::EMERGENCY=>CLogger::LEVEL_ERROR,
    Mustache_Logger::ERROR=>CLogger::LEVEL_ERROR,
    Mustache_Logger::INFO=>CLogger::LEVEL_INFO,
    Mustache_Logger::NOTICE=>CLogger::LEVEL_INFO,
    Mustache_Logger::WARNING=>CLogger::LEVEL_WARNING
  ];

  /**
   * Logs a message.
   * @method log
   * @param {string} $level The logging level.
   * @param {string} $message The message to be logged.
   * @param {array} [$context] The log context.
   */
  public function log($level, $message, array $context=array()) {
    if(!isset(self::$levels[$level])) throw new CException(Yii::t(
      'yii',
      'Invalid enumerable value "{value}". Please make sure it is among ({enum}).',
      [ '{enum}'=>implode(', ', (new ReflectionClass('Mustache_Logger'))->getConstants()), '{value}'=>$value ]
    ));

    Yii::log($message, self::$levels[$level], static::CATEGORY);
  }
}
