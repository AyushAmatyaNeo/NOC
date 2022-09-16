<?php
namespace Workforce;

use Application\Controller\ControllerFactory;
use Zend\Router\Http\Segment;
use Workforce\Controller\WorkforceController;

return [
    'router' => [
        'routes' => [
            'workforce' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/workforce[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => WorkforceController::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\WorkforceController::class => ControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
