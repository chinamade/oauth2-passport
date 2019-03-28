<?php
/**
 * Created by Mopi.
 *
 * Date: 2019-01-07
 * Time: 10:41
 */


use LianYun\Passport\Passport;

/** @var Api $app */
$app = require_once __DIR__ . "/../bootstrap.php";

$app->getHttpKernel()->run();

