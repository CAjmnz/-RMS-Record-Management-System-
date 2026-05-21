<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| AUTOLOAD LIBRARIES
|
| session     — needed on virtually every request (login check, flashdata)
| database    — needed on virtually every request for an RMS
|
| DO NOT autoload: form_validation, email, upload, pdf, excel
| These are heavy and only needed on specific controllers.
| Load them in the controller constructor that needs them.
*/
$autoload['libraries'] = ['session', 'database'];

/*
| AUTOLOAD HELPERS
|
| url    — base_url(), site_url(), redirect() used everywhere
| form   — form_open(), form_input() etc used in many views
|
| DO NOT autoload: file, download, date (load where needed)
*/
$autoload['helper'] = ['url', 'form'];

/*
| AUTOLOAD CONFIG
| Only if you have a custom config file used everywhere.
*/
$autoload['config'] = [];

/*
| AUTOLOAD MODELS
| DO NOT autoload models. Load them in the controller that needs them.
| Autoloading models couples every request to every model's DB queries.
*/
$autoload['model'] = [];

$autoload['drivers']    = [];
$autoload['language']   = [];
$autoload['packages']   = [];