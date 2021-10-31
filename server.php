<?php

use Base\App;

require 'autoload.php';

$app = App::singleton();

$app->prepare()->run();