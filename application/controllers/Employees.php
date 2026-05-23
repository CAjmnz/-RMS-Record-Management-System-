<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employees extends RMS_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
    }

    /**
     * EMPLOYEE DIRECTORY PAGE
     */
    public function index()
    {
        $this->data['title'] = 'Employee Directory — RMS';

        $this->data['role'] = $this->session->userdata('role');

        $this->data['employees'] = $this->User_model->get_all();

        $this->load->view('employees/index', $this->data);
    }
}