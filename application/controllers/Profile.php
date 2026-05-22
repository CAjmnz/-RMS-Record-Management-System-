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

    /**
     * VIEW OWN PROFILE
     */
    public function index()
    {
        $user_id = $this->session->userdata('user_id');

        $this->data['title'] = 'My Profile — RMS';
        $this->data['user']  = $this->User_model->get_by_id($user_id);

        $this->load->view('profile/index', $this->data);
    }

    /**
     * EDIT OWN PROFILE PAGE
     */
    public function edit()
    {
        $user_id = $this->session->userdata('user_id');

        $this->data['title'] = 'Edit Profile — RMS';
        $this->data['user']  = $this->User_model->get_by_id($user_id);

        $this->load->view('profile/edit', $this->data);
    }

    /**
     * UPDATE PROFILE (POST)
     */
    public function update()
    {
        $user_id = $this->session->userdata('user_id');

        $this->form_validation->set_rules('nickname', 'Nickname', 'trim|max_length[50]');
        $this->form_validation->set_rules('address', 'Address', 'trim|required|max_length[255]');
        $this->form_validation->set_rules('contactno', 'Contact No', 'trim|required|max_length[20]');
        $this->form_validation->set_rules('password', 'Password', 'trim|min_length[6]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('profile/edit');
            return;
        }

        $data = [
            'nickname'  => $this->input->post('nickname', TRUE),
            'address'   => $this->input->post('address', TRUE),
            'contactno' => $this->input->post('contactno', TRUE),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // update password only if filled
        if (!empty($this->input->post('password'))) {
            $data['password'] = password_hash(
                $this->input->post('password'),
                PASSWORD_BCRYPT
            );
        }

        $this->User_model->update($user_id, $data);

        $this->session->set_flashdata('success', 'Profile updated successfully.');
        redirect('profile');
    }
}