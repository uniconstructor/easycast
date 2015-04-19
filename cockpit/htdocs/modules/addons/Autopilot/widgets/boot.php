<?php

// register core widgets

$module->registerWidget('content', [
    'name'   => 'Content',
    'views'  => [ 'edit' => 'autopilot:widgets/content/admin/edit.php'],
    'render' => function($widget) use($module) {

        $settings = &$widget['settings'];

        // look in the theme first
        $view = $module->frontend->path("theme:widgets/content/widget.php");

        if (!$view) {
            $view = $module->app->path("custom:autopilot/widgets/content/widget.php");
        }

        // fall back to default
        if (!$view) {
            $view = $module->app->path("autopilot:widgets/content/widget.php");
        }

        if ($view) {
            return $module->frontend->view($view, compact('widget', 'settings'));
        }

        return false;
    }
]);