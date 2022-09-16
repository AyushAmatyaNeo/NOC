<?php

namespace Application\Factory;

interface ConfigInterface {

    public function getApplicationConfig(string $key = null): array;
}
