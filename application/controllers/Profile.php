<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends RMS_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $user_id = $this->session->userdata('user_id');

        $this->data['title']         = 'My Profile — RMS';
        $this->data['user']          = $this->User_model->get_by_id($user_id);
        $this->data['flash_success'] = $this->session->flashdata('success');
        $this->data['flash_error']   = $this->session->flashdata('error');

        $this->load->view('profile/index', $this->data);
    }

    public function edit()
    {
        $user_id = $this->session->userdata('user_id');

        $this->data['title']         = 'Edit Profile — RMS';
        $this->data['user']          = $this->User_model->get_by_id($user_id);
        $this->data['flash_success'] = $this->session->flashdata('success');
        $this->data['flash_error']   = $this->session->flashdata('error');

        $this->load->view('profile/edit', $this->data);
    }

    public function update()
    {
        $user_id = $this->session->userdata('user_id');
        $user    = $this->User_model->get_by_id($user_id);

        // ── Validation ────────────────────────────────────────────────
        $this->form_validation->set_rules('firstname',   'First Name',   'trim|required|max_length[50]');
        $this->form_validation->set_rules('lastname',    'Last Name',    'trim|required|max_length[50]');
        $this->form_validation->set_rules('nickname',    'Nickname',     'trim|max_length[50]');
        $this->form_validation->set_rules('address',     'Address',      'trim|required');
        $this->form_validation->set_rules('contactno',   'Contact',      'trim|required|max_length[20]');
        $this->form_validation->set_rules('employee_id', 'Employee ID',  'trim|max_length[50]');
        $this->form_validation->set_rules('birthday',    'Birthday',     'trim');

        // Password rules only if user is trying to change it
        $new_password = $this->input->post('new_password');
        if (!empty($new_password)) {
            $this->form_validation->set_rules('current_password', 'Current Password', 'trim|required');
            $this->form_validation->set_rules('new_password',     'New Password',     'trim|required|min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|matches[new_password]');
        }

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors('<p>', '</p>'));
            redirect('profile/edit');
            return;
        }

        // ── Build update data ─────────────────────────────────────────
        $data = [
            'firstname'   => $this->input->post('firstname',   TRUE),
            'lastname'    => $this->input->post('lastname',    TRUE),
            'nickname'    => $this->input->post('nickname',    TRUE),
            'address'     => $this->input->post('address',     TRUE),
            'contactno'   => $this->input->post('contactno',   TRUE),
            'employee_id' => $this->input->post('employee_id', TRUE),
            'birthday'    => $this->input->post('birthday',    TRUE),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        // ── Password change (optional) ────────────────────────────────
        if (!empty($new_password)) {
            $current_password = $this->input->post('current_password', FALSE);

            // Verify current password against DB
            if (!password_verify($current_password, $user->password)) {
                $this->session->set_flashdata('error', 'Current password is incorrect.');
                redirect('profile/edit');
                return;
            }

            // Confirm new password matches
            $confirm = $this->input->post('confirm_password', FALSE);
            if ($new_password !== $confirm) {
                $this->session->set_flashdata('error', 'New passwords do not match.');
                redirect('profile/edit');
                return;
            }

            $data['password'] = password_hash($new_password, PASSWORD_BCRYPT);
        }

        // ── Save ──────────────────────────────────────────────────────
        $this->User_model->update($user_id, $data);

        // Update session name if firstname changed
        $this->session->set_userdata('firstname', $data['firstname']);

        $this->session->set_flashdata('success', 'Profile updated successfully.');
        redirect('profile');
    }
}