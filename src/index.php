<?php
require 'vendor/autoload.php';
require 'Application.php';
$conf = parse_ini_file(__DIR__.'/conf.ini', true);
Application::app()->start($conf);