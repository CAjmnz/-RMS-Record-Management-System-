<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'core/MY_Controller.php';

class Dashboard extends RMS_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Activity_log_model'); // ✅ FIXED
    }

    public function index()
    {
        $this->data['title']     = 'Dashboard — RMS';
        $this->data['firstname'] = $this->session->userdata('firstname');
        $this->data['lastname']  = $this->session->userdata('lastname');
        $this->data['email']     = $this->session->userdata('email');
        $this->data['role']      = $this->session->userdata('role');

        $this->data['stats'] = $this->User_model->get_stats();

        // ✅ REAL LOGS FROM DB
        $this->data['logs'] = $this->Activity_log_model->get_recent(10);

        $this->load->view('dashboard/index', $this->data);
    }
}