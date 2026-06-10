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
$route['users/store'] = 'users/store';
$route['users/get/(:num)'] = 'users/get/$1';
$route['users/update/(:num)'] = 'users/update/$1';
$route['users/delete/(:num)'] = 'users/delete/$1';
$route['users/reset-password/(:num)'] = 'users/reset_password/$1';
/*
|--------------------------------------------------------------------------
| Logout Route
| Points to Logout controller which extends RMS_Controller.
| This allows logged-in users to reach it without being redirected away.
|--------------------------------------------------------------------------
*/
$route['logout']  = 'auth/logout/index';

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
$route['profile']       = 'profile/index';
$route['profile/edit']  = 'profile/edit';
$route['profile/update']= 'profile/update';
/*
|--------------------------------------------------------------------------
| Employees Route
|--------------------------------------------------------------------------
*/
$route['employees'] = 'employees/index';


/*
|--------------------------------------------------------------------------
| Dashboard Route
|--------------------------------------------------------------------------
*/
$route['uploadDocs'] = 'users/uploadDocs';


/*
|--------------------------------------------------------------------------
| Reserved Routes
|--------------------------------------------------------------------------
*/
$route['404_override']         = '';
$route['translate_uri_dashes'] = FALSE;
