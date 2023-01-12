<?php

return [
    'db' => [
        // 'driver' => 'oci8',
        'driver' => 'odbc',
        'connection_string' => '(DESCRIPTION =
        (ADDRESS = (PROTOCOL = TCP)(HOST = 172.16.100.1)(PORT = 30015))
        (CONNECT_DATA =
        (SERVER = DEDICATED)
        (SERVICE_NAME = SAPHANA)
        )
        )',
        // 'username' => 'HRISVISMA2',
        // 'password' => 'HRIS@neo123',
        // 'username' => 'NOCHR',
        // 'password' => 'NocHR@123',

        'username' => 'NOCHR',
        'password'=> 'Noc$%^@123',
        'platform_options' => ['quote_identifiers' => false]
    ],
    'service_manager' => [
        'factories' => [
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ],
    ],
];


// return [
//     'db' => [
//         'driver' => 'oci8',
//         'connection_string' => '(DESCRIPTION =
//         (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.4.2)(PORT = 1521))
//         (CONNECT_DATA =
//         (SERVER = DEDICATED)
//         (SERVICE_NAME = ITN)
//         )
//         )',
//         'username' => 'HRIS',
//         'password' => 'NEO_HRIS',
//         'platform_options' => ['quote_identifiers' => false]
//     ],
//     'service_manager' => [
//         'factories' => [
//             'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
//         ],
//     ],
// ];
        // (ADDRESS = (PROTOCOL = TCP)(HOST = 172.16.100.1)(PORT = 30015))
// (ADDRESS = (PROTOCOL = TCP)(HOST = 10.10.10.92)(PORT = 30015))

//for postgres sql
//return [
//    'db' => [
//        'driver' => 'Pgsql',
//        'host'     => '10.255.0.10',
//        'username' => 'postgres',
//        'password' => 'test@2456',
//        'dbname' => 'postgres',
//        'port' => '5432'
//    ]
//];

