<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    protected $data = [];
    protected $current_user_id = null;
    protected $current_user_role = null;

    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');

        $this->data['base_url']      = base_url();
        $this->data['site_name']     = 'RMS';
        $this->data['flash_error']   = $this->session->flashdata('error');
        $this->data['flash_success'] = $this->session->flashdata('success');
        $this->data['flash_info']    = $this->session->flashdata('info');
    }

    /**
     * RBAC: Admin-only guard
     */
    protected function require_admin()
    {
        if ($this->current_user_role !== 'admin') {
            $this->session->set_flashdata('error', 'Access denied: Admins only.');
            redirect('dashboard');
            exit;
        }
    }
}

/**
 * Guest Controller
 */
class Guest_Controller extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('user_id')) {
            redirect('dashboard');
            exit;
        }
    }
}

/**
 * RMS Controller (Protected)
 */
class RMS_Controller extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'Please log in to continue.');
            redirect('auth/login');
            exit;
        }

        $this->current_user_id = $this->session->userdata('user_id');

        $this->current_user_role = $this->session->userdata('role');

        // fallback from DB if session missing role
        if (!$this->current_user_role) {

            $this->load->model('auth/Auth_model');
            $user = $this->Auth_model->get_by_id($this->current_user_id);

            if ($user) {
                $this->current_user_role = $user->role;
                $this->session->set_userdata('role', $user->role);
            }
        }
    }
}