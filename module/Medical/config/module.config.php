<?php

namespace Medical;

use Application\Controller\ControllerFactory;
use Medical\Controller\MedicalEntry;
use Medical\Controller\MedicalReport;
use Medical\Controller\MedicalSettlement;
use Medical\Controller\MedicalVerify;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'medicalEntry' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/medical/entry[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => MedicalEntry::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'medicalVerify' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/medical/verify[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => MedicalVerify::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'medicalSettlement' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/medical/settlement[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => MedicalSettlement::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'medicalReport' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/medical/report[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => MedicalReport::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ],
    ],
    'navigation' => [
        'medicalEntry' => [
            [
                'label' => "Medical Entry",
                'route' => "medicalEntry"
            ],
            [
                'label' => "Medical Entry",
                'route' => "medicalEntry",
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'medicalEntry',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'medicalEntry',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'medicalEntry',
                        'action' => 'view',
                    ],
                ],
            ],
        ],
        'medicalVerify' => [
            [
                'label' => "Medical Verify",
                'route' => "medicalVerify"
            ],
            [
                'label' => "Medical Verify",
                'route' => "medicalVerify",
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'medicalVerify',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'medicalVerify',
                        'action' => 'view',
                    ],
                ],
            ],
        ],
        'medicalSettlement' => [
            [
                'label' => "Medical Settlement",
                'route' => "medicalSettlement"
            ],
            [
                'label' => "Medical Settlement",
                'route' => "medicalSettlement",
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'medicalSettlement',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'medicalSettlement',
                        'action' => 'view',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            MedicalEntry::class => ControllerFactory::class,
            MedicalVerify::class => ControllerFactory::class,
            MedicalSettlement::class => ControllerFactory::class,
            MedicalReport::class => ControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
