<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends Guest_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['form', 'url']);
        $this->load->library('form_validation');
        $this->load->model('auth/Auth_model', 'auth_model');
        $this->load->model('auth/Login_attempt_model', 'attempt_model');
    }

    public function index()
    {
        $this->data['title'] = 'Login — RMS';
        $this->load->view('auth/login', $this->data);
    }

    public function submit()
    {
        $ip = $this->input->ip_address();

        // ── Brute force check ─────────────────────────────────────────
        if ($this->attempt_model->is_locked_out($ip)) {
            $remaining = $this->attempt_model->get_lockout_remaining($ip);
            $this->session->set_flashdata(
                'error',
                "Too many failed attempts. Please wait {$remaining} minute(s) before trying again."
            );
            redirect('auth/login');
            return;
        }

        // ── Form validation ───────────────────────────────────────────
        $this->form_validation->set_rules('email',    'Email',    'required|trim|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->data['title'] = 'Login — RMS';
            $this->load->view('auth/login', $this->data);
            return;
        }

        $email    = $this->input->post('email', TRUE);
        $password = $this->input->post('password', FALSE);

        $user = $this->auth_model->get_by_email($email);

        // ── Failed login ──────────────────────────────────────────────
        if (!$user || !password_verify($password, $user->password)) {
            $this->attempt_model->log_attempt($ip);

            $attempts_used = $this->attempt_model->count_recent($ip);
            $remaining_attempts = max(5 - $attempts_used, 0);

            if ($remaining_attempts === 0) {
                $this->session->set_flashdata(
                    'error',
                    'Too many failed attempts. You are locked out for 15 minutes.'
                );
            } else {
                $this->session->set_flashdata(
                    'error',
                    "Invalid email or password. {$remaining_attempts} attempt(s) remaining."
                );
            }

            redirect('auth/login');
            return;
        }

        // ── Successful login ──────────────────────────────────────────
        $this->attempt_model->clear($ip); // wipe failed attempts on success

        $session_data = [
            'user_id'         => $user->id,
            'firstname'       => $user->firstname,
            'lastname'        => $user->lastname,
            'email'           => $user->email,
            'role'            => $user->role,
            'profile_picture' => $user->profile_picture ?? null, // ← add this line
            'logged_in'       => TRUE,
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