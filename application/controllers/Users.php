<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends RMS_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('form_validation'); 
    }

    public function index()
    {
        $role           = $this->session->userdata('role');
        $logged_user_id = $this->session->userdata('user_id');
        $filter         = $this->input->get('filter');

        $filters = [];

        if ($role === 'user') $filters['hide_admins'] = true;
        if ($filter === 'admins')    $filters['role']      = 'admin';
        if ($filter === 'nonadmins') $filters['role_not']  = 'admin';
        if ($filter === 'active')    $filters['is_active'] = 1;
        if ($filter === 'inactive')  $filters['is_active'] = 0;

        $this->data['users']          = $this->User_model->get_all($filters);
        $this->data['role']           = $role;
        $this->data['logged_user_id'] = $logged_user_id;

        $this->data['csrf_token_name']  = $this->security->get_csrf_token_name();
        $this->data['csrf_token_value'] = $this->security->get_csrf_hash();

        $this->load->view('users/index', $this->data);
    }

    public function get($id)
    {
        $user = $this->User_model->get_by_id($id);

        if (!$user) {
            return $this->jsonFail('User not found');
        }

        return $this->jsonSuccess('OK', $user);
    }

    public function store()
{
    if ($this->session->userdata('role') !== 'admin') {
        return $this->jsonFail('Unauthorized');
    }

    $data = [
        'firstname'   => trim($this->input->post('firstname',   TRUE)),
        'lastname'    => trim($this->input->post('lastname',    TRUE)),
        'employee_id' => trim($this->input->post('employee_id', TRUE)),
        'birthday'    => trim($this->input->post('birthday',    TRUE)),
        'contactno'   => trim($this->input->post('contactno',   TRUE)),
        'address'     => trim($this->input->post('address',     TRUE)),
        'email'       => trim($this->input->post('email',       TRUE)),
        'password'    => $this->input->post('password', FALSE),
        'role'        => trim($this->input->post('role',        TRUE)),
        'is_active'   => trim($this->input->post('is_active',   TRUE)),
        'job_title'   => trim($this->input->post('job_title',   TRUE)),
        'department'  => trim($this->input->post('department',  TRUE)),
    ];

$this->form_validation->set_rules('firstname', 'First Name', 'required|trim');
$this->form_validation->set_rules('lastname', 'Last Name', 'required|trim');
$this->form_validation->set_rules('employee_id', 'Employee ID', 'required|trim');
$this->form_validation->set_rules('birthday', 'Birthday', 'required');
$this->form_validation->set_rules('contactno', 'Contact Number', 'required|numeric|exact_length[11]');
$this->form_validation->set_rules('address', 'Address', 'required|trim');
$this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
$this->form_validation->set_rules('role', 'Role', 'required');
$this->form_validation->set_rules('department', 'Department', 'required');
$this->form_validation->set_rules('job_title', 'Job Title', 'required');

if ($this->form_validation->run() === FALSE) {
    return $this->jsonFail('Validation failed', $this->form_validation->error_array());
}
   
    // DEFAULT PASSWORD
    $password = !empty($data['password']) ? $data['password'] : 'rms-2026';

    $insert = $this->User_model->insert([
        'employee_id' => $data['employee_id'],
        'firstname'   => $data['firstname'],
        'lastname'    => $data['lastname'],
        'birthday'    => $data['birthday'],
        'address'     => $data['address'],
        'contactno'   => $data['contactno'],
        'email'       => $data['email'],
        'password'    => password_hash($password, PASSWORD_DEFAULT),
        'role'        => $data['role'],
        'is_active'   => $data['is_active'],
        'job_title'   => $data['job_title'],
        'department'  => $data['department'],

        // 🔥 IMPORTANT NEW FIELD
        'must_change_password' => 1,

        'created_at'  => date('Y-m-d H:i:s')
    ]);

    if (!$insert) return $this->jsonFail('Database insert failed.');

    return $this->jsonSuccess('User created successfully.');
}

    public function update()
    {
        if ($this->session->userdata('role') !== 'admin') {
            return $this->jsonFail('Unauthorized');
        }

        $id = trim($this->input->post('id', TRUE));

        $data = [
            'firstname'   => trim($this->input->post('firstname',   TRUE)),
            'lastname'    => trim($this->input->post('lastname',    TRUE)),
            'employee_id' => trim($this->input->post('employee_id', TRUE)),
            'birthday'    => trim($this->input->post('birthday',    TRUE)),
            'contactno'   => trim($this->input->post('contactno',   TRUE)),
            'address'     => trim($this->input->post('address',     TRUE)),
            'email'       => trim($this->input->post('email',       TRUE)),
            'role'        => trim($this->input->post('role',        TRUE)),
            'is_active'   => trim($this->input->post('is_active',   TRUE)),
            'job_title'   => trim($this->input->post('job_title',   TRUE)),
            'department'  => trim($this->input->post('department',  TRUE)),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $this->form_validation->set_rules('firstname', 'First Name', 'required|trim');
$this->form_validation->set_rules('lastname', 'Last Name', 'required|trim');
$this->form_validation->set_rules('employee_id', 'Employee ID', 'required|trim');
$this->form_validation->set_rules('birthday', 'Birthday', 'required');
$this->form_validation->set_rules('contactno', 'Contact Number', 'required|numeric|exact_length[11]');
$this->form_validation->set_rules('address', 'Address', 'required|trim');
$this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
$this->form_validation->set_rules('role', 'Role', 'required');
$this->form_validation->set_rules('department', 'Department', 'required');
$this->form_validation->set_rules('job_title', 'Job Title', 'required');

if ($this->form_validation->run() === FALSE) {
    return $this->jsonFail('Validation failed', $this->form_validation->error_array());
}

$update = $this->User_model->update($id, $data);

if (!$update) {
    return $this->jsonFail('Update failed.');
}

return $this->jsonSuccess('User updated successfully.'); 
}

    public function delete($id)
    {
        if ($this->session->userdata('role') !== 'admin') {
            return $this->jsonFail('Unauthorized');
        }

        $logged_user_id = $this->session->userdata('user_id');

        if ((int)$id === (int)$logged_user_id) {
            return $this->jsonFail('You cannot delete your own account.');
        }

        $delete = $this->User_model->soft_delete($id);

        if (!$delete) return $this->jsonFail('Delete failed.');

        return $this->jsonSuccess('User deleted.');
    }

    private function jsonFail($message, $errors = [])
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $message,
            'errors'  => $errors
        ]);
        exit;
    }

    private function jsonSuccess($message, $data = null)
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data'    => $data
        ]);
        exit;
    }
}