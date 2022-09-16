<?php
namespace ServiceQuestion;

use Application\Controller\ControllerFactory;
use ServiceQuestion\Controller\EmpServiceQuestion;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'empServiceQuestion' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/serviceQuestion/empServiceQuestion[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => EmpServiceQuestion::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ],
    ],
    'navigation' => [
        'empServiceQuestion' => [
                [
                'label' => "Employee Service Question",
                'route' => "empServiceQuestion"
            ],
                [
                'label' => "Employee Service Question",
                'route' => "empServiceQuestion",
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'empServiceQuestion',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'empServiceQuestion',
                        'action' => 'add',
                    ],
                     [
                        'label' => 'Edit',
                        'route' => 'empServiceQuestion',
                        'action' => 'edit',
                    ],
                        [
                        'label' => 'Detail',
                        'route' => 'empServiceQuestion',
                        'action' => 'view',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\EmpServiceQuestion::class => ControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
