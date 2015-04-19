<?php

define('COCKPIT_ADMIN', 1);

// set default url rewrite setting
if (!isset($_SERVER['COCKPIT_URL_REWRITE'])) {
    $_SERVER['COCKPIT_URL_REWRITE'] = 'Off';
}

// set default timezone
date_default_timezone_set('UTC');

// handle php webserver
if (PHP_SAPI == 'cli-server' && is_file(__DIR__.parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

// Amazon Web Services lib (for S3 data storage)
require_once __DIR__ . '/vendor/aws.phar';
use Aws\Common\Aws;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
$s3 = S3Client::factory(array(
    'key'    => 'AKIAISQJ47JQQ2QOGBKA',
    'secret' => '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',
    'region' => 'us-east-1',
));
$s3->registerStreamWrapper();

// bootstrap cockpit
require(__DIR__.'/bootstrap.php');

// handle error pages
$cockpit->on("after", function() {

    switch ($this->response->status) {
        case 500:

            if ($this['debug']) {

                if ($this->req_is('ajax')) {
                    $this->response->body = json_encode(['error' => json_decode($this->response->body, true)]);
                } else {
                    $this->response->body = $this->render("cockpit:views/errors/500-debug.php", ['error' => json_decode($this->response->body, true)]);
                }

            } else {

                if ($this->req_is('ajax')) {
                    $this->response->body = '{"error": "500", "message": "system error"}';
                } else {
                    $this->response->body = $this->view("cockpit:views/errors/500.php");
                }
            }

            break;

        case 404:

            if ($this->req_is('ajax')) {
                $this->response->body = '{"error": "404", "message":"File not found"}';
            } else {
                $this->response->body = $this->view("cockpit:views/errors/404.php");
            }
            break;
    }
});

// run backend
$cockpit->set('route', COCKPIT_ADMIN_ROUTE)->trigger("admin.init")->run();
//echo '<pre>';
//print_r($cockpit);
//echo '</pre>';