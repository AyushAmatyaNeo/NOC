<?php
namespace WorkOnDayoff;

use Application\Controller\ControllerFactory;
use WorkOnDayoff\Controller\WorkOnDayoffStatus;
use WorkOnDayoff\Controller\WorkOnDayoffApply;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'workOnDayoffStatus' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/workOnDayoff/status[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => WorkOnDayoffStatus::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'workOnDayoffApply' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/workOnDayoff/apply[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => WorkOnDayoffApply::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ],
    ],
    'navigation' => [
        'workOnDayoffStatus' => [
                [
                'label' => "Work On Day-off Request",
                'route' => "workOnDayoffStatus"
            ],
                [
                'label' => "Work On Day-off Request",
                'route' => "workOnDayoffStatus",
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'workOnDayoffStatus',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'workOnDayoffStatus',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Detail',
                        'route' => 'workOnDayoffStatus',
                        'action' => 'view',
                    ],
                ],
            ],
        ],
        'workOnDayoffApply' => [
                [
                'label' => "Work on Day-off Request",
                'route' => "workOnDayoffApply"
            ],
                [
                'label' => "Work on Day-off Request",
                'route' => "workOnDayoffApply",
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'workOnDayoffApply',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'workOnDayoffApply',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Edit',
                        'route' => 'workOnDayoffApply',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\WorkOnDayoffStatus::class => ControllerFactory::class,
            Controller\WorkOnDayoffApply::class => ControllerFactory::class
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
