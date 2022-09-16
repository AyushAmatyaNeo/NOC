<?php

namespace Application\Factory;

class ApplicationConfig implements ConfigInterface {

    private $config = null;

    public function setApplicationConfig(array $config) {
        $this->config = $config;
    }

    public function getApplicationConfig(string $key = null): array {
        if ($key != null) {
            return $this->config[$key];
        }

        return $this->config;
    }

}
