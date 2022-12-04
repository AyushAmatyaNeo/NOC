<?php

namespace JobResponsibilityManagement;

use Zend\Router\Http\Segment;
use Application\Controller\ControllerFactory;

return [
    'router' => [
        'routes' => [
            'jobResponsibility' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/job-responsibility-setup[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\JobResponsibilitySetup::class,
                        'action' => 'index'
                    ]
                ]
            ],
            'jobResponsibilityAssign' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/job-responsibility-assign[/:action[/:eid[/:id]]]',
                    'defaults' => [
                        'controller' => Controller\JobResponsibilityAssign::class,
                        'action' => 'index'
                    ]
                ]
            ],
        ]
    ],
    'navigation' => [
        'jobResponsibility' => [
            [
                'label' => 'Job Responsibility',
                'route' => 'jobResponsibility',
            ],
            [
                'label' => 'Job Responsibility',
                'route' => 'jobResponsibility',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'jobResponsibility',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'jobResponsibility',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'jobResponsibility',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\JobResponsibilitySetup::class => ControllerFactory::class,
            Controller\JobResponsibilityAssign::class => ControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];


