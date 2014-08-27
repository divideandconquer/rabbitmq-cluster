<?php

namespace PhpExample\Connection;

use PhpAmqpLib\Connection\AMQPSSLConnection;

class RabbitFactory extends BaseFactory
{
  public function getInstance()
  {
    //make sure library is loaded.
    if (!class_exists('PhpAmqpLib\Connection\AMQPSSLConnection')) {
      echo "\nAMQP Library not found.  Please run `composer install` from the rabbit-cluster root directory.\n\n";
      exit;
    }

    //setup SSL options
    $ssl_options = array(
      'cafile' => __DIR__ . '/../../../../ssl/testca/cacert.pem',
      'verify_peer' => true,
      'local_cert' => __DIR__ . '/../../../../ssl/client/certkey.pem',
      'CN_match' => $this->getParameter(static::PARAMETER_SSL_COMMON_NAME)
    );

    //create connections
    $connection = new AMQPSSLConnection($this->getParameter(static::PARAMETER_HOST), 5671, $this->getParameter(static::PARAMETER_USER), $this->getParameter(static::PARAMETER_PASSWORD), "/", $ssl_options);

    return $connection;
  }

} 