#! /usr/bin/env php
<?php
/**
 * Created by Mopi.
 *
 * Date: 2019-01-07
 * Time: 10:41
 */

use LianYun\Passport\Passport;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

/** @var Passport $app */
$app     = require_once __DIR__ . "/../bootstrap.php";
$console = $app->getConsoleApplication();
$console->addCommands(
    [

    ]
);
/** @noinspection PhpIncludeInspection */
$helperSet = require_once PROJECT_DIR . "/config/cli-config.php";
$console->setHelperSet($helperSet);
ConsoleRunner::addCommands($console);
$console->run();