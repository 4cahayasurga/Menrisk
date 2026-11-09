<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
*/

$route['default_controller']   = 'auth';
$route['404_override']          = '';
$route['translate_uri_dashes']  = FALSE;

// Auth Routes (Login, Logout, Process)
$route['login']                 = 'auth/login';
$route['logout']                = 'auth/logout';
$route['auth']                  = 'auth/login';
$route['auth/login']            = 'auth/login';
$route['auth/process_login']    = 'auth/process_login';
$route['auth/logout']           = 'auth/logout';

// Dashboard Routes
$route['menrisk']               = 'menrisk/index';
$route['menrisk/index']         = 'menrisk/index';
$route['menrisk/detail/(:any)'] = 'menrisk/detail/$1';
$route['menrisk/export_brief']  = 'menrisk/export_brief';
$route['api/risks']             = 'menrisk/index';

// Settings Routes (User & Department)
$route['settings']                  = 'settings/users';
$route['settings/user']             = 'settings/users';
$route['settings/users']            = 'settings/users';
$route['settings/user_save']        = 'settings/user_save';
$route['settings/user_delete/(:num)'] = 'settings/user_delete/$1';
$route['settings/user_departments/(:num)'] = 'settings/user_departments/$1';
$route['settings/department']       = 'settings/departments';
$route['settings/departments']      = 'settings/departments';
$route['settings/department_save']  = 'settings/department_save';
$route['settings/department_delete/(:num)'] = 'settings/department_delete/$1';
$route['settings/toggle_department'] = 'settings/toggle_user_department';

$route['menrisk/profil_risiko']             = 'menrisk/profil_risiko';
$route['menrisk/profil_risiko/(:num)']      = 'menrisk/profil_risiko/$1';
$route['menrisk/profil-risiko']             = 'menrisk/profil_risiko';
$route['menrisk/profil-risiko/(:num)']      = 'menrisk/profil_risiko/$1';
$route['profil-risiko']                     = 'menrisk/profil_risiko';
$route['menrisk/save_profil_risiko']        = 'menrisk/save_profil_risiko';
$route['menrisk/daftar_profil_risiko']      = 'menrisk/daftar_profil_risiko';
$route['menrisk/daftar-profil-risiko']      = 'menrisk/daftar_profil_risiko';
$route['menrisk/detail_profil_risiko/(:num)'] = 'menrisk/detail_profil_risiko/$1';

// Routes Progress Monitoring Profile Risiko
$route['menrisk/monitoring_profil_risiko/(:num)']     = 'menrisk/monitoring_profil_risiko/$1';
$route['menrisk/save_monitoring_profil_risiko']       = 'menrisk/save_monitoring_profil_risiko';
$route['menrisk/detail_monitoring_profil_risiko/(:num)'] = 'menrisk/detail_monitoring_profil_risiko/$1';
$route['menrisk/delete_monitoring_profil_risiko/(:num)'] = 'menrisk/delete_monitoring_profil_risiko/$1';
