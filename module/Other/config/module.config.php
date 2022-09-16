<?php

namespace Other;

use Application\Controller\ControllerFactory;
use Other\Controller\AccidentAndDeath;
use Other\Controller\AllowanceAssign;
use Other\Controller\Bonus;
use Other\Controller\GradeChange;
use Other\Controller\LifeInsurance;
use Other\Controller\PaymentSuspended;
use Other\Controller\RetirementGratuity;
use Other\Controller\WorkforceManagement;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'accident-and-death' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/accident-death[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => AccidentAndDeath::class,
                        'action' => 'add'
                    ],
                ],
            ],
            'retirement-gratuity' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/retirement-gratuity[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => RetirementGratuity::class,
                        'action' => 'calculate'
                    ],
                ],
            ],
            'grade-change' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/grade-change[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => GradeChange::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'bonus' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/bonus[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Bonus::class,
                        'action' => 'calculation'
                    ],
                ],
            ],
            'workforce-management' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/workforce-management[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => WorkforceManagement::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'life-insurance' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/life-insurance[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => LifeInsurance::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'payment-suspended' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/payment-suspended[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => PaymentSuspended::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'allowance-assign' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/allowance/assign[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => AllowanceAssign::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ]
    ],
    'controllers' => [
        'factories' => [
            AccidentAndDeath::class => ControllerFactory::class,
            RetirementGratuity::class => ControllerFactory::class,
            GradeChange::class => ControllerFactory::class,
            Bonus::class => ControllerFactory::class,
            WorkforceManagement::class => ControllerFactory::class,
            LifeInsurance::class => ControllerFactory::class,
            PaymentSuspended::class => ControllerFactory::class,
            AllowanceAssign::class => ControllerFactory::class,
        ],
    ],
    'navigation' => [
        'accident-and-death' => [
                [
                'label' => 'Accident-Death',
                'route' => 'accident-and-death',
            ],
                [
                'label' => 'Accident-Death',
                'route' => 'accident-and-death',
                'pages' => [
                        [
                        'label' => 'Add',
                        'route' => 'accident-and-death',
                        'action' => 'add',
                    ],
                ]
            ]
        ],
        'retirement-gratuity' => [
                [
                'label' => 'Retirement Gratuity',
                'route' => 'retirement-gratuity',
            ],
                [
                'label' => 'Retirement Gratuity',
                'route' => 'retirement-gratuity',
                'pages' => [
                        [
                        'label' => 'Calculate',
                        'route' => 'retirement-gratuity',
                        'action' => 'calculate',
                    ],
                ]
            ]
        ],
        'grade-change' => [
                [
                'label' => 'Grade Change',
                'route' => 'grade-change',
            ],
                [
                'label' => 'Grade Change',
                'route' => 'grade-change',
                'pages' => [
                        [
                        'label' => 'list',
                        'route' => 'grade-change',
                        'action' => 'index',
                    ],
                ]
            ]
        ],
        'bonus' => [
                [
                'label' => 'Bonus',
                'route' => 'bonus',
            ],
                [
                'label' => 'Bonus',
                'route' => 'bonus',
                'pages' => [
                        [
                        'label' => 'calculation',
                        'route' => 'bonus',
                        'action' => 'calculation',
                    ],
                ]
            ]
        ],
        'workforce-management' => [
                [
                'label' => 'Workforce Management',
                'route' => 'workforce-management',
            ],
                [
                'label' => 'Workforce Management',
                'route' => 'workforce-management',
                'pages' => [
                        [
                        'label' => 'list',
                        'route' => 'workforce-management',
                        'action' => 'index',
                    ],
                ]
            ]
        ],
        'life-insurance' => [
                [
                'label' => 'Life Insurance',
                'route' => 'life-insurance',
            ],
                [
                'label' => 'Life Insurance',
                'route' => 'life-insurance',
                'pages' => [
                        [
                        'label' => 'list',
                        'route' => 'life-insurance',
                        'action' => 'index',
                    ],
                ]
            ]
        ],
        'payment-suspended' => [
                [
                'label' => 'Payment Suspended',
                'route' => 'payment-suspended',
            ],
                [
                'label' => 'Payment Suspended',
                'route' => 'payment-suspended',
                'pages' => [
                        [
                        'label' => 'Add',
                        'route' => 'payment-suspended',
                        'action' => 'add',
                    ],
                ]
            ]
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
