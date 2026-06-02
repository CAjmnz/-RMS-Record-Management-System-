<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends Guest_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['form', 'url']);
        $this->load->library('form_validation');

        $this->load->model('auth/Auth_model', 'auth_model');
        $this->load->model('User_model'); // 🔥 REQUIRED for password update
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

        if ($this->form_validation->run() === FALSE) {
            $this->data['title'] = 'Login — RMS';
            $this->load->view('auth/login', $this->data);
            return;
        }

        $email    = $this->input->post('email', TRUE);
        $password = $this->input->post('password', TRUE);

        $user = $this->auth_model->get_by_email($email);

        // ❌ REMOVE DEBUG BLOCK COMPLETELY

        if (!$user || !password_verify($password, $user->password)) {
            $this->session->set_flashdata('error', 'Invalid email or password.');
            redirect('auth/login');
            return;
        }

        // 🔐 SESSION SET
        $this->session->set_userdata([
            'user_id'   => $user->id,
            'firstname' => $user->firstname,
            'lastname'  => $user->lastname,
            'email'     => $user->email,
            'role'      => $user->role,
            'logged_in' => TRUE,
        ]);

        // 🔥 MUST CHANGE PASSWORD CHECK
        if ((int)$user->must_change_password === 1) {
            redirect('auth/change_password');
            return;
        }

        redirect('dashboard');
    }

    // 🔐 PASSWORD UPDATE (FIRST LOGIN FLOW)
    public function update_password()
    {
        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            return;
        }

        $password = $this->input->post('password', TRUE);

        if (empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Password required']);
            return;
        }

        $update = $this->User_model->update($user_id, [
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'must_change_password' => 0
        ]);

        if (!$update) {
            echo json_encode(['success' => false, 'message' => 'Update failed']);
            return;
        }

        echo json_encode(['success' => true, 'message' => 'Password updated']);
    }

    public function logout()
    {
        $this->session->sess_destroy();
        $this->session->set_flashdata('success', 'You have been logged out.');
        redirect('auth/login');
    }
}