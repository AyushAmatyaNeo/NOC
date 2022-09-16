<?php

namespace Gratuity;

use Zend\Router\Http\Segment;
use Application\Controller\ControllerFactory;
//use LeaveManagement\Controller\LeaveEncashment;

return [
    'router' => [
        'routes' => [
            'gratuity' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/gratuity[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\Gratuity::class,
                        'action' => 'index'
                    ]
                ]
            ],
          
        ]
    ],
    'navigation' => [
        'gratuity' => [
            [
                'label' => 'Gratuity',
                'route' => 'gratuity',
            ],
            [
                'label' => 'Gratuity',
                'route' => 'gratuity',
                'pageleaverequests' => [
                    [
                        'label' => 'List',
                        'route' => 'gratuity',
                        'action' => 'index',
                    ]
                ]
            ]
        ],
       
       
    ],
    'controllers' => [
        'factories' => [
            Controller\Gratuity::class => ControllerFactory::class,

        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];


