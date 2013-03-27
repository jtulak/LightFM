<?php

// Uncomment this line if you must temporarily take down your site for maintenance.
// require '.maintenance.php';

define('DATA_ROOT',__DIR__);
define('FM_DATA_DIR','filemanager');
define('DATA_TEMP','temp');

define('DATA_TEMP_FULL',DATA_ROOT.'/'.DATA_TEMP);

// Let bootstrap create Dependency Injection container.
$container = require __DIR__ . '/../app/bootstrap.php';

// Run application.
$container->application->run();
