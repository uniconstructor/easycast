<?php

// ACL
$app("acl")->addResource("autopilot", ['manage.autopilot']);


$app->on('admin.init', function() {

    if (!$this->module('auth')->hasaccess('autopilot', ['manage.autopilot'])) return;

    $this->trigger('autopilot.admin.init');

    // bind controllers
    $this->bindClass("autopilot\\Controller\\Admin\\Admin", "autopilot");
    $this->bindClass("autopilot\\Controller\\Admin\\Api", "api/autopilot");

    $this('admin')->menu('top', [
        'url'    => $this->routeUrl('/autopilot'),
        'label'  => '<i class="uk-icon-newspaper-o"></i>',
        'title'  => 'Autopilot',
        'active' => (strpos($this['route'], '/autopilot') === 0),

        'children' => new \ArrayObject([
            [
                'url'    => $this->routeUrl('/autopilot'),
                'label'  => '<i class="uk-icon-files-o"></i> '.$this('i18n')->get('Pages'),
                'header' => 'Autopilot'
            ],
            [
                'url'    => $this->routeUrl('/autopilot/widgets'),
                'label'  => '<i class="uk-icon-cubes"></i> '.$this('i18n')->get('Widgets')
            ],
            [
                'url'     => $this->routeUrl('/autopilot/settings'),
                'label'   => '<i class="uk-icon-cogs"></i> '.$this('i18n')->get('Settings'),
                'divider' => true
            ]
        ])
    ], 100);

    // handle global search request
    $this->on('cockpit.globalsearch', function($search, $list) {

        $menues = $this->module('autopilot')->getFlattenMenues();

        foreach($menues as $menu => $links) {

            foreach($links as &$meta) {

                if (stripos($meta['data']['title'], $search)!==false){
                    $list[] = [
                        'title' => '<i class="uk-icon-link"></i> '.$meta['data']['title'],
                        'url'   => $this->routeUrl('/autopilot/menuItem/'.$menu.'/'.$meta['data']['type'].'/'.$meta['data']['_id'])
                    ];
                }
            }
        }

        $widgets = $this->db->find('autopilot/widgets')->toArray();

        foreach($widgets as &$widget) {

            if (stripos($widget['name'], $search)!==false){
                $list[] = [
                    'title' => '<i class="uk-icon-cube"></i> '.$widget['name'],
                    'url'   => $this->routeUrl('/autopilot/widget/'.$widget['type'].'/'.$widget['_id'])
                ];
            }
        }
    });

    // load theme bootfile if exists
    if ($bootfile = $this->path($this->module('autopilot')->themepath.'/bootstrap.php')) {
        include_once($bootfile);
    }
});


// dashboard widget
$app->on("admin.dashboard.main", function() {

    $settings = $this->module('autopilot')->settings();

    $title = 'Autopilot';
    $badge = (isset($settings['maintenance']) && $settings['maintenance']) ? $this("i18n")->get('Maintenance Mode') : null;

    $widgets = $this->db->getCollection('autopilot/widgets')->count();

    $menuItems = 0;

    foreach($this->module('autopilot')->getFlattenMenues() as $menu) {
        $menuItems += count($menu);
    }

    $this->renderView("autopilot:admin/views/dashboard.php with cockpit:views/layouts/dashboard.widget.php", compact('title', 'badge', 'widgets', 'menuItems'));
}, 100);

// @TODO: convert to a more general approach
$app->on('autopilot.data.updated', function(){

    if ($cachefile = $this->path('tmp:autopilot.memory.sqlite')) {
        @unlink($cachefile);
    }
});