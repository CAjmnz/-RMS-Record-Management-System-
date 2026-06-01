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

        $errors = [];

        foreach ([
            'firstname','lastname','employee_id','birthday',
            'contactno','address','email','role','department','job_title'
        ] as $field) {
            if (empty($data[$field])) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
            }
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format.';
        }

        if (!empty($data['contactno']) &&
            (!ctype_digit($data['contactno']) || strlen($data['contactno']) !== 11)) {
            $errors['contactno'] = 'Must be exactly 11 digits.';
        }

        if (!empty($errors)) return $this->jsonFail('Validation failed', $errors);

        if ($this->User_model->employee_id_exists($data['employee_id'])) {
            return $this->jsonFail('Validation failed', [
                'employee_id' => 'Employee ID already exists.'
            ]);
        }

        if ($this->User_model->email_exists($data['email'])) {
            return $this->jsonFail('Validation failed', [
                'email' => 'Email already exists.'
            ]);
        }

        if ($this->User_model->full_name_exists($data['firstname'], $data['lastname'])) {
            return $this->jsonFail('Validation failed', [
                'firstname' => 'This name combination already exists.',
                'lastname'  => 'This name combination already exists.'
            ]);
        }

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

        $errors = [];

        foreach ([
            'firstname','lastname','employee_id','birthday',
            'contactno','address','email','role','department','job_title'
        ] as $field) {
            if (empty($data[$field])) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
            }
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format.';
        }

        if (!empty($data['contactno']) &&
            (!ctype_digit($data['contactno']) || strlen($data['contactno']) !== 11)) {
            $errors['contactno'] = 'Must be exactly 11 digits.';
        }

        if (!empty($errors)) return $this->jsonFail('Validation failed', $errors);

        if ($this->User_model->employee_id_exists($data['employee_id'], $id)) {
            return $this->jsonFail('Validation failed', [
                'employee_id' => 'Employee ID already exists.'
            ]);
        }

        if ($this->User_model->email_exists($data['email'], $id)) {
            return $this->jsonFail('Validation failed', [
                'email' => 'Email already exists.'
            ]);
        }

        if ($this->User_model->full_name_exists($data['firstname'], $data['lastname'], $id)) {
            return $this->jsonFail('Validation failed', [
                'firstname' => 'This name combination already exists.',
                'lastname'  => 'This name combination already exists.'
            ]);
        }

        $update = $this->User_model->update($id, $data);

        if (!$update) return $this->jsonFail('Update failed.');

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