<?php

return [
    'db' => [
        'driver' => 'oci8',
        'connection_string' => '(DESCRIPTION =
            (ADDRESS = (PROTOCOL = TCP)(HOST = 127.0.1.1)(PORT = 1521))
            (CONNECT_DATA =
            (SERVER = DEDICATED)
            (SERVICE_NAME = XE)
            )
            )',
        'username' => 'HRIS_JGI',
        'password' => 'HRIS_JGI',
        'platform_options' => ['quote_identifiers' => false]
    ],
    'service_manager' => [
        'factories' => [
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ],
    ],
];


//return [
//    'db' => [
//        'driver' => 'oci8',
//        'connection_string' => '(DESCRIPTION =
//        (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.4.2)(PORT = 1521))
//        (CONNECT_DATA =
//        (SERVER = DEDICATED)
//        (SERVICE_NAME = ITN)
//        )
//        )',
////        'username' => 'HRIS',
////        'password' => 'NEO_HRIS',
////        'username' => 'JWL_HRIS_APR5',
////        'password' => 'JWL_HRIS_APR5',
//        'username' => 'ITNEPAL_HRIS_APR2',
//        'password' => 'ITNEPAL_HRIS_APR2',
////        'username' => 'LAXMI_HRIS_APR28',
////        'password' => 'LAXMI_HRIS_APR28',
//        'platform_options' => ['quote_identifiers' => false]
//    ],
//    'service_manager' => [
//        'factories' => [
//            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
//        ],
//    ],
//];
