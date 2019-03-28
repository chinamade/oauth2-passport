<?php
/**
 * Created by Mopi.
 *
 * Date: 2019-01-07
 * Time: 10:41
 */


use LianYun\Passport\PassportConfiguration;
use LianYun\Passport\Passport;

require_once __DIR__ . "/vendor/autoload.php";

define('PROJECT_DIR', __DIR__);
#error_reporting(0);
/** @var Passport $app */
$app = Passport::app();
$app->init(__DIR__ . "/config", new PassportConfiguration(), __DIR__ . "/cache/config");

return $app;

