<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends RMS_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function index()
    {
        $this->data['users']      = $this->User_model->get_all();
        $this->data['role']       = $this->session->userdata('role');
        $this->data['total_rows'] = count($this->data['users']);

        $this->load->view('users/index', $this->data);
    }

    public function get($id)
    {
        $user = $this->User_model->get_by_id($id);

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            return;
        }

        echo json_encode(['success' => true, 'data' => $user]);
    }

    public function store()
    {
        $data = [
            'employee_id' => $this->input->post('employee_id'),
            'firstname'   => $this->input->post('firstname'),
            'lastname'    => $this->input->post('lastname'),
            'birthday'    => $this->input->post('birthday'),
            'address'     => $this->input->post('address'),
            'contactno'   => $this->input->post('contactno'),
            'email'       => $this->input->post('email'),
            'password'    => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'role'        => $this->input->post('role'),
            'is_active'   => $this->input->post('is_active'),
            'job_title'   => $this->input->post('job_title'),
            'department'  => $this->input->post('department'),
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $insert = $this->User_model->insert($data);
        echo json_encode(['success' => $insert]);
    }

    public function update()
    {
        $id = $this->input->post('id');

        $data = [
            'firstname'   => $this->input->post('firstname'),
            'lastname'    => $this->input->post('lastname'),
            'employee_id' => $this->input->post('employee_id'),
            'birthday'    => $this->input->post('birthday'),
            'contactno'   => $this->input->post('contactno'),
            'address'     => $this->input->post('address'),
            'email'       => $this->input->post('email'),
            'role'        => $this->input->post('role'),
            'is_active'   => $this->input->post('is_active'),
            'job_title'   => $this->input->post('job_title'),
            'department'  => $this->input->post('department'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $update = $this->User_model->update($id, $data);
        echo json_encode(['success' => $update]);
    }

    public function delete($id)
    {
        $delete = $this->User_model->soft_delete($id);
        echo json_encode(['success' => $delete]);
    }
}