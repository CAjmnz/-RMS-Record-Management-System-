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
        $role           = $this->session->userdata('role');
        $logged_user_id = $this->session->userdata('id');
    
        // Rule 3: pass filter so model hides admins from regular users
        $filters = [];
        if ($role === 'user') {
            $filters['hide_admins'] = true;
        }
    
        $this->data['users']          = $this->User_model->get_all($filters);
        $this->data['role']           = $role;
        $this->data['logged_user_id'] = $logged_user_id;
        $this->data['total_rows']     = count($this->data['users']);
    
        $this->data['csrf_token_name']  = $this->security->get_csrf_token_name();
        $this->data['csrf_token_value'] = $this->security->get_csrf_hash();
    
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
        if ($this->session->userdata('role') !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
    
        $id        = $this->input->post('id');
        $firstname = $this->input->post('firstname');
        $lastname  = $this->input->post('lastname');
    
        // Rule 1: block if name combo already taken by another user
        if ($this->User_model->full_name_exists($firstname, $lastname, $id)) {
            echo json_encode([
                'success' => false,
                'message' => 'This name combination already exists.'
            ]);
            return;
        }
    
        $data = [
            'firstname'   => $firstname,
            'lastname'    => $lastname,
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