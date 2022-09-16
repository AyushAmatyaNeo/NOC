<?php

namespace Asset;

use Application\Controller\ControllerFactory;
use Asset\Controller\GroupController;
use Asset\Controller\IssueController;
use Asset\Controller\SetupController;
use Zend\Router\Http\Segment;

return[
    'router' => [
        'routes' => [
            'assetGroup' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/asset/group[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => GroupController::class,
                        'action' => 'index',
                    ]
                ],
            ],
            
            'assetSetup' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/asset/setup[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => SetupController::class,
                        'action' => 'index',
                    ]
                ],
            ],
            
            'assetIssue' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/asset/issue[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => IssueController::class,
                        'action' => 'index',
                    ]
                ],
            ],
            
            
        ],
    ],
    
    'navigation' => [
        'assetGroup'=>[
            [
                'label'=>'Asset Type',
                'route'=>'assetGroup',
            ],[
                'label' => 'Asset Type',
                'route' => 'assetGroup',
                'pages' => [
                        [
                        'label' => 'Asset Type List',
                        'route' => 'assetGroup',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'assetGroup',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Edit',
                        'route' => 'assetGroup',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
        
        'assetSetup' => [
            [
                'label'=>'Asset Setup',
                'route'=>'assetSetup',
                ],
            [
                'label' => 'Asset Setup',
                'route' => 'assetSetup',
                'pages' => [
                        [
                        'label' => 'Asset List',
                        'route' => 'assetSetup',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'assetSetup',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Edit',
                        'route' => 'assetSetup',
                        'action' => 'edit',
                    ],
                    [
                        'label' => 'View',
                        'route' => 'assetIssue',
                        'action' => 'view',
                    ],
                ],
            ],
            ],
        
        'assetIssue'=>[
            [
                'label'=>'Asset Issue',
                'route'=>'assetIssue',
            ],[
                'label' => 'Asset Issue',
                'route' => 'assetIssue',
                'pages' => [
                        [
                        'label' => 'Asset Issue Report',
                        'route' => 'assetIssue',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'assetIssue',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Edit',
                        'route' => 'assetIssue',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
        
        
    ],
    
    'controllers' => [
        'factories' => [
            GroupController::class => ControllerFactory::class,
            SetupController::class => ControllerFactory::class,
            IssueController::class => ControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];

