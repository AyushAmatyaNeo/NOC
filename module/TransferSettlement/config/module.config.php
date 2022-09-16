<?php
namespace TransferSettlement;

use Application\Controller\ControllerFactory;
use TransferSettlement\Controller\TransferSettlementStatus;
use TransferSettlement\Controller\TransferSettlementApply;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'transferSettlementStatus' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/transferSettlement/status[/:action[/:id][/:serialNumber]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'serialNumber' => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => TransferSettlementStatus::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'transferSettlementApply' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/transferSettlement/apply[/:action[/:id][/:serialNumber]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'serialNumber' => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => TransferSettlementApply::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ],
    ],
    'navigation' => [
        'transferSettlementStatus' => [
                [
                'label' => "Transfer Settlement Status",
                'route' => "transferSettlementStatus"
            ],
                [
                'label' => "Transfer Settlement Status",
                'route' => "transferSettlementStatus",
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'transferSettlementStatus',
                        'action' => 'index',
                        ],
                ],
            ],
        ],
        'transferSettlementApply' => [
                [
                'label' => "Transfer Settlement Apply",
                'route' => "transferSettlementApply"
            ],
                [
                'label' => "Transfer Settlement Apply",
                'route' => "transferSettlementApply",
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'transferSettlementApply',
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    
    'controllers' => [
        'factories' => [
            Controller\TransferSettlementStatus::class => ControllerFactory::class,
            Controller\TransferSettlementApply::class => ControllerFactory::class
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
