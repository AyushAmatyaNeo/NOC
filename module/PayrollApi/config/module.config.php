<?php

namespace PayrollApi;

use Application\Controller\ControllerFactory;
use PayrollApi\Controller\PayrollApi;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'payroll-api' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/payroll/api[/:action[/:id]]',
                    'defaults' => [
                        'controller' => PayrollApi::class,
                        'action' => 'index'
                    ]
                ]
            ],
        ]
    ],
    'controllers' => [
        'factories' => [
            PayrollApi::class => ControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];


