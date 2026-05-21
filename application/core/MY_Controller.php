<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Controller
 *
 * Base controller for all RMS controllers.
 *
 * Architecture:
 *   MY_Controller          → base (session, db, helpers, common view data)
 *   ├── Guest_Controller   → login/register pages (redirects if already logged in)
 *   └── RMS_Controller     → protected pages (redirects if NOT logged in)
 */
class MY_Controller extends CI_Controller {

    /** @var array Shared data passed to every view */
    protected $data = [];

    /** @var int|null Current logged-in user's ID */
    protected $current_user_id = null;

    public function __construct()
    {
        parent::__construct();

        // Explicitly load session even though it is in autoload.
        // This guarantees session exists before any constructor logic
        // in child controllers runs. Loading an already-loaded library
        // in CI3 is safe and idempotent.
        $this->load->library('session');

        // Pre-fetch flashdata into $this->data so views NEVER need to
        // call $this->session directly. This is the fix for the
        // "flashdata() on null" crash.
        $this->data['base_url']      = base_url();
        $this->data['site_name']     = 'RMS';
        $this->data['flash_error']   = $this->session->flashdata('error');
        $this->data['flash_success'] = $this->session->flashdata('success');
        $this->data['flash_info']    = $this->session->flashdata('info');
    }
}


/**
 * Guest_Controller
 *
 * For pages accessible ONLY when NOT logged in.
 * Logged-in users hitting these pages get redirected to dashboard.
 */
class Guest_Controller extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('user_id'))
        {
            redirect('dashboard');
            exit;
        }
    }
}


/**
 * RMS_Controller
 *
 * For ALL protected pages requiring authentication.
 * Guests hitting these pages get redirected to login.
 */
class RMS_Controller extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        if ( ! $this->session->userdata('user_id'))
        {
            $this->session->set_flashdata('error', 'Please log in to continue.');
            redirect('auth/login');
            exit;
        }

        $this->current_user_id = $this->session->userdata('user_id');
    }
}