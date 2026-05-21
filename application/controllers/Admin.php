<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();

        if (!$this->session->userdata('logged_in') || $this->session->userdata('is_admin') != 1) {
            redirect('dashboard');
        }
    }

    public function users() {
        $data['users'] = $this->User_model->getAllUsers();
        $this->load->view('admin/users_list', $data);
    }

    public function create() {
        $this->load->view('admin/user_form');
    }
}