<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Login Controller
 *
 * Extends Guest_Controller so:
 * - Session is always loaded (from MY_Controller)
 * - Flashdata is always available (from MY_Controller)
 * - Logged-in users are auto-redirected away (from Guest_Controller)
 */
class Login extends Guest_Controller {

    public function __construct()
    {
        parent::__construct();

        // Load form helper and validation library for this controller only.
        // No need to autoload these globally.
        $this->load->helper(['form', 'url']);
        $this->load->library('form_validation');
    }

    /**
     * index()
     * Route: /auth/login or /login (depending on your routes.php)
     */
    public function index()
    {
        // $this->data already has flash_error, flash_success from MY_Controller
        // Just pass it to the view
        $this->data['title'] = 'Login — RMS';

        $this->load->view('auth/login', $this->data);
    }

    /**
     * submit()
     * Route: POST /auth/login/submit
     * Handles form submission
     */
    public function submit()
    {
        // Set validation rules
        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === FALSE) {
            // Validation failed — redisplay form with errors
            $this->data['title'] = 'Login — RMS';
            $this->load->view('auth/login', $this->data);
            return;
        }

        $username = $this->input->post('username', TRUE);
        $password = $this->input->post('password', TRUE);

        // Load the auth model
        $this->load->model('auth/Auth_model');
        $user = $this->Auth_model->get_by_username($username);

        if ( ! $user || ! password_verify($password, $user->password)) {
            $this->session->set_flashdata('error', 'Invalid username or password.');
            redirect('auth/login');
            return;
        }

        // Authentication passed — set session
        $session_data = [
            'user_id'   => $user->id,
            'username'  => $user->username,
            'role'      => $user->role,
            'logged_in' => TRUE,
        ];
        $this->session->set_userdata($session_data);

        $this->session->set_flashdata('success', 'Welcome back, ' . $user->username . '!');
        redirect('dashboard');
    }

    /**
     * logout()
     * Route: /auth/logout
     */
    public function logout()
    {
        $this->session->sess_destroy();
        $this->session->set_flashdata('success', 'You have been logged out.');
        redirect('auth/login');
    }
}