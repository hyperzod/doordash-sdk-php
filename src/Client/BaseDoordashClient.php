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
    * @param string $developer_id the Developer ID for Doordash's API
    * @param string $key_id the Key ID for Doordash's API
    * @param string $signing_secret the Signing Secret for Doordash's API
    * @param string $api_base the base URL for Doordash's API
    */

   public function __construct($developer_id, $key_id, $signing_secret, $api_base)
   {
      $config = $this->validateConfig(array(
         "developer_id" => $developer_id,
         "key_id" => $key_id,
         "signing_secret" => $signing_secret,
         "api_base" => $api_base
      ));

      $this->config = $config;
   }

   /**
    * Gets the developer id used by the client to send requests.
    *
    * @return null|string the Developer ID used by the client to send requests
    */
   public function getDeveloperId()
   {
      return $this->config['developer_id'];
   }

   /**
    * Gets the key id used by the client to send requests.
    *
    * @return null|string the key id used by the client to send requests
    */

   public function getKeyId()
   {
      return $this->config['key_id'];
   }

   /**
    * Gets the signing secret used by the client to send requests.
    *
    * @return null|string the signing secret id used by the client to send requests
    */

   public function getSigningSecret()
   {
      return $this->config['signing_secret'];
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

   public function getAccessToken()
   {
      $header = json_encode([
         'alg' => 'HS256',
         'typ' => 'JWT',
         'dd-ver' => 'DD-JWT-V1'
      ]);

      $payload = json_encode([
         'aud' => 'doordash',
         'iss' => $this->getDeveloperId(),
         'kid' => $this->getKeyId(),
         'exp' => time() + 300,
         'iat' => time()
      ]);

      $base64UrlHeader = $this->base64UrlEncode($header);
      $base64UrlPayload = $this->base64UrlEncode($payload);

      $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->base64UrlDecode($this->getSigningSecret()), true);
      $base64UrlSignature = $this->base64UrlEncode($signature);

      $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

      return $jwt;
   }

   public function request($method, $path, $params)
   {
      $client = new Client([
         'headers' => [
            'accept' => 'application/json',
            'content-type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->getAccessToken()
         ]
      ]);

      $api = $this->getApiBase() . $path;

      $response = $client->request($method, $api, [
         'http_errors' => true,
         'body' => json_encode($params)
      ]);

      return $this->validateResponse($response);
   }

   /**
    * @param array<string, mixed> $config
    *
    * @throws InvalidArgumentException
    */
   private function validateConfig($config)
   {
      // developer_id
      if (!isset($config['developer_id'])) {
         throw new InvalidArgumentException('developer_id field is required');
      }

      if (!is_string($config['developer_id'])) {
         throw new InvalidArgumentException('developer_id must be a string');
      }

      if ($config['developer_id'] === '') {
         throw new InvalidArgumentException('developer_id cannot be an empty string');
      }

      if (preg_match('/\s/', $config['developer_id'])) {
         throw new InvalidArgumentException('developer_id cannot contain whitespace');
      }

      // key_id
      if (!isset($config['key_id'])) {
         throw new InvalidArgumentException('key_id field is required');
      }

      if (!is_string($config['key_id'])) {
         throw new InvalidArgumentException('key_id must be a string');
      }

      if ($config['key_id'] === '') {
         throw new InvalidArgumentException('key_id cannot be an empty string');
      }

      // signing_secret
      if (!isset($config['signing_secret'])) {
         throw new InvalidArgumentException('signing_secret field is required');
      }

      if (!is_string($config['signing_secret'])) {
         throw new InvalidArgumentException('signing_secret must be a string');
      }

      if ($config['signing_secret'] === '') {
         throw new InvalidArgumentException('signing_secret cannot be an empty string');
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
         "developer_id" => $config['developer_id'],
         "key_id" => $config['key_id'],
         "signing_secret" => $config['signing_secret'],
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

   private function base64UrlEncode(string $data): string
   {
      $base64Url = strtr(base64_encode($data), '+/', '-_');

      return rtrim($base64Url, '=');
   }

   private function base64UrlDecode(string $base64Url): string
   {
      return base64_decode(strtr($base64Url, '-_', '+/'));
   }
}
