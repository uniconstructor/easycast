<?php

if (!defined('AUTOPILOT_FRONTEND')) define('AUTOPILOT_FRONTEND', 0);


$this->module("autopilot")->extend([

    "types"     => new \ContainerArray([], $module),
    "widgets"   => new \ContainerArray([], $module),
    "themepath" => __DIR__.'/theme',
    "thememeta" => [],

    "helper"    => function($name) {
        return $this->app->helper("autopilot.{$name}");
    },

    "registerType" => function($name, $definition) {

        $definition = new \ContainerArray(array_merge([
            "name"   => $name,
            "route"  => null,
            "render" => null,
            "views"  => []
        ], $definition));

        $this->types->extend($name, $definition);
    },

    "registerWidget" => function($name, $definition) {

        $definition = new \ContainerArray(array_merge([
            "name"   => $name,
            "render" => null,
            "init"   => null,
            "views"  => []
        ], $definition));

        $this->widgets->extend($name, $definition);
    },

    "renderMenu" => function($name, $options = []) {

        $options = array_merge([
            "renderer" => null,
            "class"    => ""
        ], $options);

        $items = $this->helper("menu")->getMenu($name);
        $level = 0;

        if (!count($items)) {
            return;
        }

        $view = $options['renderer'];

        if (!$view) {
            $view = $this->frontend->path("theme:renderers/menu.php");
        }

        if (!$view) {
            $view = $this->app->path("custom:autopilot/renderers/menu.php");
        }

        if (!$view) {
            $view = $this->frontend->path("autopilot:renderers/menu.php");
        }

        return $this->frontend->view($view, compact('options', 'items', 'level'));
    },

    "getMenues" => function() {
        return $this->helper('menu')->getMenues();
    },

    "getMenu" => function($menu) {
        return $this->helper('menu')->getMenu($menu);
    },

    "getFlattenMenues" => function() {
        return $this->helper('menu')->flatten();
    },

    "getFlattenMenu" => function($name) {
        return $this->helper('menu')->flatten($name);
    },

    "settings" => function() {
        return $this->app->db->getKey("autopilot/core", "settings", []);
    },

    "getWidgets" => function($position = null) {
        return [];
    },

    "getPositions" => function() {
        return isset($this->thememeta['positions']) ? $this->thememeta['positions']:['Sidebar'];
    },

    "renderLink" => function($link) {

        $type    = $link['type'];
        $tObject = $this->types[$type];

        if ($tObject) {

            $data = $this->helper("menu")->getItemData($link['_id']);

            $this->frontend->meta['title']       = $link['title'];
            $this->frontend->meta['menuItem']    = $link['_id'];

            // set page keywords
            if (isset($data['meta']['keywords']) && $data['meta']['keywords']) {
                $this->frontend->meta['keywords'] = @$data['meta']['keywords'];
            }

            // set page description
            if (isset($data['meta']['description']) && $data['meta']['description']) {
                $this->frontend->meta['description'] = @$data['meta']['description'];
            }

            return $tObject->render($link, $data);
        }

        return false;
    },

    "widgetsCount" => function($position) {
        return $this->helper('widgets')->filteredWidgets($position);
    },

    "renderWidgets" => function($position, $options = []) {

        $options = array_merge([
            "renderer" => null,
            "class"    => ""
        ], $options);

        $widgets = $this->helper('widgets')->filteredWidgets($position);

        if (!count($widgets)) {
            return;
        }

        $view = $options['renderer'];

        if (!$view) {
            $view = $this->frontend->path("theme:renderers/widgets.php");
        }

        if (!$view) {
            $view = $this->app->path("custom:autopilot/renderers/widgets.php");
        }

        if (!$view) {
            $view = $this->frontend->path("autopilot:renderers/widgets.php");
        }

        return $this->frontend->view($view, compact('options', 'widgets', 'position'));
    },

    "renderWidget" => function($widget, $options = []) {

        $type    = $widget['type'];
        $wObject = $this->widgets[$type];

        if ($wObject) {

            if (isset($wObject->init)) {
                $widget = $wObject->init($widget);
            }

            return $wObject->render($widget, $options);
        }

        return false;
    }
]);

// register helper
$app->helpers["autopilot.menu"]       = 'Autopilot\\Helper\\Menu';
$app->helpers["autopilot.shortcodes"] = 'Autopilot\\Helper\\Shortcodes';
$app->helpers["autopilot.widgets"]    = 'Autopilot\\Helper\\Widgets';
$app->helpers["autopilot.filters"]    = 'Autopilot\\Helper\\Filters';

// register core types + widgets
include_once(__DIR__.'/types/boot.php');
include_once(__DIR__.'/widgets/boot.php');

// set theme path
if ($themepath = $app->path('custom:autopilot/theme')) {
    $module->themepath = $themepath;
}

// load theme meta
if ($themeconfigfile = $app->path($module->themepath.'/theme.config.php')) {
    $module->thememeta = include_once($themeconfigfile);
}

// BOOT ADMIN
if (COCKPIT_ADMIN && !COCKPIT_REST) include_once(__DIR__.'/admin/bootstrap.php');

// ON COCKPIT BOOT
$app->on('cockpit.bootstrap', function() use($module) {

    // register core filters

    # try to fix relative urls
    $module->helper('filters')->add('content', function($content){
        return $this->helper('utils')->fixRelativeUrls($content, $this->app->baseUrl('site:'));
    }, -100);

    # apply short codes
    $module->helper('filters')->add('content', function($content){
        return $this->app->helper('autopilot.shortcodes')->do_shortcode($content);
    }, 10);

    $this->trigger('autopilot.bootstrap');

}, -100);
