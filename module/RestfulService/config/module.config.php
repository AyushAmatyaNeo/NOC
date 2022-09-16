<?php
namespace RestfulService;

use Application\Controller\ControllerFactory;
use Zend\Router\Http\Literal;

return [
    'controllers' => [
        'factories' => [
            Controller\RestfulService::class => ControllerFactory::class,
        ],

    ],


    'router' => [
        'routes' => [
            'restful' => [
                'type'    => Literal::class,
                'options' => [
                    'route' => '/restful',
                    'defaults' => [
                        'controller' => Controller\RestfulService::class,
                        'action'=>'index'
                    ],
                ],
            ],
        ],
    ],

  
    'view_manager' => [
        'template_path_stack' => [
             __DIR__ . '/../view',
        ]
    ],
];



