<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Default Controller
|--------------------------------------------------------------------------
*/
$route['default_controller'] = 'home';

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
$route['auth/login']        = 'auth/login/index';
$route['auth/login/submit'] = 'auth/login/submit';
$route['login']             = 'auth/login/index';
/*
|--------------------------------------------------------------------------
| Users Routes
|--------------------------------------------------------------------------
*/
$route['users'] = 'users/index';
$route['users/create'] = 'users/create';
$route['users/store'] = 'users/store';
/*
|--------------------------------------------------------------------------
| Logout Route
| Points to Logout controller which extends RMS_Controller.
| This allows logged-in users to reach it without being redirected away.
|--------------------------------------------------------------------------
*/
$route['logout']            = 'auth/logout/index';

/*
|--------------------------------------------------------------------------
| Dashboard Route
|--------------------------------------------------------------------------
*/
$route['dashboard'] = 'dashboard/index';

/*
|--------------------------------------------------------------------------
| Profile Route
|--------------------------------------------------------------------------
*/
$route['profile'] = 'profile/index';
$route['profile/edit'] = 'profile/edit';
$route['profile/update'] = 'profile/update';
/*
|--------------------------------------------------------------------------
| Reserved Routes
|--------------------------------------------------------------------------
*/
$route['404_override']         = '';
$route['translate_uri_dashes'] = FALSE;