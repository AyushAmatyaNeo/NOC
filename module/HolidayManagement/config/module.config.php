<?php

namespace HolidayManagement;

use Zend\Router\Http\Segment;
use Application\Controller\ControllerFactory;

return [
    'router' => [
        'routes' => [
            'holidaysetup' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/holiday[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\HolidaySetup::class,
                        'action' => 'list'
                    ]
                ]
            ],
            'holiday-assign' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/holiday-assign[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\HolidayAssign::class,
                        'action' => 'index'
                    ]
                ]
            ],
        ]
    ],
    'navigation' => [
        'holidaysetup' => [
                [
                'label' => 'Holiday',
                'route' => 'holidaysetup',
            ],
                [
                'label' => 'Holiday',
                'route' => 'holidaysetup',
                'pages' => [
                        [
                        'label' => 'Detail',
                        'route' => 'holidaysetup',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'holidaysetup',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'List',
                        'route' => 'holidaysetup',
                        'action' => 'list',
                    ],
                        [
                        'label' => 'Edit',
                        'route' => 'holidaysetup',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
        'holiday-assign' => [
                [
                'label' => 'Holiday',
                'route' => 'holiday-assign',
            ],
                [
                'label' => 'Holiday',
                'route' => 'holiday-assign',
                'pages' => [
                        [
                        'label' => 'Assign',
                        'route' => 'holiday-assign',
                        'action' => 'index',
                    ],
                ]
            ]
        ]
    ],
    'controllers' => [
        'factories' => [
            Controller\HolidaySetup::class => ControllerFactory::class,
            Controller\HolidayAssign::class => ControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];


