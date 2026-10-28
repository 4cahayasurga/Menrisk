<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| AUTO-LOADER SETTINGS (MENRISK CI3)
| -------------------------------------------------------------------
*/

$autoload['packages'] = array();

// Autoload Libraries
$autoload['libraries'] = array('database', 'session', 'risk_calculator');

// Autoload Drivers
$autoload['drivers'] = array();

// Autoload Helpers
$autoload['helper'] = array('url', 'html', 'form', 'menrisk');

// Autoload Config
$autoload['config'] = array();

// Autoload Language
$autoload['language'] = array();

// Autoload Models
$autoload['model'] = array('Risk_model', 'Kri_model', 'User_model', 'Department_model');

