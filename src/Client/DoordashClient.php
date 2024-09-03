<?php

namespace Hyperzod\DoordashSdkPhp\Client;

use Hyperzod\DoordashSdkPhp\Service\CoreServiceFactory;

class DoordashClient extends BaseDoordashClient
{
    /**
     * @var CoreServiceFactory
     */
    private $coreServiceFactory;

    public function __get($name)
    {
        if (null === $this->coreServiceFactory) {
            $this->coreServiceFactory = new CoreServiceFactory($this);
        }

        return $this->coreServiceFactory->__get($name);
    }
}
