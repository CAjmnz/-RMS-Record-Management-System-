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
        $logged_user_id = $this->session->userdata('user_id'); // ✅ fixed from 'id'
        $filter         = $this->input->get('filter');

        $filters = [];

        if ($role === 'user') {
            $filters['hide_admins'] = true;
        }

        if ($filter === 'admins')    { $filters['role']     = 'admin'; }
        if ($filter === 'nonadmins') { $filters['role_not'] = 'admin'; }
        if ($filter === 'active')    { $filters['is_active'] = 1; }
        if ($filter === 'inactive')  { $filters['is_active'] = 0; }

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
        if ($this->session->userdata('role') !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $firstname   = trim($this->input->post('firstname',   TRUE));
        $lastname    = trim($this->input->post('lastname',    TRUE));
        $employee_id = trim($this->input->post('employee_id', TRUE));
        $birthday    = trim($this->input->post('birthday',    TRUE));
        $contactno   = trim($this->input->post('contactno',   TRUE));
        $address     = trim($this->input->post('address',     TRUE));
        $email       = trim($this->input->post('email',       TRUE));
        $password    = $this->input->post('password', FALSE);
        $role        = trim($this->input->post('role',        TRUE));
        $is_active   = trim($this->input->post('is_active',   TRUE));
        $job_title   = trim($this->input->post('job_title',   TRUE));
        $department  = trim($this->input->post('department',  TRUE));

        if (
            empty($firstname) || empty($lastname)    ||
            empty($employee_id) || empty($birthday)  ||
            empty($contactno)  || empty($address)    ||
            empty($email)      || empty($role)       ||
            empty($department)
        ) {
            echo json_encode(['success' => false, 'message' => 'All required fields must be completed.']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
            return;
        }

        if (!ctype_digit($contactno)) {
            echo json_encode(['success' => false, 'message' => 'Contact number must contain numbers only.']);
            return;
        }

        if (strlen($contactno) !== 11) {
            echo json_encode(['success' => false, 'message' => 'Contact number must be exactly 11 digits.']);
            return;
        }

        if ($this->User_model->email_exists($email)) {
            echo json_encode(['success' => false, 'message' => 'Email already exists.']);
            return;
        }

        if ($this->User_model->full_name_exists($firstname, $lastname)) {
            echo json_encode(['success' => false, 'message' => 'This name combination already exists.']);
            return;
        }

        $password = !empty($password) ? $password : 'rms-2026';

        $data = [
            'employee_id' => $employee_id,
            'firstname'   => $firstname,
            'lastname'    => $lastname,
            'birthday'    => $birthday,
            'address'     => $address,
            'contactno'   => $contactno,
            'email'       => $email,
            'password'    => password_hash($password, PASSWORD_DEFAULT),
            'role'        => $role,
            'is_active'   => $is_active,
            'job_title'   => $job_title,
            'department'  => $department,
            'created_at'  => date('Y-m-d H:i:s')
        ];

        $insert = $this->User_model->insert($data);

        echo json_encode([
            'success' => $insert,
            'message' => $insert ? 'User created successfully.' : 'Database insert failed.'
        ]);
    }

    public function update()
    {
        if ($this->session->userdata('role') !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $id          = trim($this->input->post('id',          TRUE));
        $firstname   = trim($this->input->post('firstname',   TRUE));
        $lastname    = trim($this->input->post('lastname',    TRUE));
        $employee_id = trim($this->input->post('employee_id', TRUE));
        $birthday    = trim($this->input->post('birthday',    TRUE));
        $contactno   = trim($this->input->post('contactno',   TRUE));
        $address     = trim($this->input->post('address',     TRUE));
        $email       = trim($this->input->post('email',       TRUE));
        $role        = trim($this->input->post('role',        TRUE));
        $is_active   = trim($this->input->post('is_active',   TRUE));
        $job_title   = trim($this->input->post('job_title',   TRUE));
        $department  = trim($this->input->post('department',  TRUE));

        if (
            empty($firstname) || empty($lastname)    ||
            empty($employee_id) || empty($birthday)  ||
            empty($contactno)  || empty($address)    ||
            empty($email)      || empty($role)       ||
            empty($department)
        ) {
            echo json_encode(['success' => false, 'message' => 'All required fields must be completed.']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
            return;
        }

        if (!ctype_digit($contactno)) {
            echo json_encode(['success' => false, 'message' => 'Contact number must contain numbers only.']);
            return;
        }

        if (strlen($contactno) !== 11) {
            echo json_encode(['success' => false, 'message' => 'Contact number must be exactly 11 digits.']);
            return;
        }

        if ($this->User_model->full_name_exists($firstname, $lastname, $id)) {
            echo json_encode(['success' => false, 'message' => 'This name combination already exists.']);
            return;
        }

        if ($this->User_model->email_exists($email, $id)) {
            echo json_encode(['success' => false, 'message' => 'Email already exists.']);
            return;
        }

        $data = [
            'firstname'   => $firstname,
            'lastname'    => $lastname,
            'employee_id' => $employee_id,
            'birthday'    => $birthday,
            'contactno'   => $contactno,
            'address'     => $address,
            'email'       => $email,
            'role'        => $role,
            'is_active'   => $is_active,
            'job_title'   => $job_title,
            'department'  => $department,
            'updated_at'  => date('Y-m-d H:i:s')
        ];

        $update = $this->User_model->update($id, $data);

        echo json_encode([
            'success' => $update,
            'message' => $update ? 'User updated successfully.' : 'Update failed.'
        ]);
    }

    public function delete($id)
    {
        if ($this->session->userdata('role') !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $logged_user_id = $this->session->userdata('user_id');

        if ((int)$id === (int)$logged_user_id) {
            echo json_encode(['success' => false, 'message' => 'You cannot delete your own account.']);
            return;
        }

        $delete = $this->User_model->soft_delete($id);
        echo json_encode(['success' => $delete]);
    }
}