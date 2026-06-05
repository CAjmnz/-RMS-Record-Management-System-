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
        $this->form_validation->set_rules('firstname', 'First Name', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('lastname',  'Last Name',  'trim|required|max_length[50]');
        $this->form_validation->set_rules('nickname',  'Nickname',   'trim|max_length[50]');
        $this->form_validation->set_rules('address',   'Address',    'trim|required');
        $this->form_validation->set_rules('contactno', 'Contact',    'trim|required|max_length[20]');
        $this->form_validation->set_rules('birthday',  'Birthday',   'trim');

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
            'firstname'  => $this->input->post('firstname',  TRUE),
            'lastname'   => $this->input->post('lastname',   TRUE),
            'nickname'   => $this->input->post('nickname',   TRUE),
            'address'    => $this->input->post('address',    TRUE),
            'contactno'  => $this->input->post('contactno',  TRUE),
            'birthday'   => $this->input->post('birthday',   TRUE),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // ── Profile picture upload ────────────────────────────────────
        // ── Profile picture upload ────────────────────────────────────
if (!empty($_FILES['profile_picture']['name'])) {

    $upload_path = FCPATH . 'uploads/profile_pictures/';

    if (!is_dir($upload_path)) {
        mkdir($upload_path, 0755, TRUE);
    }

    $config = [
        'upload_path'   => $upload_path,
        'allowed_types' => 'jpg|jpeg|png|gif|webp',
        'max_size'      => 2048,
        'encrypt_name'  => TRUE,
    ];

    $this->load->library('upload', $config);

    if ($this->upload->do_upload('profile_picture')) {
        $upload_data = $this->upload->data();
        $new_path    = 'uploads/profile_pictures/' . $upload_data['file_name'];

        // Delete old picture if exists
        if (!empty($user->profile_picture)) {
            $old_file = FCPATH . $user->profile_picture;
            if (file_exists($old_file)) {
                unlink($old_file);
            }
        }

        $data['profile_picture'] = $new_path;

        // ✅ Update session so topbar shows new picture immediately
        $this->session->set_userdata('profile_picture', $new_path);

    } else {
        $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
        redirect('profile/edit');
        return;
    }
}

        // ── Password change (optional) ────────────────────────────────
        if (!empty($new_password)) {
            $current_password = $this->input->post('current_password', FALSE);

            if (!password_verify($current_password, $user->password)) {
                $this->session->set_flashdata('error', 'Current password is incorrect.');
                redirect('profile/edit');
                return;
            }

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

        $this->session->set_userdata('firstname', $data['firstname']);

        $this->session->set_flashdata('success', 'Profile updated successfully.');
        redirect('profile');
    }
}