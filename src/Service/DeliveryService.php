<?php

namespace Hyperzod\DoordashSdkPhp\Service;

use Hyperzod\DoordashSdkPhp\Enums\HttpMethodEnum;

class DeliveryService extends AbstractService
{
   /**
    * Create a job on Doordash
    *
    * @param array $params
    *
    * @throws \Hyperzod\DoordashSdkPhp\Exception\ApiErrorException if the request fails
    *
    */
   public function create(array $params)
   {
      return $this->request(HttpMethodEnum::POST, '/deliveries', $params);
   }

   /**
    * Get a job on Doordash
    *
    * @param array $params
    *
    * @throws \Hyperzod\DoordashSdkPhp\Exception\ApiErrorException if the request fails
    *
    */

   public function get(array $params)
   {
      return $this->request(HttpMethodEnum::GET, '/deliveries/' . $params['order_id'], $params);
   }
}
