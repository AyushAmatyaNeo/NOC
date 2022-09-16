<?php
namespace Application\Controller;

use Application\Factory\ApplicationConfig;
use Application\Factory\ConfigInterface;
use Application\Factory\HrLogger;
use Application\Repository\HrisRepository;
use Interop\Container\ContainerInterface;
use ReflectionClass;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Log\Logger;
use Zend\ServiceManager\Factory\FactoryInterface;

class ControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $getDependency = function(string $className, ContainerInterface $container) {
            $output = null;
            switch ($className) {
                case AdapterInterface::class:
                    $output = $container->get(AdapterInterface::class);
                    break;
                case Logger::class:
                    $output = $container->get(HrLogger::class);
                    break;
                case ContainerInterface::class:
                    $output = $container;
                    break;
                case ConfigInterface::class:
                    $appConfig = new ApplicationConfig();
                    $appConfig->setApplicationConfig($container->get('config'));
                    $output = $appConfig;
                    break;
                case StorageInterface::class:
                    $auth = new AuthenticationService();
                    $storage = $auth->getStorage();
                    $output = $storage;
                    break;
                default:
                    $reflClass = new ReflectionClass($className);
                    $object = $reflClass->newInstanceWithoutConstructor();
                    if ($object instanceof HrisRepository) {
                        $output = new $className($container->get(AdapterInterface::class));
                    } else {
                        $output = null;
                    }
                    break;
            }
            return $output;
        };
        $refl = new ReflectionClass($requestedName);
        $constructor = $refl->getConstructor();
        if ($constructor == null) {
            return $refl->newInstanceArgs();
        }
        $params = $constructor->getParameters();
        if (sizeof($params) == 0) {
            return $refl->newInstanceArgs();
        }
        $initParams = [];
        foreach ($params as $key => $param) {
            $initParams[$key] = $getDependency($param->getClass()->name, $container);
        }
        return $refl->newInstanceArgs($initParams);
    }
}
