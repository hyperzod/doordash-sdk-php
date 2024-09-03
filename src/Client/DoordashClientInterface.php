<?php

namespace Hyperzod\DoordashSdkPhp\Client;

/**
 * Interface for a Doordash client.
 */
interface DoordashClientInterface extends BaseDoordashClientInterface
{
   /**
    * Sends a request to Doordash's API.
    *
    * @param string $method the HTTP method
    * @param string $path the path of the request
    * @param array $params the parameters of the request
    */
   public function request($method, $path, $params);
}
