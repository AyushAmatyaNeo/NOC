<?php

namespace DartaChalani;

use Application\Controller\ControllerFactory;
use Zend\Router\Http\Segment;
use DartaChalani\Controller\DartaChalani;
use DartaChalani\Controller\OfficesController;
use DartaChalani\Controller\DepartmentUsersController;

return [
    'router' => [
        'routes' => [
            'dartachalani' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/darta-chalani[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => DartaChalani::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'offices' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/offices[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => OfficesController::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'departmentusers' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/departmentusers[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => DepartmentUsersController::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            DartaChalani::class => ControllerFactory::class,
            OfficesController::class => ControllerFactory::class,
            DepartmentUsersController::class => ControllerFactory::class

        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
