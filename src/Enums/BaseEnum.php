<?php

namespace Hyperzod\DoordashSdkPhp\Enums;

class BaseEnum
{
    public function getConstants()
    {
        $reflectionClass = new \ReflectionClass($this);
        return $reflectionClass->getConstants();
    }
}
