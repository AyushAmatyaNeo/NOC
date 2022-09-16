<?php

namespace Application;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => Controller\DashboardController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'application' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/application[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'auth' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/auth',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action' => 'login',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'process' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '[/:action]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [],
                        ],
                    ],
                ],
            ],
            'login' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/login[/:type]',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action' => 'login',
                    ],
                ],
            ],
            'logout' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/logout',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action' => 'logout',
                    ],
                ],
            ],
            'dashboard' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/dashboard[/:action]',
                    'defaults' => [
                        'controller' => Controller\DashboardController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'process' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '[/:action]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [],
                        ],
                    ],
                ],
            ],
            'task' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/task[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\TaskController::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'recover' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/recover[/:action[/:employeeId]]',
                    'defaults' => [
                        'controller' => Controller\ForgotPasswordController::class,
                        'action' => 'email',
                    ],
                ],
            ],
            'registerAttendance' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/registerAttendance[/:action[/:userId][/:type]]',
                    'defaults' => [
                        'controller' => Controller\RegisterAttendanceController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'apiEmployee' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/api[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\ApiController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'apiAttendance' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/api/attendance[/:action[/:year][/:month][/:day][/:employeeCode]]',
                    'defaults' => [
                        'controller' => Controller\ApiAttendanceController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'updatePwd' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/auth[/:action[/:un]]',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'cronBrij' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/cronBrij[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\CronBrij::class,
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'navigation' => [
        'navigation-example' => [
            [
                'label' => 'Google',
                'uri' => 'https://www.google.com',
                'target' => '_blank'
            ],
            [
                'label' => 'Home',
                'route' => 'leavesetup'
            ],
            [
                'label' => 'Modules',
                'uri' => '#',
                'pages' => [
                    [
                        'label' => 'LearnZF2Ajax',
                        'route' => 'leavesetup'
                    ],
                    [
                        'label' => 'LearnZF2FormUsage',
                        'route' => 'leavesetup'
                    ],
                    [
                        'label' => 'LearnZF2Barcode',
                        'route' => 'leavesetup'
                    ],
                    [
                        'label' => 'LearnZF2Pagination',
                        'route' => 'leavesetup'
                    ],
                    [
                        'label' => 'LearnZF2Log',
                        'route' => 'leavesetup'
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            'navigation-menu' => 'Application\Navigation\NavigationFactory',
        ]
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\DashboardController::class => Controller\ControllerFactory::class,
            Controller\TaskController::class => Controller\ControllerFactory::class,
            Controller\ForgotPasswordController::class => Controller\ControllerFactory::class,
            Controller\ChangePassword::class => Controller\ControllerFactory::class,
            Controller\ApiController::class => Controller\ControllerFactory::class,
            Controller\ApiAttendanceController::class => Controller\ControllerFactory::class,
            Controller\CronBrij::class => Controller\ControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'layout/login' => __DIR__ . '/../view/layout/login.phtml',
            'layout/json' => __DIR__ . '/../view/layout/json.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
            'error/no_access' => __DIR__ . '/../view/error/no_access.phtml',
            'partial/header' => __DIR__ . '/../view/layout/partials/header.phtml',
            'partial/footer' => __DIR__ . '/../view/layout/partials/footer.phtml',
            'partial/sidebar' => __DIR__ . '/../view/layout/partials/sidebar.phtml',
            'partial/breadcrumb' => __DIR__ . '/../view/layout/partials/breadcrumb.phtml',
            'partial/profile' => __DIR__ . '/../view/layout/partials/profile.phtml',
            'dashboard-item/holiday-list' => __DIR__ . '/../view/layout/dashboard-items/holiday-list.phtml',
            'dashboard-item/attendance-request' => __DIR__ . '/../view/layout/dashboard-items/attendance-request.phtml',
            'dashboard-item/leave-apply' => __DIR__ . '/../view/layout/dashboard-items/leave-apply.phtml',
            'dashboard-item/present-absent' => __DIR__ . '/../view/layout/dashboard-items/present-absent.phtml',
            'dashboard-item/employee-count-by-branch' => __DIR__ . '/../view/layout/dashboard-items/employee-count-by-branch.phtml',
            'dashboard-item/today-leave' => __DIR__ . '/../view/layout/dashboard-items/today-leave.phtml',
            'dashboard-item/birthdays' => __DIR__ . '/../view/layout/dashboard-items/birthdays.phtml',
            'dashboard/employee' => __DIR__ . '/../view/application/dashboard/employee-dashboard.phtml',
            'dashboard/hrm' => __DIR__ . '/../view/application/dashboard/hrm-dashboard.phtml',
            'dashboard/branch-manager' => __DIR__ . '/../view/application/dashboard/branch-manager-dashboard.phtml',
        ],
//        'base_path' => '/public/',
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'dashboard-items' => [
//        'holiday-list' => 'dashboard-item/holiday-list',
//        'attendance-request' => 'dashboard-item/attendance-request',
//        'leave-apply' => 'dashboard-item/leave-apply',
//        'present-absent' => 'dashboard-item/present-absent',
//        'emp-cnt-by-branch' => 'dashboard-item/employee-count-by-branch',
//        'today-leave' => 'dashboard-item/today-leave',
//        'birthdays' => 'dashboard-item/birthdays',
        'dashboard' => "",
    ],
    'role-types' => [
        'A' => 'Human Resource',
        'B' => 'Branch Manager',
        'E' => 'Employee',
//        'D' => 'Department Manager'
    ],
//    'role-types' => [
//        'H' => 'Human Resource',
//        'E' => 'Employee'
//    ],
    'mail' => [
        'host' => 'duster.websitewelcome.com',
        'port' => 587,
        'connection_class' => 'login',
        'connection_config' => [
            'username' => 'ukesh.gaiju@itnepal.com',
            'password' => 'ukesh@123',
            'ssl' => 'tls',
        ],
    ],
    'genders' => [
        1 => 'Male',
        2 => 'Female',
        3 => 'Other'
    ],
    'default-profile-picture' => "default-profile-picture.jpg",
    'default-system-mail' => "somkala.pachhai@itnepal.com",
    'default-system-name' => "neo hris"
];
