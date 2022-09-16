<?php

namespace Cron;

use Application\Controller\ControllerFactory;
use Cron\Controller\Cron;
use Cron\Controller\BottlersCron;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'cron' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/cron[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Cron::class,
                        'action' => 'index'
                    ]
                ],
            ],
        ]
    ],
    'controllers' => [
        'factories' => [
            Cron::class => ControllerFactory::class,
        ],
    ],
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
];
