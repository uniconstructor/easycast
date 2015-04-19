<?php

/*
 * autopilot frontend bootstrap
 */

if (!defined('AUTOPILOT_FRONTEND')) {
    return die();
}

// extend view renderer
$frontend->renderer->extend(function($content){

    $content = preg_replace('/(\s*)@menu\((.+?)\)/'     , '$1<?=$app->autopilot->renderMenu($2);?>', $content);

    $content = preg_replace('/(\s*)@widgets\?\((.+?)\)/', '$1<?php if ($app->autopilot->widgetsCount($2)) { ?>', $content);
    $content = preg_replace('/(\s*)@widgets\((.+?)\)/'  , '$1<?=$app->autopilot->renderWidgets($2);?>', $content);

    $content = preg_replace('/(\s*)@assets\((.+?)\)/'   , '$1<?=$app->assets($2);?>', $content);
    $content = preg_replace('/(\s*)@thumbnail\((.+?)\)/', '$1<?php $app->cockpit->module("mediamanager")->thumbnail($2); ?>', $content);
    $content = preg_replace('/(\s*)@form\((.+?)\)/'     , '$1<?php $app->cockpit->module("forms")->form($2); ?>', $content);
    $content = preg_replace('/(\s*)@region\((.+?)\)/'   , '$1<?php echo $app->cockpit->module("regions")->render($2); ?>', $content);

    return $content;
});

// set paths
$frontend->path('site'     , AUTOPILOT_SITE_DIR);
$frontend->path('autopilot', dirname(__DIR__));
$frontend->path('cockpit'  , COCKPIT_DIR);
$frontend->path('cache'    , $cockpit->path('cache:'));
$frontend->path('theme'    , $cockpit->module('autopilot')->themepath);

// set caching
$frontend("cache")->setCachePath(AUTOPILOT_TMP_PATH);
$frontend->renderer->setCachePath(AUTOPILOT_TMP_PATH);

// site settings
$frontend['settings'] = $cockpit->module('autopilot')->settings();

// set meta
$frontend['meta'] = new \ContainerArray([
    'title'       => $frontend['settings/title'],
    'keywords'    => '',
    'description' => $frontend['settings/description'],
    'menuItem'    => null,
    'route'       => $frontend['route'],
    'admin'       => $cockpit->module("auth")->getUser(),
    'cacheId'     => 'site.cache.'.md5($_SERVER['REQUEST_URI'])
]);

// global assets object
$frontend['assets'] = new ArrayObject([]);

// global view vars
$frontend->viewvars['frontend'] = $frontend;
$frontend->viewvars['cockpit']  = $cockpit;

// register services
$frontend->service('autopilot', function() use($cockpit) {
    return $cockpit->module('autopilot');
});

$frontend->service('cockpit', function() use($cockpit) {
    return $cockpit;
});

$frontend->service('widgets', function() use($cockpit) {
    return $cockpit->helper('autopilot.widgets');
});

$frontend->service('memory', function() use($cockpit) {
    $client = new SimpleStorage\Client(sprintf("redislite://%s/autopilot.memory.sqlite", $cockpit->path('tmp:')));
    return $client;
});

// check for maintenance mode
if ($frontend->retrieve('settings/maintenance', false)) {
    $frontend->renderView('theme:maintenance.php');
    $frontend->stop();
}

// check for cache loading
if ($frontend['settings/cache'] && !$frontend['meta/admin'] && !count($_POST)) {

    if ($content = $frontend->memory->get($frontend['meta/cacheId'])) {
        $frontend->stop($content);
    }
}


// cache menues
$frontend['menues']         = $cockpit->module('autopilot')->getMenues();
$frontend['menues:flatten'] = $cockpit->module('autopilot')->getFlattenMenues();

// match route to menu links
$frontend->on('before', function() use($cockpit) {

    $frontend   = $this;
    $route      = $frontend['route'];

    // homepage
    if ($homeMenuId = $frontend->retrieve('settings/homepage', false)) {

        $menu = $frontend['menues:flatten']['main'];
        $meta = isset($menu[$homeMenuId]) ? $menu[$homeMenuId] : null;

        if ($meta && $meta['data']['active']) {

            $link = $meta['data'];
            $type = $frontend->autopilot->types[$link['type']];

            $link['home']      = true;
            $link['slug_path'] = $meta['slug_path'];

            if ($type["route"]) {
                $type->route($link);
            } else {

                $frontend->bind('/', function() use($meta, $cockpit, $frontend, $link) {
                    return $cockpit->module("autopilot")->renderLink($link);
                });
            }
        }
    }

    // route menu links
    foreach($frontend['menues:flatten'] as $links) {

        foreach($links as &$meta) {

            // filter out inactive paths
            if (!$meta['data']['active']) continue;
            if (strlen($route) < strlen($meta['slug_path'])) continue;
            if (strpos($route, $meta['slug_path'])===false) continue;

            $link = $meta['data'];
            $type = $frontend->autopilot->types[$link['type']];

            $link['slug_path'] = $meta['slug_path'];
            $link['home']      = false;

            if ($type["route"]) {

                $type->route($link);

            } else {

                if ($meta['slug_path'] === $route) {

                    $frontend->bind($meta['slug_path'], function() use($meta, $cockpit, $frontend, $link) {
                        return $cockpit->module("autopilot")->renderLink($link);
                    });
                }
            }
        }
    }

    $cockpit->trigger('autopilot.frontend.before');
});


$frontend->on("after", function() use($cockpit) {

    define('AUTOPILOT_MEMORY_USAGE' , memory_get_peak_usage(true));
    define('AUTOPILOT_TIME_DURATION', microtime(true) - AUTOPILOT_TIME_START);


    // handle error
    switch ($this->response->status) {
        case 500:

            if ($this['debug']) {

                if ($this->req_is('ajax')) {
                    $this->response->body = json_encode(['error' => json_decode($this->response->body, true)]);
                } else {
                    $this->response->body = $this->render("theme:errors/500-debug.php", ['error' => json_decode($this->response->body, true)]);
                }

            } else {

                if ($this->req_is('ajax')) {
                    $this->response->body = '{"error": "500", "message": "system error"}';
                } else {
                    $this->response->body = $this->view("theme:errors/500.php");
                }
            }

           break;
        case 404:
           $this->response->body = $this->view("theme:errors/404.php");
           break;
        case 200:
           $this->response->body = $this->autopilot->helper('filters')->apply('output', $this->response->body);

           // inject admin tools
           if (!$this->req_is('ajax') && $this['meta/admin'] && $this->response->mime == 'html') {

                $this->response->body = str_replace(
                   '</body>',
                   $this->view('autopilot:frontend/manage/inject.php'),
                   $this->response->body
                );
           }

           break;

    }

    $cockpit->trigger('autopilot.frontend.after');

    // cache output
    if ($this->response->status == 200 && $this['settings/cache'] && !$this['meta/admin'] && !count($_POST)) {
        $this->memory->set($this['meta/cacheId'], $this->response->body);
    }
});