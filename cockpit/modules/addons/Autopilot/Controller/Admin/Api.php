<?php

namespace Autopilot\Controller\Admin;


class Api extends \Cockpit\Controller {


    public function updateSettings() {

        if ($settings = $this->param('settings', null)) {

            $old = $this->app->db->getKey("autopilot/core", "settings", null);

            if (!$old) $old = [];

            $settings = array_merge($old, $settings);

            $this->app->db->setKey("autopilot/core", "settings", $settings);
            $this->app->trigger('autopilot.data.updated');
        }

        return json_encode(["success"=>true]);
    }

    public function saveMenu() {

        $menu      = $this->param('menu', 'main');
        $structure = $this->param('structure', false);


        if ($structure !== false) {
            $this->app->module('autopilot')->helper("menu")->saveMenu($menu, $structure);
        }

        return json_encode(["success"=>true]);
    }


    public function saveMenuItem() {

        $menu = $this->param('menu', 'main');

        if ($item = $this->param('item', null)) {

            $this->app->module('autopilot')->helper("menu")->saveItem($menu, $item);
        }


        return json_encode($item);
    }

    public function saveMenuItemData() {

        $menu   = $this->param('menu', 'main');
        $itemId = $this->param('item', null);
        $data   = null;

        if ($data = $this->param('data', null)) {
            $this->app->module('autopilot')->helper("menu")->saveItemData($itemId, $data);
        }

        return json_encode($data);
    }

    public function removeMenuItem() {

        $menu = $this->param('menu', 'main');

        if ($item = $this->param('item', null)) {

            $this->app->module('autopilot')->helper("menu")->removeItem($menu, $item);
        }

        return json_encode(["success"=>true]);
    }


    public function saveWidget() {

        $widget = null;

        if ($widget = $this->param('widget', null)) {
            $this->app->db->save('autopilot/widgets', $widget);
            $this->app->trigger('autopilot.data.updated');
        }

        return json_encode($widget);
    }

    public function removeWidget() {

        $widget = null;

        if ($widget = $this->param('widget', null)) {
            $this->app->db->remove('autopilot/widgets', ["_id" => $widget["_id"]]);
            $this->app->trigger('autopilot.data.updated');
        }

        return json_encode($widget);
    }

    public function duplicateWidget(){

        $widgetId = $this->param("widgetId", null);

        if ($widgetId) {

            $widget = $this->app->db->findOneById("autopilot/widgets", $widgetId);

            if ($widget) {

                unset($widget['_id']);

                $widget["title"] .= ' (copy)';
                $widget["order"] = '';

                $this->app->db->save("autopilot/widgets", $widget);

                $this->app->trigger('autopilot.data.updated');

                return json_encode($widget);
            }
        }

        return false;
    }

    public function updateWidgetOrder(){

        $widgets = $this->param("widgets", null);

        if ($widgets) {

            foreach($widgets as $order => $id) {
                $widget = ["_id" => $id, "order" => $order];
                $this->app->db->save("autopilot/widgets", $widget);
            }

            $this->app->trigger('autopilot.data.updated');

            return json_encode($widgets);
        }

        return false;
    }
}
