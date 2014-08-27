<?php

namespace PhpExample\Connection;

/**
 * Class CommandLine
 * @package PhpExample
 * A helper class that wraps up some of the command line functionality used by the php examples
 */
abstract class BaseFactory
{

  const DEFAULT_HOST = 'proxy';
  const DEFAULT_USER = 'admin';
  const DEFAULT_PASSWORD = 'admin';
  const DEFAULT_SSL_COMMON_NAME = 'rabbit';

  const PARAMETER_HOST = 'h';
  const PARAMETER_USER = 'u';
  const PARAMETER_PASSWORD = 'p';
  const PARAMETER_SSL_COMMON_NAME = 'ssl-common-name';

  protected $params = array();

  /**
   * Constructs a CommandLine object from the command line variables provided.
   * @param $argv - The CLI $argv variable
   * @return CommandLine
   */
  public function __construct($argv)
  {
    //setup defaults
    $this->params[static::PARAMETER_HOST] = static::DEFAULT_HOST;
    $this->params[static::PARAMETER_USER] = static::DEFAULT_USER;
    $this->params[static::PARAMETER_PASSWORD] = static::DEFAULT_PASSWORD;
    $this->params[static::PARAMETER_SSL_COMMON_NAME] = static::DEFAULT_SSL_COMMON_NAME;

    //pull in arguments
    $short_opts = static::PARAMETER_HOST . ':' . static::PARAMETER_USER . ':' . static::PARAMETER_PASSWORD . ':';
    $long_opts = array();
    $long_opts[] = static::PARAMETER_SSL_COMMON_NAME . ':';
    $long_opts[] = 'help';
    $options = getopt($short_opts, $long_opts);

    //override defaults
    $this->params = array_merge($this->params, $options);

    if (isset($this->params['help'])) {
      echo "\n----------------- USAGE --------------\nphp " . $argv[0] . " [-h <host>] [-u <rabbit username>] [-p <rabbit password>] [--ssl-common-name <common name>]\n\n";
      exit;
    }
  }

  /**
   * Returns the parameters passed in as CLI arguments or their defaults
   * @param $key
   * @return mixed
   */
  public function getParameter($key)
  {
    return isset($this->params[$key]) ? $this->params[$key] : null;
  }

  /**
   * Retrieves a connection
   * @return mixed
   */
  abstract public function getInstance();

} 