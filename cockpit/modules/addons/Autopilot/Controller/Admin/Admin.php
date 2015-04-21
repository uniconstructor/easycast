<?php

namespace Autopilot\Controller\Admin;


class Admin extends \Cockpit\Controller {

    public function index(){

        $menues        = $this->app->helper("autopilot.menu")->getMenues();
        $flattenMenues = $this->app->helper("autopilot.menu")->flatten();
        $settings      = $this->app->db->getKey("autopilot/core", "settings", null);

        if (!isset($menues['main'])) {
            $menues['main'] = [];
        }

        if (!isset($flattenMenues['main'])) {
            $flattenMenues['main'] = [];
        }

        return $this->render("autopilot:admin/views/index.php", compact('menues', 'flattenMenues', 'settings'));
    }

    public function menuItem($menu, $type, $itemId = null) {

        if (!isset($this->app->module("autopilot")->types[$type])) {
            return false;
        }

        $typeSettings = $this->app->module("autopilot")->types[$type];
        $slug         = '';
        $data         = null;
        $menuItem     = [
            "title"  => "",
            "slug"   => "",
            "type"   => $type,
            "active" => true
        ];

        if ($itemId) {

            $item = $this->app->helper("autopilot.menu")->getItem($menu, $itemId, true);

            if ($item) {

                $menuItem = $item['data'];

                if (isset($menuItem['children'])) {
                    unset($menuItem['children']);
                }

                $slug = $item['parent'] ? $item['parent']['slug_path'] : '';
                $data = $this->app->helper("autopilot.menu")->getItemData($itemId);
            }
        }

        $typeEditView = isset($typeSettings['views']['edit']) ? $typeSettings['views']['edit'] : null;
        $typeview     = $this->app->path($typeEditView);

        return $this->render("autopilot:admin/views/menuItem.php", compact('menu', 'type', 'menuItem', 'slug', 'data', 'typeview'));
    }

    public function settings() {

        $settings = $this->app->module('autopilot')->settings();

        return $this->render("autopilot:admin/views/settings.php", compact('settings'));
    }

    public function widgets() {

        $widgets   = $this->app->db->find('autopilot/widgets', ['sort'=>['order'=>1]])->toArray();
        $positions = $this->app->module('autopilot')->getPositions();

        return $this->render("autopilot:admin/views/widgets.php", compact('widgets', 'positions'));
    }

    public function widget($type, $id = null) {

        if (!$this->app->module("autopilot")->widgets[$type]) {
            return false;
        }

        $widgetSettings = $this->app->module("autopilot")->widgets[$type];
        $widget         = null;

        if ($id) {
            $widget = $this->app->db->findOne('autopilot/widgets', ["_id" => $id]);
        }

        if (!$widget) {

            $widget = [
                'title'       => '',
                'type'        => $type,
                'settings'    => new \ArrayObject([]),
                'position'    => '',
                'order'       => '',
                'active'      => true,
                'ac_links'    => [],
                'ac_patterns' => [],
            ];
        }

        $positions    = $this->app->module('autopilot')->getPositions();
        $typeEditView = isset($widgetSettings['views']['edit']) ? $widgetSettings['views']['edit'] : null;
        $typeview     = $this->app->path($typeEditView);
        $menues       = $this->app->module('autopilot')->helper("menu")->flatten();

        return $this->render("autopilot:admin/views/widget.php", compact('widget', 'positions', 'typeview', 'type', 'menues'));
    }
}
