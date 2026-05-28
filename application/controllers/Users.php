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
            echo json_encode([
                'success' => false,
                'message' => 'User not found'
            ]);
            return;
        }

        echo json_encode([
            'success' => true,
            'data'    => $user
        ]);
    }

    public function store()
    {
        // Admin only
        if ($this->session->userdata('role') !== 'admin') {
            echo json_encode([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
            return;
        }

        $firstname = trim($this->input->post('firstname'));
        $lastname  = trim($this->input->post('lastname'));
        $email     = trim($this->input->post('email'));
        $password  = $this->input->post('password');
        $role      = $this->input->post('role');

        // Required validation
        if (empty($firstname) || empty($email) || empty($password)) {
            echo json_encode([
                'success' => false,
                'message' => 'Firstname, email and password are required.'
            ]);
            return;
        }

        // Email format validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid email address.'
            ]);
            return;
        }

        // Duplicate email check
        if ($this->User_model->email_exists($email)) {
            echo json_encode([
                'success' => false,
                'message' => 'Email already exists.'
            ]);
            return;
        }

        // Duplicate full name check
        if ($this->User_model->full_name_exists($firstname, $lastname)) {
            echo json_encode([
                'success' => false,
                'message' => 'This name combination already exists.'
            ]);
            return;
        }

        // Role fallback
        if (empty($role)) {
            $role = 'user';
        }

        $data = [
            'employee_id' => $this->input->post('employee_id'),
            'firstname'   => $firstname,
            'lastname'    => $lastname,
            'birthday'    => $this->input->post('birthday'),
            'address'     => $this->input->post('address'),
            'contactno'   => $this->input->post('contactno'),
            'email'       => $email,
            'password'    => password_hash($password, PASSWORD_DEFAULT),
            'role'        => $role,
            'is_active'   => $this->input->post('is_active'),
            'job_title'   => $this->input->post('job_title'),
            'department'  => $this->input->post('department'),
            'created_at'  => date('Y-m-d H:i:s')
        ];

        $insert = $this->User_model->insert($data);

        if ($insert) {
            echo json_encode([
                'success' => true,
                'message' => 'User created successfully.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Database insert failed.'
            ]);
        }
    }

    public function update()
    {
        // Admin only
        if ($this->session->userdata('role') !== 'admin') {
            echo json_encode([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
            return;
        }

        $id        = $this->input->post('id');
        $firstname = trim($this->input->post('firstname'));
        $lastname  = trim($this->input->post('lastname'));
        $email     = trim($this->input->post('email'));

        // Duplicate full name check
        if ($this->User_model->full_name_exists($firstname, $lastname, $id)) {
            echo json_encode([
                'success' => false,
                'message' => 'This name combination already exists.'
            ]);
            return;
        }

        // Duplicate email check
        if ($this->User_model->email_exists($email, $id)) {
            echo json_encode([
                'success' => false,
                'message' => 'Email already exists.'
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
            'email'       => $email,
            'role'        => $this->input->post('role'),
            'is_active'   => $this->input->post('is_active'),
            'job_title'   => $this->input->post('job_title'),
            'department'  => $this->input->post('department'),
            'updated_at'  => date('Y-m-d H:i:s')
        ];

        $update = $this->User_model->update($id, $data);

        echo json_encode([
            'success' => $update
        ]);
    }

    public function delete($id)
    {
        // Admin only
        if ($this->session->userdata('role') !== 'admin') {
            echo json_encode([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
            return;
        }

        // Self-delete protection
        $logged_user_id = $this->session->userdata('user_id');

        if ((int)$id === (int)$logged_user_id) {
            echo json_encode([
                'success' => false,
                'message' => 'You cannot delete your own account.'
            ]);
            return;
        }

        $delete = $this->User_model->soft_delete($id);

        echo json_encode([
            'success' => $delete
        ]);
    }
}