<?php

namespace Insurance;

use Zend\Router\Http\Segment;
use Application\Controller\ControllerFactory;

return [
    'router' => [
        'routes' => [
            'insurance-emp' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/insurance[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\InsuranceController::class,
                        'action' => 'index'
                    ]
                ]
            ],
            'insurance-emp-dtl' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/insurance[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\InsuranceController::class,
                        'action' => 'employeeDtl'
                    ]
                ]
            ]
        ]
    ],
    'controllers' => [
        'factories' => [
            Controller\InsuranceController::class => ControllerFactory::class
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
