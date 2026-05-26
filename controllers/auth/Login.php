<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends Guest_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['form', 'url']);
        $this->load->library('form_validation');

        // Load model here with explicit alias — guarantees $this->auth_model is always set
        $this->load->model('auth/Auth_model', 'auth_model');
    }

    public function index()
    {
        $this->data['title'] = 'Login — RMS';
        $this->load->view('auth/login', $this->data);
    }

    public function submit()
    {
        $this->form_validation->set_rules('email',    'Email',    'required|trim|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->data['title'] = 'Login — RMS';
            $this->load->view('auth/login', $this->data);
            return;
        }

        $email    = $this->input->post('email',    TRUE);
        $password = $this->input->post('password', TRUE);

        // Use alias 'auth_model' — matches what we passed to load->model()
        $user = $this->auth_model->get_by_email($email);

        // ── TEMP DEBUG — REMOVE AFTER LOGIN WORKS ──
        echo '<pre>';
        var_dump($email);
        var_dump($password);
        var_dump($user);
        if ($user) {
            var_dump($user->password);
            var_dump(password_verify($password, $user->password));
        }
        echo '</pre>';
        die();
        // ── END DEBUG ──

        if ( ! $user || ! password_verify($password, $user->password)) {
            $this->session->set_flashdata('error', 'Invalid email or password.');
            redirect('auth/login');
            return;
        }

        $session_data = [
            'user_id'   => $user->id,
            'firstname' => $user->firstname,
            'lastname'  => $user->lastname,
            'email'     => $user->email,
            'role'      => $user->role,
            'logged_in' => TRUE,
        ];
        $this->session->set_userdata($session_data);

        $this->session->set_flashdata('success', 'Welcome back, ' . $user->firstname . '!');
        redirect('dashboard');
    }

    public function logout()
    {
        $this->session->sess_destroy();
        $this->session->set_flashdata('success', 'You have been logged out.');
        redirect('auth/login');
    }
}