<?php

return [
'db' => [


    'driver'=> 'odbc',
    'dsn'=> 'SAPHANA',
    'database'=> 'HRIS',
    'username'=> 'HRISVISMA2',
    'password'=> 'HRIS@neo123',



// 'platform_options' => ['quote_identifiers' => false]
],
'service_manager' => [
'factories' => [
'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
],
],
];
//for postgres sql
//return [
// 'db' => [
// 'driver' => 'Pgsql',
// 'host' => '10.255.0.10',
// 'username' => 'postgres',
// 'password' => 'test@2456',
// 'dbname' => 'postgres',
// 'port' => '5432'
// ]
//];