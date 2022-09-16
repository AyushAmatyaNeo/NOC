<?php

namespace AutoNotification;

use Application\Controller\ControllerFactory;
use AutoNotification\Controller\AutoNotificationController;

return [
    'console' => [
        'router' => [
            'routes' => [
                'auto-notification' => [
                    'options' => [
                        'route' => 'notification [<action>]',
                        'constants' => [
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            'id' => '[0-9]+',
                        ],
                        'defaults' => [
                            'controller' => AutoNotificationController::class,
                            'action' => 'index'
                        ]
                    ],
                ],
            ]
        ],
    ],
    'controllers' => [
        'factories' => [
            AutoNotificationController::class => ControllerFactory::class
        ],
    ],
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
];
