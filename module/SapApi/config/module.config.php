<?php

namespace SapApi;

use Application\Controller\ControllerFactory;
use Zend\Router\Http\Segment;
use SapApi\Controller\FinanceDataApiController;
use SapApi\Controller\EmployeeDataApiController;

return [
    'router' => [
        'routes' => [
            'sapapi-financedata' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/api/financedata[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => FinanceDataApiController::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'sapapi-employeedata' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/api/employeedata[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => EmployeeDataApiController::class,
                        'action' => 'index'
                    ],
                ],
            ]
        ]
    ],
    'controllers' => [
        'factories' => [
            FinanceDataApiController::class => ControllerFactory::class,
            EmployeeDataApiController::class => ControllerFactory::class
        ],
    ],
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
];
