<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends RMS_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->data['title'] = 'Users — RMS';
        $this->data['role']  = $this->session->userdata('role');
        $this->data['users'] = $this->User_model->get_all();

        $this->load->view('users/index', $this->data);
    }

    public function store()
    {
        // Admin only
        if ($this->session->userdata('role') !== 'admin') {
            show_404();
        }

        // AJAX only
        if ( ! $this->input->is_ajax_request()) {
            show_404();
        }

        $this->form_validation->set_rules('firstname',        'First Name',       'trim|required|max_length[100]');
        $this->form_validation->set_rules('lastname',         'Last Name',        'trim|required|max_length[100]');
        $this->form_validation->set_rules('birthday',         'Birthday',         'trim|required');
        $this->form_validation->set_rules('address',          'Address',          'trim|required|max_length[255]');
        $this->form_validation->set_rules('contactno',        'Contact No',       'trim|required|max_length[20]');
        $this->form_validation->set_rules('email',            'Email',            'trim|required|valid_email|max_length[150]|is_unique[users.email]');
        $this->form_validation->set_rules('password',         'Password',         'trim|required|min_length[6]|max_length[72]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|matches[password]');
        $this->form_validation->set_rules('role',             'Role',             'trim|required|in_list[admin,user]');
        $this->form_validation->set_rules('is_active',        'Status',           'trim|required|in_list[0,1]');

        if ($this->form_validation->run() == FALSE)
        {
            $this->output
                 ->set_content_type('application/json')
                 ->set_status_header(422)
                 ->set_output(json_encode([
                     'status' => 'error',
                     'errors' => $this->form_validation->error_array(),
                 ]));
            return;
        }

        $data = [
            'firstname'  => $this->input->post('firstname',  TRUE),
            'lastname'   => $this->input->post('lastname',   TRUE),
            'birthday'   => $this->input->post('birthday',   TRUE),
            'address'    => $this->input->post('address',    TRUE),
            'contactno'  => $this->input->post('contactno',  TRUE),
            'email'      => $this->input->post('email',      TRUE),
            'password'   => password_hash($this->input->post('password'), PASSWORD_BCRYPT),
            'role'       => $this->input->post('role',       TRUE),
            'is_active'  => $this->input->post('is_active',  TRUE),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($this->User_model->insert($data))
        {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode([
                     'status'  => 'success',
                     'message' => 'User created successfully.',
                 ]));
        }
        else
        {
            $this->output
                 ->set_content_type('application/json')
                 ->set_status_header(500)
                 ->set_output(json_encode([
                     'status' => 'error',
                     'errors' => ['db' => 'Database error. Please try again.'],
                 ]));
        }
    }
}