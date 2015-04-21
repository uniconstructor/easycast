<?php

namespace Autopilot\Helper;

class Widgets extends \Lime\Helper {

    protected $frontendPosCache = [];

    public function widgets($position) {

        $widgets = $this->app->db->find('autopilot/widgets', [
            'filter' => ['active' => true, 'position' => $position],
            'sort'   => ['order'=>1]
        ]);

        return $widgets;
    }

    public function count($position = null) {

        $filter = ['active' => true];

        if ($position) {
            $filter['position'] = $position;
        }

        $widgets = $this->app->db->count('autopilot/widgets', $filter);

        return $widgets;
    }


    public function filteredWidgets($position) {

        if (isset($this->frontendPosCache[$position])) {
            return $this->frontendPosCache[$position];
        }

        $frontend = $this->app->module("autopilot")->frontend;
        $widgets  = $this->widgets($position);

        //filter

        $linkId   = $frontend['meta/menuItem'];
        $route    = $frontend['route'];
        $filtered = [];

        foreach($widgets as &$widget) {

            // filter by menu id
            if ($linkId && count($widget['ac_links'])) {
                if(!in_array($linkId, $widget['ac_links'])) continue;
            }

            // filter by route pattern
            if ($route && isset($widget['ac_patterns']) && $widget['ac_patterns']) {

                // rules validation
                $rules = trim(preg_replace('/#(.+)/', '', $widget['ac_patterns'])); // trim and replace comments

                if ($rules) {

                    $pass  = true;
                    $lines = explode("\n", $rules);

                    // validate every rule
                    foreach ($lines as $rule) {

                        $rule = trim($rule);

                        if (!$rule) continue;

                        $ret = $rule[0] == '!' ? false : true;

                        if (!$ret) {
                            $rule = substr($rule, 1);
                        }

                        if (fnmatch($rule, $route)) {
                            $pass = $ret;
                            break;
                        }
                    }

                    if (!$pass) continue;
                }

            }

            $filtered[] = $widget;
        }

        $this->frontendPosCache[$position] = $filtered;


        return $this->frontendPosCache[$position];
    }

    public function filteredCount($position) {

        return count($this->filteredWidgets($position));
    }

    /**
     * Cleanup widgets from deleted menu links
     */
    public function cleanup($menuLinkIds) {

        $widgets     = $this->app->db->find('autopilot/widgets');
        $menuLinkIds = (array)$menuLinkIds;

        foreach($widgets as &$widget) {

            if (!count($widget['ac_links'])) continue;

            $ids = [];

            foreach($widget['ac_links'] as $id) {
                if (!in_array($id, $menuLinkIds)) $ids[] = $id;
            }

            if (count($ids) != count($widget['ac_links'])) {

                $widget['ac_links'] = $ids;
                $this->app->db->save('autopilot/widgets', $widget);
            }
        }

    }
}