<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpExample\Connection\RabbitFactory;

$rabbit = new RabbitFactory($argv);

function handleMsg($channel)
{
  while (count($channel->callbacks)) {
    $channel->wait();
  }
}

echo "Starting consumer.  Make sure a producer is running so I have messages to read!\n";
$channel = false;
while (true) {
  try {
    if ($channel === false) {
      //connect/reconnect to the 'hello' channel
      $connection = $rabbit->getInstance();

      //get the default queue in the 'hello' channel
      $channel = $connection->channel();
      $channel->exchange_declare('hello', 'fanout', false, false, false);
      $queue_name = $channel->queue_declare("", false, false, true, false);
      $queue_name = array_shift($queue_name);
      $channel->queue_bind($queue_name, 'hello');

      //setup callback to handle messages
      $callback = function ($msg) {
        echo " Received Message: $msg->body\n";
      };
      $channel->basic_consume($queue_name, '', false, true, false, false, $callback);
    }
    //listen for messages
    handleMsg($channel);
  } catch (\Exception $e) {
    echo "Connection lost.  Reconnecting...\n";
    echo $e->getMessage() . "\n";
    $channel = false;
    sleep(1);
  }
}
$channel->close();
$connection->close();
