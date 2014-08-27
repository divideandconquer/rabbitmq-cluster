<?php

namespace PhpExample\Connection;

use Celery;

class CeleryFactory extends BaseFactory
{
  public function getInstance()
  {
    //make sure library is loaded.
    if (!class_exists('Celery')) {
      echo "\Celery Library not found.  Please run `composer install` from the rabbit-cluster root directory.\n\n";
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
    $c = new Celery($this->getParameter(static::PARAMETER_HOST), $this->getParameter(static::PARAMETER_USER), $this->getParameter(static::PARAMETER_PASSWORD), '/', 'celery', 'celery', 5671, false, false, $ssl_options);
    return $c;
  }

} 