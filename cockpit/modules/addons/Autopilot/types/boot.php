<?php

// register core types

$module->registerType('link', [
    'name'   => 'Link',
    'views'  => [ 'edit' => 'autopilot:types/link/admin/edit.php']
]);

$module->registerType('page', [
    'name'   => 'Page',
    'views'  => [ 'edit' => 'autopilot:types/page/admin/edit.php'],
    'render' => function($link, $data) use($module) {

        // look in the theme first
        $view = $module->frontend->path("theme:types/page/render.php");

        if (!$view) {
            $view = $module->app->path("custom:autopilot/types/page/render.php");
        }

        // fall back to default
        if (!$view) {
            $view = $module->app->path("autopilot:types/page/render.php");
        }

        if ($view) {
            return $module->frontend->view($view.' with theme:layout.php', compact('data', 'link'));
        }

        return false;
    }
]);

$module->registerType('php-file', [
    'name'   => 'PHP Script',
    'views'  => [ 'edit' => 'autopilot:types/php-file/admin/edit.php'],
    'render' => function($link, $data) use($module) {

        // look in the theme first
        $view = $module->frontend->path("theme:types/php-file/render.php");

        if (!$view) {
            $view = $module->app->path("custom:autopilot/types/php-file/render.php");
        }

        // fall back to default
        if (!$view) {
            $view = $module->app->path("autopilot:types/php-file/render.php");
        }

        if ($view) {
            return $module->frontend->view($view.' with theme:layout.php', compact('data', 'link'));
        }

        return false;
    }
]);

$module->registerType('collection', [
    'name'   => 'Collection',
    'views'  => [ 'edit' => 'autopilot:types/collection/admin/edit.php'],
    'route'  => function($link) use($module) {

        $data        = $module->helper("menu")->getItemData($link['_id']);
        $listroute   = $link['slug_path'];
        $detailroute = $link['slug_path'].'/*';

        if ($link['home']) {
           $listroute = '/';
        }

        // bind list view
        $module->frontend->bind($listroute, function() use($link, $data, $module) {

            if (!isset($data['collectionId']) && !$data['collectionId']) {
                return false;
            }

            $collection = $module->app->db->findOne('common/collections', ['_id'=>$data['collectionId']]);;

            if (!$collection) {
                return false;
            }

            $this->meta['title']    = $link['title'];
            $this->meta['menuItem'] = $link['_id'];

            // set page keywords
            if (isset($data['meta']['keywords']) && $data['meta']['keywords']) {
                $this->meta['keywords'] = @$data['meta']['keywords'];
            }

            // set page description
            if (isset($data['meta']['description']) && $data['meta']['description']) {
                $this->meta['description'] = @$data['meta']['description'];
            }

            $colname = $collection['name'];

            // look in the theme first
            $view = $this->path("theme:types/collection/{$colname}/list.php");

            if (!$view) {
                $view = $module->app->path("custom:autopilot/types/collection/{$colname}/list.php");
            }

            if (!$view) {
                $view = $module->app->path("custom:autopilot/types/collection/list.php");
            }

            // fall back to default
            if (!$view) {
                $view = $module->app->path("autopilot:types/collection/list.php");
            }

            if ($view) {

                $limit = 10;
                $page  = $this->param("page", 1);
                $count = $module->app->module('collections')->collection($colname)->count();
                $pages = ceil($count/$limit);

                $items = $module->app->module('collections')->find($colname, [
                    'limit' => $limit,
                    'skip'  => ($page-1) * $limit
                ]);

                return $module->frontend->view($view.' with theme:layout.php', compact('link', 'data', 'items', 'page', 'pages'));
            }

            return false;
        });

        // bind detail view
        $module->frontend->bind($detailroute, function($params) use($link, $data, $module) {

            if (!isset($data['collectionId']) && !$data['collectionId']) {
                return false;
            }

            $collection = $module->app->db->findOne('common/collections', ['_id'=>$data['collectionId']]);;

            if (!$collection) {
                return false;
            }

            $colname = $collection['name'];

            // look in the theme first
            $view = $this->path("theme:types/collection/{$colname}/item.php");

            if (!$view) {
                $view = $module->app->path("custom:autopilot/types/collection/{$colname}/item.php");
            }

            if (!$view) {
                $view = $module->app->path("custom:autopilot/types/collection/item.php");
            }

            // fall back to default
            if (!$view) {
                $view = $module->app->path("autopilot:types/collection/item.php");
            }

            if ($view) {

                $routeparts = explode('-', $this['route']);
                $item       = $module->app->module('collections')->findOne($colname, ['_id' => array_pop($routeparts)]);

                if (!$item) {
                    return false;
                }

                return $module->frontend->view($view.' with theme:layout.php', compact('widget', 'settings', 'item'));
            }

            return false;


            return 'detail';
        });
    }
]);