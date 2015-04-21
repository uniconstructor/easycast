<?php

namespace Autopilot\Helper;

class Menu extends \Lime\Helper {

    protected $flattenCache = [];

    public function initialize(){

    }

    public function getMenues(){

        $menues = $this->app->db->getKey("autopilot/core", "menues", []);

        return $menues;
    }

    public function getMenu($name){

        $menu = $this->app->db->hget("autopilot/core", "menues", $name, []);

        return $menu;
    }

    public function walk($menu, $fn) {

        $array = $this->getMenu($menu);

        $this->_walk($array, $fn);

        return $array;
    }

    public function _walk(&$array, &$fn) {

        foreach($array as &$item) {

            $fn($item);

            if(isset($item['children']) && count($item['children'])) {
                $this->_walk($item['children'], $fn);
            }
        }

        return $array;
    }

    public function saveMenu($menu, $data) {

        if (isset($this->flattenCache[$menu])){
            unset($this->flattenCache[$menu]);
        }

        $data = $this->_normalizeMenu($data);

        $this->app->trigger('autopilot.data.updated');

        return $this->app->db->hset("autopilot/core", "menues", $menu, $data);
    }

    public function getItem($menu, $id, $extended = false) {

        $flat = $this->flatten($menu);

        return isset($flat[$id]) ? ($extended ? $flat[$id] : $flat[$id]['data']) : null;
    }

    public function saveItemData($item, $data) {

        $id = is_string($item) ? $item : $item['_id'];

        if ($id) {
            return $this->app->db->setKey("autopilot/menudata", $id, $data);
        }

        return false;
    }

    public function getItemData($item) {

        $id = is_string($item) ? $item : $item['_id'];

        if ($id) {
            return $this->app->db->getKey("autopilot/menudata", $id, null);
        }

        return false;
    }

    public function removeItemData($item) {

        $id = is_string($item) ? $item : $item['_id'];

        if ($id) {
            $this->app->helper("autopilot.widgets")->cleanup($id);
            return $this->app->db->removeKey("autopilot/menudata", $id);
        }

        return false;
    }

    public function saveItem($menu, &$item) {

        if (isset($item['_id'])) {
            $this->updateItem($menu, $item);
        } else {
            $this->insertItem($menu, $item);
        }

        return $item;
    }

    public function updateItem($menu, $item) {

        if (!isset($item['_id'])) {
            return false;
        }

        $id   = $item['_id'];
        $flat = $this->flatten($menu);

        if (isset($flat[$id])) {
            $data = $this->getMenu($menu);
            $path = $flat[$id]['path'];

            $savedItem = null;

            eval('$savedItem = $data'.$path.';'); // @TODO: rework without eval

            $savedItem = array_merge($savedItem, $item);

            eval('$data'.$path.' = $savedItem;'); // @TODO: rework without eval

            return $this->saveMenu($menu, $data);
        }

        return false;
    }

    public function insertItem($menu, &$item, $parent = null) {

        $data = $this->getMenu($menu);

        $item['_id'] = uniqid('mi');

        if ($parent) {

            $id       = is_string($item) ? $item : $item['_id'];
            $parentId = is_string($parent) ? $parent : $parent['_id'];
            $flat     = $this->flatten($menu);

            if (isset($flat[$parentId])) {
                eval('$data'.$flat[$parentId]['path'].'[] = $item;'); // @TODO: rework without eval
            }

        } else {
            $data[] = $item;
        }

        $this->saveMenu($menu, $data);
    }

    public function moveItem($menu, $item, $parent, $pos) {

        $data = $this->getMenu($menu);

        if ($parent) {



        } else {

            for ($i=count($data); $i>$pos; $i--){
                $data[$i] = $data[$i-1];
            }

            $data[$pos] = $item;
        }

        $this->saveMenu($menu, $data);
    }

    public function removeItem($menu, $item) {

        if ($path = $this->getPath($menu, $item, false)) {
            $data = $this->getMenu($menu);
            eval('unset($data'.$path.');'); // @TODO: rework without eval
            $this->removeItemData($item);
            $this->saveMenu($menu, $data);
        }
    }

    protected function _normalizeMenu(&$array) {
        $menu = [];
        foreach($array as $item) {
            $menuItem             = $item;
            $menuItem['children'] = (isset($item['children']) && count($item['children'])) ? $this->_normalizeMenu($item['children']) : [];
            $menu[]               = $menuItem;
        }
        return $menu;
    }

    public function getPath($menu, $item, $slug = true) {

        $flat = $this->flatten($menu);
        $id   = is_string($item) ? $item : $item['_id'];
        $path = false;

        if (isset($flat[$id])) {
            $path = $flat[$id][$slug ? 'slug_path':'path'];
        }

        return $path;
    }


    public function flatten($menu = null) {

        if ($menu) {


            $m = $this->getMenu($menu);
            $this->flattenCache[$menu] = $this->_flatten_menu($m);


            return $this->flattenCache[$menu];

        } else {

            $menues = $this->getMenues();

            foreach($menues as $menu => $data) {

                $this->flattenCache[$menu] = $this->_flatten_menu($data);
                $menues[$menu] = $this->flattenCache[$menu];
            }

            return $menues;
        }
    }

    public function _flatten_menu(&$array, $level = 0, $parent = null) {

        $return = [];

        for ($i = 0; $i < count($array); $i++) {

            $item = [];
            $data = $array[$i];

            $item['_id']            = $data['_id'];
            $item['data']           = $data;
            $item['level']          = $level;
            $item['path']           = ($parent ? $parent['path'].'["children"]':'').'['.$i.']';
            $item['slug_path']      = ($parent ? $parent['slug_path']:'').'/'.$data['slug'];
            $item['parent']         = $parent;
            $item['children_count'] = isset($data['children']) ? count($data['children']) : 0;

            $return[$data['_id']] = $item;

            if (isset($data['children']) && count($data['children'])) {

                foreach($this->_flatten_menu($data['children'], ($level+1), $item) as $id => $child) {
                    $return[$id] = $child;
                }
            }
        }

        return $return;
    }

}
