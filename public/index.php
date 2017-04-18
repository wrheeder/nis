<?php
/**
 * Created by IntelliJ IDEA.
 * User: WRheeder
 * Date: 25/01/2017
 * Time: 11:04 AM
 */

date_default_timezone_set('Africa/Johannesburg');
chdir('..');
require_once'vendor/autoload.php';
require_once 'lib/Frontend.php';
$api=new Frontend("NIS","CellC");
$api->main();