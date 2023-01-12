<?php
return [
'db' => [



// 'driver'=> 'odbc',
// 'dsn'=> 'NEW_TEST_DB',
/////live-DATABASE////////
// 'database'=> 'NOCHR',
// 'username'=> 'NOCHR',
// 'password'=> 'Noc$%^@123',

///// Local DB /////////
'database'=> 'NOCHR',
'username'=> 'NOCHR',
'password'=> 'NocHR@123',

///////////////////////////////
// 'database'=> 'NOCHR_TEST',
// 'username'=> 'NOCHRTEST',
// 'password'=> 'Noc$%^@123',
///////////////////////////////
// 'driver'=> 'odbc',
// 'dsn'=> 'HANA_LIVE',
// 'database'=> 'HRNOC',
// 'username'=> 'HRNOC',
// 'password'=> 'NOCc$%^@123hr',
////////////////////////////////////////
// 'dsn'=> 'NOCTEST',
// 'database'=> 'HRTESTNOC',
// 'username'=> 'HRTESTNOC',
// 'password'=> 'HRTESTNoc$%^123',
////////////////////////////////////////
//'username'=> 'HRTESTNOC',
//'password'=> 'HRTESTNoc$%^123',


// 'driver' => 'oci8',
// 'connection_string' => '(DESCRIPTION =
// (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521))
// (CONNECT_DATA =
// (SERVER = DEDICATED)
// (SERVICE_NAME = ORCL)
// )
// )',
//
//
//// for shijan HRIS start
// // lan ip 192.168.4.31
//// 'connection_string' => '(DESCRIPTION =
//// (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.4.31)(PORT = 1521))
//// (CONNECT_DATA =
//// (SERVER = DEDICATED)
//// (SERVICE_NAME = HRIS)
//// )
//// )',
//
//// 'username' => 'HRIS_BNL',
//// 'password' => 'HRIS_BNL',
//
//// 'username' => 'HRIS_MARUTI',
//// 'password' => 'HRIS_MARUTI',
//
//// for shijan HRIS stop
//
//// for manager HRIS start
// // lan ip 192.168.3.240
//// 'connection_string' => '(DESCRIPTION =
//// (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.3.240)(PORT = 1521))
//// (CONNECT_DATA =
//// (SERVER = DEDICATED)
//// (SERVICE_NAME = HR)
//// )
//// )',
////
////
//// 'username' => 'LAXMI_HRIS_PAYROLL',
//// 'password' => 'LAXMI_HRIS_PAYROLL',
//
//// for manager HRIS stop
//
//// 'username' => 'ALOFT_HRIS7677',
//// 'password' => 'ALOFT_HRIS7677',
//
//// 'username' => 'NBB7677',
//// 'password' => 'NBB7677',
//
// // 'username' => 'HRIS_SCP',
//// 'password' => 'HRIS_SCP',
//
//// 'username' => 'NBB7677_MAR',
//// 'password' => 'NBB7677_MAR',
//
//// 'username' => 'NBB7778_OCT',
// // 'password' => 'NBB7778_OCT',
//
// 'username' => 'NBB7778',
// 'password' => 'NBB7778',
//
//// 'username' => 'JGI_7778',
//// 'password' => 'JGI_7778',
//
//// 'username' => 'HRIS_GWS_JUL',
//// 'password' => 'HRIS_GWS_JUL',
//
//// 'username' => 'HRIS_GWS_JUN',
//// 'password' => 'HRIS_GWS_JUN',
//
//// 'username' => 'LAXMI_PAYROLL',
//// 'password' => 'LAXMI_PAYROLL',
//
//// 'username' => 'HRIS_KISOK',
//// 'password' => 'HRIS_KISOK',
//
//// 'username' => 'HRIS_GWS_MAR',
//// 'password' => 'HRIS_GWS_MAR',
//
//// 'username' => 'NBB7677JAN23',
//// 'password' => 'NBB7677JAN23',
//
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