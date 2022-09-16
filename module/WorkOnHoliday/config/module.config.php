<?php
namespace WorkOnHoliday;

use Application\Controller\ControllerFactory;
use WorkOnHoliday\Controller\WorkOnHolidayStatus;
use WorkOnHoliday\Controller\WorkOnHolidayApply;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'workOnHolidayStatus' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/workOnHoliday/status[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => WorkOnHolidayStatus::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'workOnHolidayApply' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/workOnHoliday/apply[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => WorkOnHolidayApply::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ],
    ],
    'navigation' => [
        'workOnHolidayStatus' => [
                [
                'label' => "Work on Holiday Request",
                'route' => "workOnHolidayStatus"
            ],
                [
                'label' => "Work on Holiday Request",
                'route' => "workOnHolidayStatus",
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'workOnHolidayStatus',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'workOnHolidayStatus',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Detail',
                        'route' => 'workOnHolidayStatus',
                        'action' => 'view',
                    ],
                ],
            ],
        ],
        'workOnHolidayApply' => [
                [
                'label' => "Work on Holiday Request",
                'route' => "workOnHolidayApply"
            ],
                [
                'label' => "Work on Holiday Request",
                'route' => "workOnHolidayApply",
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'workOnHolidayApply',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'workOnHolidayApply',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Edit',
                        'route' => 'workOnHolidayApply',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\WorkOnHolidayStatus::class => ControllerFactory::class,
            Controller\WorkOnHolidayApply::class => ControllerFactory::class
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
