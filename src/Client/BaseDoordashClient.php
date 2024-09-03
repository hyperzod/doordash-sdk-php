<?php

namespace Hyperzod\DoordashSdkPhp\Client;

use Exception;
use GuzzleHttp\Client;
use Hyperzod\DoordashSdkPhp\Exception\InvalidArgumentException;

class BaseDoordashClient implements DoordashClientInterface
{

   /** @var array<string, mixed> */
   private $config;

   /**
    * Initializes a new instance of the {@link BaseDoordashClient} class.
    *
    * The constructor takes two arguments.
    * @param string $username the Username for Doordash's API
    * @param string $password the Password for Doordash's API
    * @param string $api_base the base URL for Doordash's API
    */

   public function __construct($username, $password, $api_base)
   {
      $config = $this->validateConfig(array(
         "username" => $username,
         "password" => $password,
         "api_base" => $api_base
      ));

      $this->config = $config;
   }

   /**
    * Gets the username used by the client to send requests.
    *
    * @return null|string the username used by the client to send requests
    */
   public function getUsername()
   {
      return $this->config['username'];
   }

   /**
    * Gets the password used by the client to send requests.
    *
    * @return null|string the password used by the client to send requests
    */

   public function getPassword()
   {
      return $this->config['password'];
   }

   /**
    * Gets the base URL for Doordash's API.
    *
    * @return string the base URL for Doordash's API
    */
   public function getApiBase()
   {
      return $this->config['api_base'];
   }

   // Get access token through login api
   public function getAccessToken()
   {
      $headers['content-type'] = 'application/json';

      $client = new Client([
         'headers' => $headers,
      ]);

      $api = $this->getApiBase() . '/login';

      $response = $client->request('POST', $api, [
         'json' => [
            'username' => $this->getUsername(),
            'password' => $this->getPassword()
         ]
      ]);

      $response = json_decode($response->getBody(), true);

      return $response['data']['token'];
   }

   public function request($method, $path, $params)
   {
      $headers['content-type'] = 'application/json';
      $headers['Authorization'] = $params['access_token'];
      // unset token from params
      unset($params['access_token']);

      $client = new Client([
         'headers' => $headers,
      ]);

      $api = $this->getApiBase() . $path;

      if ($method == 'GET') {
         $response = $client->request($method, $api);
      } else {
         $response = $client->request($method, $api, [
            'json' => $params
         ]);
      }

      return $this->validateResponse($response);
   }

   /**
    * @param array<string, mixed> $config
    *
    * @throws InvalidArgumentException
    */
   private function validateConfig($config)
   {
      // username
      if (!isset($config['username'])) {
         throw new InvalidArgumentException('username field is required');
      }

      if (!is_string($config['username'])) {
         throw new InvalidArgumentException('username must be a string');
      }

      if ($config['username'] === '') {
         throw new InvalidArgumentException('username cannot be an empty string');
      }

      if (preg_match('/\s/', $config['username'])) {
         throw new InvalidArgumentException('username cannot contain whitespace');
      }

      // password
      if (!isset($config['password'])) {
         throw new InvalidArgumentException('password field is required');
      }

      if (!is_string($config['password'])) {
         throw new InvalidArgumentException('password must be a string');
      }

      if ($config['password'] === '') {
         throw new InvalidArgumentException('password cannot be an empty string');
      }

      if (!isset($config['api_base'])) {
         throw new InvalidArgumentException('api_base field is required');
      }

      if (!is_string($config['api_base'])) {
         throw new InvalidArgumentException('api_base must be a string');
      }

      if ($config['api_base'] === '') {
         throw new InvalidArgumentException('api_base cannot be an empty string');
      }

      return [
         "username" => $config['username'],
         "password" => $config['password'],
         "api_base" => $config['api_base'],
      ];
   }

   private function validateResponse($response)
   {
      $status_code = $response->getStatusCode();

      if ($status_code >= 200 && $status_code < 300) {
         $response = json_decode($response->getBody(), true);
         return $response;
      } else {
         $response = json_decode($response->getBody(), true);

         if (isset($response["error"]) && isset($response["error"]["code"]) && $response["error"]["code"] == 401) {
            return $response;
         }

         if (isset($response["error"])) {
            throw new Exception($response["error"]["message"]);
         }
         throw new Exception("Errors node not set in server response");
      }
   }
}
