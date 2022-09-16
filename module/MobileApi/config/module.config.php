<?php

namespace MobileApi;

use Application\Controller\ControllerFactory;
use MobileApi\Controller\Attendance;
use MobileApi\Controller\Authentication;
use MobileApi\Controller\Checkout;
use MobileApi\Controller\Dashboard;
use MobileApi\Controller\Employee;
use MobileApi\Controller\Holidaylist;
use MobileApi\Controller\Leave;
use MobileApi\Controller\Leavelist;
use MobileApi\Controller\Notification;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'api-auth' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/mobile/api/auth[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Authentication::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'api-leave' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/mobile/api/leave[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Leave::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'api-employee' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/mobile/api/employee[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Employee::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'api-notification' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/mobile/api/notification[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Notification::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'api-dashboard' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/mobile/api/dashboard[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Dashboard::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'api-attendance' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/mobile/api/attendance[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Attendance::class,
                        'action' => 'index'
                    ],
                ],
            ],
            
            'api-leavelist' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/mobile/api/leavelist[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Leavelist::class,
                        'action' => 'index'
                    ],
                ],
            ],
            
            'api-holidaylist' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/mobile/api/holidaylist[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Holidaylist::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'api-checkout' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/mobile/api/checkout[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Checkout::class,
                        'action' => 'index'
                    ],
                ],
            ],
            
            
            
        ]
    ],
    'controllers' => [
        'factories' => [
            Authentication::class => ControllerFactory::class,
            Leave::class => ControllerFactory::class,
            Employee::class => ControllerFactory::class,
            Notification::class => ControllerFactory::class,
            Dashboard::class => ControllerFactory::class,
            Attendance::class => ControllerFactory::class,
            Leavelist::class => ControllerFactory::class,
            Holidaylist::class=>ControllerFactory::class,
            Checkout::class=>ControllerFactory::class,
        ],
    ],
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
];
