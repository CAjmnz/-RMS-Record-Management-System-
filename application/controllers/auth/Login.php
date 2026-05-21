<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'core/MY_Controller.php';

class Login extends Guest_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->data['title'] = 'Login — RMS';
        $this->load->view('auth/login', $this->data);
    }

    public function submit()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === FALSE)
        {
            $this->data['title'] = 'Login — RMS';
            $this->load->view('auth/login', $this->data);
            return;
        }

        $email    = $this->input->post('email', TRUE);
        $password = $this->input->post('password', TRUE);

        $this->load->model('auth/Auth_model');
        $user = $this->Auth_model->get_by_email($email);

        if ( ! $user || ! password_verify($password, $user->password))
        {
            $this->session->set_flashdata('error', 'Invalid email or password.');
            redirect('auth/login');
            return;
        }

        $this->session->set_userdata([
            'user_id'   => $user->id,
            'email'     => $user->email,
            'firstname' => $user->firstname,
            'lastname'  => $user->lastname,
            'logged_in' => TRUE,
        ]);

        $this->session->set_flashdata('success', 'Welcome back, ' . $user->firstname . '!');
        redirect('dashboard');
    }
}