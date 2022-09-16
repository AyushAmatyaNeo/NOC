<?php
namespace Grade;

use Application\Controller\ControllerFactory;
use Grade\Controller\GradeController;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'grade' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/grade[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => GradeController::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\GradeController::class => ControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
