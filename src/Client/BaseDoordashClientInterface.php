<?php

namespace Hyperzod\DoordashSdkPhp\Client;

/**
 * Interface for a Doordash client.
 */
interface BaseDoordashClientInterface
{
   /**
    * Gets the developer id used by the client to send requests.
    *
    * @return null|string the developer id used by the client to send requests
    */
   public function getDeveloperId();

   /**
    * Gets the key id used by the client to send requests.
    *
    * @return null|string the key id used by the client to send requests
    */
   public function getKeyId();

   /**
    * Gets the signing secret used by the client to send requests.
    *
    * @return null|string the signing secret used by the client to send requests
    */
   public function getSigningSecret();

   /**
    * Gets the base URL for Doordash's API.
    *
    * @return string the base URL for Doordash's API
    */
   public function getApiBase();
}
