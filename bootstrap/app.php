<?php

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

require 'config.php';
require 'database.php';
require 'router.php';

require "../vendor/larapack/dd/src/helper.php";

return $router;
