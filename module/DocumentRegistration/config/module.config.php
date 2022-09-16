<?php
namespace DocumentRegistration;

use Application\Controller\ControllerFactory;
use Zend\Router\Http\Segment;
use DocumentRegistration\Controller\Test;
use DocumentRegistration\Controller\IncomingController;
use DocumentRegistration\Controller\AddDocument;

return [
    'router' => [
        'routes' => [
            'incoming-document' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/document-registration/incoming-document[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => IncomingController::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IncomingController::class => ControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
