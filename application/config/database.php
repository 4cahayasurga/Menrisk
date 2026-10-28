<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS (SQL SERVER)
| -------------------------------------------------------------------
| Terhubung ke SQL Server (Azure SQL Edge / MS SQL Server).
| Otomatis mendeteksi host 'db' (Docker) atau '127.0.0.1,1433' (Local Host).
*/

$active_group = 'default';
$query_builder = TRUE;

// Deteksi otomatis hostname (Docker container 'db' vs Localhost)
$db_host = 'db';
if (function_exists('gethostbyname') && gethostbyname('db') === 'db' && !file_exists('/.dockerenv')) {
    $db_host = '127.0.0.1,1433';
}

$db['default'] = array(
	'dsn'	   => '',
	'hostname' => $db_host,
	'username' => 'sa',
	'password' => 'PasswordKuat123!',
	'database' => 'menrisk_db',
	'dbdriver' => 'sqlsrv',
	'subdriver'=> '',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => FALSE, // Set false agar tidak unhandled crash jika DB belum di-create
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt'  => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
