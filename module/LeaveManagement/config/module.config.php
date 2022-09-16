<?php

namespace LeaveManagement;

use Zend\Router\Http\Segment;
use Application\Controller\ControllerFactory;
//use LeaveManagement\Controller\LeaveEncashment;

return [
    'router' => [
        'routes' => [
            'leavesetup' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/leave-setup[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\LeaveSetup::class,
                        'action' => 'index'
                    ]
                ]
            ],
            'substituteLeave' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/substitute-leave[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\SubstituteLeave::class,
                        'action' => 'index'
                    ]
                ]
            ],
            'leaveassign' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/leave-assign[/:action[/:eid[/:id]]]',
                    'defaults' => [
                        'controller' => Controller\leaveAssign::class,
                        'action' => 'assign'
                    ]
                ]
            ],
            'leaveapply' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/leave-apply[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\LeaveApply::class,
                        'action' => 'index'
                    ]
                ]
            ],
            'leavestatus' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/leave-status[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\LeaveStatus::class,
                        'action' => 'index'
                    ]
                ]
            ],
            'leavebalance' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/leave-balance[/:action]',
                    'defaults' => [
                        'controller' => Controller\LeaveBalance::class,
                        'action' => 'index'
                    ]
                ]
            ],
            'leaveSubBypass' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/leave-sub-man[/:action]',
                    'defaults' => [
                        'controller' => Controller\LeaveSubBypass::class,
                        'action' => 'index'
                    ]
                ]
            ],
            'leavecarryforward' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/leavemanagement/leavecarryforward[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\LeaveCarryForward::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'leavereportcard' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/leave-report-card[/:action]',
                    'defaults' => [
                        'controller' => Controller\LeaveReportCard::class,
                        'action' => 'index'
                    ]
                ]
            ],
            'leavededuction' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/leave-deduction[/:action]',
                    'defaults' => [
                        'controller' => Controller\LeaveDeduction::class,
                        'action' => 'index'
                    ]
                ]
            ],
            'LeaveEncashmenthr' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/leavemanagement/LeaveEncashmenthr[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\LeaveEncashmenthr::class,
                        'action' => 'index',
                    ]
                ],
            ],
        ]
    ],
    'navigation' => [
        'leavesetup' => [
            [
                'label' => 'Leave Setup',
                'route' => 'leavesetup',
            ],
            [
                'label' => 'Leave Setup',
                'route' => 'leavesetup',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'leavesetup',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'leavesetup',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'leavesetup',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
        'substituteLeave' => [
            [
                'label' => 'Substitute Leave',
                'route' => 'substituteLeave',
            ],
            [
                'label' => 'Substitute Leave',
                'route' => 'substituteLeave',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'substituteLeave',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'substituteLeave',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'substituteLeave',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
        'leaveassign' => [
            [
                'label' => 'Leave Assign',
                'route' => 'leaveassign',
            ],
            [
                'label' => 'Leave Assign',
                'route' => 'leaveassign',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'leaveassign',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'leaveassign',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Assign',
                        'route' => 'leaveassign',
                        'action' => 'assign',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'leaveassign',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
        'leaveapply' => [
            [
                'label' => 'Leave Apply',
                'route' => 'leaveapply',
            ],
            [
                'label' => 'Leave Apply',
                'route' => 'leaveapply',
                'pageleaverequests' => [
                    [
                        'label' => 'List',
                        'route' => 'leaveapply',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'leaveapply',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'leaveapply',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
        'leavestatus' => [
            [
                'label' => 'Leave Request Status',
                'route' => 'leavestatus',
            ],
            [
                'label' => 'Leave Request Status',
                'route' => 'leavestatus',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'leavestatus',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'leavestatus',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'leavestatus',
                        'action' => 'view',
                    ],
                ]
            ]
        ],
        'leavededuction' => [
            [
                'label' => 'Leave Deduction Status',
                'route' => 'leavededuction',
            ],
            [
                'label' => 'Leave Deduction Status',
                'route' => 'leavededuction',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'leavededuction',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'leavededuction',
                        'action' => 'add',
                    ],
//                    [
//                        'label' => 'Detail',
//                        'route' => 'leavededuction',
//                        'action' => 'view',
//                    ],
                ]
            ]
        ],
        'leavebalance' => [
            [
                'label' => 'Leave Balance',
                'route' => 'leavebalance',
            ],
            [
                'label' => 'Leave Balance',
                'route' => 'leavebalance',
                'pages' => [
                    [
                        'label' => 'Annual',
                        'route' => 'leavebalance',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Monthly',
                        'route' => 'leavebalance',
                        'action' => 'monthly',
                    ],
                    [
                        'label' => 'Leave Apply',
                        'route' => 'leavebalance',
                        'action' => 'apply',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'leavebalance',
                        'action' => 'view',
                    ],
                ]
            ]
        ],
        'LeaveEncashmenthr' => [
            [
                'label' => 'Leave Encashment',
                'route' => 'LeaveEncashmenthr',
            ],
            [
                'label' => 'Leave Encashment',
                'route' => 'LeaveEncashmenthr',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'LeaveEncashmenthr',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'LeaveEncashmenthr',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'LeaveEncashmenthr',
                        'action' => 'view',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\LeaveSetup::class => ControllerFactory::class,
            Controller\SubstituteLeave::class => ControllerFactory::class,
            Controller\LeaveCarryForward::class => ControllerFactory::class,
            Controller\leaveAssign::class => ControllerFactory::class,
            Controller\LeaveApply::class => ControllerFactory::class,
            Controller\LeaveStatus::class => ControllerFactory::class,
            Controller\LeaveBalance::class => ControllerFactory::class,
            Controller\LeaveSubBypass::class => ControllerFactory::class,
            Controller\LeaveReportCard::class => ControllerFactory::class,
            Controller\LeaveDeduction::class => ControllerFactory::class,
            Controller\LeaveEncashmenthr::class => ControllerFactory::class
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];


