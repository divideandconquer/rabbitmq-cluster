<?php

require_once __DIR__ . '/vendor/autoload.php';

use \PhpExample\Connection\CeleryFactory;

$factory = new CeleryFactory($argv);
$c = $factory->getInstance();

echo "Sending Celery Task to Add 2 + 2 \n";
$ret = $c->PostTask('tasks.add', array(2, 2));

$ret->get(60); // wait for result - this is optional.
if ($ret->isSuccess()) {
  echo $ret->getResult();
} else {
  echo "ERROR\n";
  echo $ret->getTraceback();
}
echo "\n";