<?php

error_reporting(E_ALL ^ E_NOTICE);
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Message\AMQPMessage;
use PhpExample\Connection\RabbitFactory;

$rabbit = new RabbitFactory($argv);

echo "Starting producer. Make sure a consumer is running so someone can read my messages!\n";
$channel = false;
$count = 0;
while (true) {
  try {
    if ($channel === false) {
      $connection = $rabbit->getInstance();
      $channel = $connection->channel();
      $channel->exchange_declare('hello', 'fanout', false, false, false);

      echo "Connection re-established. \n";
    }
    $msg = new AMQPMessage('hello - ' . $count);
    $channel->basic_publish($msg, 'hello');
    echo "Sending message: hello - $count \n";
    $count++;
  } catch (\Exception $e) {
    echo "Connection lost.  Re-establishing connection...\n";
    $channel = false;
  }
  sleep(1);
}
$channel->close();
$connection->close();
