<?php

namespace KioskApi;

use Application\Controller\ControllerFactory;
use KioskApi\Controller\Loanlist;
use KioskApi\Controller\Authentication;
use KioskApi\Controller\Paysheet;
use KioskApi\Controller\LoanDetail;
use KioskApi\Controller\LeaveBalance;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'api-loanList' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/kiosk/api/loanlist[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Loanlist::class,
                        'action' => 'status'
                    ],
                ],
            ],
            'api-authentication' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/kiosk/api/authentication[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Authentication::class,
                        'action' => 'status'
                    ],
                ],
            ],
            'api-paysheet' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/kiosk/api/paysheet[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Paysheet::class,
                        'action' => 'status'
                    ],
                ],
            ],
            'api-loandetail' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/kiosk/api/loandetail[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => LoanDetail::class,
                        'action' => 'status'
                    ],
                ],
            ],
            'api-leavebalance' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/kiosk/api/leavebalance[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => LeaveBalance::class,
                        'action' => 'status'
                    ],
                ],
            ],
        ]
    ],
    'controllers' => [
        'factories' => [
            Loanlist::class => ControllerFactory::class,
            Authentication::class => ControllerFactory::class,
            Paysheet::class => ControllerFactory::class,
            LoanDetail::class => ControllerFactory::class,
            LeaveBalance::class => ControllerFactory::class,
        ],
    ],
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
];
