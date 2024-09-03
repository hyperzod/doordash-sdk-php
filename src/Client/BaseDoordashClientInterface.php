<?php

namespace Hyperzod\DoordashSdkPhp\Client;

/**
 * Interface for a Doordash client.
 */
interface BaseDoordashClientInterface
{
   /**
    * Gets the username used by the client to send requests.
    *
    * @return null|string the username used by the client to send requests
    */
   public function getUsername();

   /**
    * Gets the password used by the client to send requests.
    *
    * @return null|string the password used by the client to send requests
    */
   public function getPassword();

   /**
    * Gets the base URL for Doordash's API.
    *
    * @return string the base URL for Doordash's API
    */
   public function getApiBase();
}
