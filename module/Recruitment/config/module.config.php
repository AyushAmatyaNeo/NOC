<?php

namespace Recruitment;

use Application\Controller\ControllerFactory;
use Zend\Router\Http\Segment;
use Recruitment\Controller\VacancyController;
use Recruitment\Controller\OpeningController;
use Recruitment\Controller\VacancyoptionsController;
use Recruitment\Controller\OptionsController;
use Recruitment\Controller\StageController;
use Recruitment\Controller\VacancyStageController;
use Recruitment\Controller\VacancyLevelController;
use Recruitment\Controller\VacancyInclusionController;
use Recruitment\Controller\UserApplicationController;
use Recruitment\Controller\SkillController;
use Recruitment\Controller\CalendarController;
use Recruitment\Controller\ReportController;
use Recruitment\Controller\InstructionController;
use Recruitment\Controller\OnboardController;

return [
    'router' => [
        'routes' => [
            'vacancy' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/recruitment/vacancy[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => VacancyController::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'instruction' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/recruitment/instruction[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => InstructionController::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'opening' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/recruitment/opening[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => OpeningController::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'vacancyoptions' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/recruitment/vacancyoptions[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => VacancyoptionsController::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'options' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/recruitment/options[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => OptionsController::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'stage' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/recruitment/stage[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => StageController::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'vacancystage' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/recruitment/vacancystage[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => VacancyStageController::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'vacancylevel' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/recruitment/vacancylevel[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => VacancyLevelController::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'vacancyinclusion' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/recruitment/vacancyinclusion[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => VacancyInclusionController::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'userapplication' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/recruitment/userapplication[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => UserApplicationController::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'skill' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/recruitment/skill[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => SkillController::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'calendar' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/recruitment/calendar[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => CalendarController::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'report' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/recruitment/report[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => ReportController::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'onboard' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/recruitment/onboard[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => OnboardController::class,
                        'action' => 'index',
                    ]
                ],
            ],
        ],
    ],
    'navigation' => [
        'vacancy' => [
            [
                'label' => 'vacancy',
                'route' => 'vacancy',
            ],
                [
                'label' => 'vacancy',
                'route' => 'vacancy',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'vacancy',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'vacancy',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'vacancy',
                        'action' => 'edit',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'vacancy',
                        'action' => 'view',
                    ],
                ]
            ]
        ],
        'opening' => [
            [
                'label' => 'Opening',
                'route' => 'opening',
            ],
                [
                'label' => 'Opening',
                'route' => 'opening',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'opening',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'opening',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'opening',
                        'action' => 'edit',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'opening',
                        'action' => 'view',
                    ],
                ]
            ]
        ],
        'vacancyoptions' => [
            [
                'label' => 'Vacancyoptions',
                'route' => 'vacancyoptions',
            ],
                [
                'label' => 'Vacancyoptions',
                'route' => 'vacancyoptions',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'vacancyoptions',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'vacancyoptions',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'vacancyoptions',
                        'action' => 'edit',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'vacancyoptions',
                        'action' => 'view',
                    ],
                ]
            ]
        ],
        'options' => [
            [
                'label' => 'Options',
                'route' => 'options',
            ],
                [
                'label' => 'Options',
                'route' => 'options',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'options',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'options',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'options',
                        'action' => 'edit',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'options',
                        'action' => 'view',
                    ],
                ]
            ]
        ],
        'stage' => [
            [
                'label' => 'Stage',
                'route' => 'stage',
            ],
                [
                'label' => 'Stage',
                'route' => 'stage',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'stage',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'stage',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'stage',
                        'action' => 'edit',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'stage',
                        'action' => 'view',
                    ],
                ]
            ]
        ],
        'vacancystage' => [
            [
                'label' => 'VacancyStage',
                'route' => 'vacancystage',
            ],
                [
                'label' => 'VacancyStage',
                'route' => 'vacancystage',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'vacancystage',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'vacancystage',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'vacancystage',
                        'action' => 'edit',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'vacancystage',
                        'action' => 'view',
                    ],
                ]
            ]
        ],
        'vacancylevel' => [
            [
                'label' => 'Vacancy Level',
                'route' => 'vacancylevel',
            ],
                [
                'label' => 'Vacancy Level',
                'route' => 'vacancylevel',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'vacancylevel',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'vacancylevel',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'vacancylevel',
                        'action' => 'edit',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'vacancylevel',
                        'action' => 'view',
                    ],
                ]
            ]
        ],
        'vacancyinclusion' => [
            [
                'label' => 'Vacancy Inclusion',
                'route' => 'vacancyinclusion',
            ],
                [
                'label' => 'Vacancy Inclusion',
                'route' => 'vacancyinclusion',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'vacancyinclusion',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'vacancyinclusion',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'vacancyinclusion',
                        'action' => 'edit',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'vacancyinclusion',
                        'action' => 'view',
                    ],
                ]
            ]
        ],
        'userapplication' => [
            [
                'label' => 'User Application',
                'route' => 'userapplication',
            ],
                [
                'label' => 'User Application',
                'route' => 'userapplication',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'userapplication',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'userapplication',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'userapplication',
                        'action' => 'edit',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'userapplication',
                        'action' => 'view',
                    ],
                ]
            ]
        ],
        'skill' => [
            [
                'label' => 'skill',
                'route' => 'skill',
            ],
                [
                'label' => 'skill',
                'route' => 'skill',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'skill',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'skill',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'skill',
                        'action' => 'edit',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'skill',
                        'action' => 'view',
                    ],
                ]
            ]
        ],
        'calendar' => [
            [
                'label' => 'calendar',
                'route' => 'calendar',
            ],
                [
                'label' => 'calendar',
                'route' => 'calendar',
                'pages' => [
                    [
                        'label' => 'Details',
                        'route' => 'calendar',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'calendar',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'calendar',
                        'action' => 'edit',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'calendar',
                        'action' => 'view',
                    ],
                ]
            ]
        ],
        'report' => [
            [
                'label' => 'report',
                'route' => 'report',
            ],
                [
                'label' => 'report',
                'route' => 'report',
                'pages' => [
                    [
                        'label' => 'Details',
                        'route' => 'report',
                        'action' => 'index',
                    ],
                ]
            ]
        ],
    ],
    'controllers' => [
        'factories' => [
            OpeningController::class => ControllerFactory::class,
            VacancyController::class => ControllerFactory::class,
            VacancyoptionsController::class => ControllerFactory::class,
            OptionsController::class => ControllerFactory::class,
            StageController::class => ControllerFactory::class,
            VacancyStageController::class => ControllerFactory::class,
            VacancyLevelController::class => ControllerFactory::class,
            VacancyInclusionController::class => ControllerFactory::class,
            UserApplicationController::class => ControllerFactory::class,
            SkillController::class => ControllerFactory::class,
            CalendarController::class => ControllerFactory::class,
            ReportController::class => ControllerFactory::class,
            InstructionController::class => ControllerFactory::class,
            OnboardController::class => ControllerFactory::class,


        ],
    ],
        'view_manager' => [
            'template_path_stack' => [
                __DIR__ . '/../view',
            ],
        ],
];
