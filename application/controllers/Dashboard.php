<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'core/MY_Controller.php';

class Dashboard extends RMS_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->data['title']     = 'Dashboard — RMS';
        $this->data['firstname'] = $this->session->userdata('firstname');
        $this->data['lastname']  = $this->session->userdata('lastname');
        $this->data['email']     = $this->session->userdata('email');

        $this->load->view('dashboard/index', $this->data);
    }
}