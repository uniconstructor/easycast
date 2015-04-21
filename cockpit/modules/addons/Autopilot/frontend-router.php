<?php

/*
 * autopilot frontend router
 */

define('AUTOPILOT_FRONTEND'  , 1);
define('AUTOPILOT_TIME_START', microtime(true));

global $frontend, $cockpit;

// set default timezone
date_default_timezone_set('UTC');

// boot cockpit
require_once(__DIR__.'/../../../bootstrap.php');

// PATHS + BASE DETECTION
// --------------------------------------------------------------
$SITE_DIR   = dirname(COCKPIT_DIR);
$DOCS_ROOT  = COCKPIT_DOCS_ROOT;

$BASE       = trim(str_replace($DOCS_ROOT, '', $SITE_DIR), "/");
$BASE_URL   = strlen($BASE) ? "/{$BASE}": $BASE;
$BASE_ROUTE = $BASE_URL;

$FRONTEND_ROUTE = str_replace($BASE_URL, '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

$TMP_PATH = $cockpit->path('cache:tmp');

// defines for quick access
define('AUTOPILOT_SITE_DIR'  , $SITE_DIR);
define('AUTOPILOT_DOCS_ROOT' , $DOCS_ROOT);
define('AUTOPILOT_BASE_URL'  , $BASE_URL);
define('AUTOPILOT_BASE_ROUTE', $BASE_URL);
define('AUTOPILOT_TMP_PATH'  , $TMP_PATH);


$CONFIG = [
    'app.name'     => 'autopilotFrontend',
    'base_url'     => $BASE_URL,
    'base_route'   => $BASE_ROUTE,
    'route'        => $FRONTEND_ROUTE,
    'docs_root'    => $DOCS_ROOT,
    'session.name' => md5(__DIR__),
    'sec-key'      => 'c3b44ccc-dbf4-f5h7-a8r4-b4931a15e5e1',
    'i18n'         => 'en',
    'debug'        => false
];

// load custom autopilot config for overrides
if ($customConfig = $cockpit->path('custom:autopilot/config.php')) {
    $CONFIG = array_merge($CONFIG, include($customConfig));
}

// init frontend app
$frontend = new LimeExtra\App($CONFIG);

$cockpit->module('autopilot')->frontend = $frontend;

// load frontend bootfile
include_once(__DIR__.'/frontend/bootstrap.php');

$frontend->trigger('autopilot.frontend.bootstrap');

// init cockpit wide before event
$cockpit->trigger('autopilot.frontend.before', [$frontend]);


$frontend->trigger('autopilot.frontend.init')->run();