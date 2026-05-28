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
    if ($this->session->userdata('role') !== 'admin') {
        echo json_encode([
            'success' => false,
            'message' => 'Unauthorized'
        ]);
        return;
    }

    $firstname = trim($this->input->post('firstname', TRUE));
    $lastname  = trim($this->input->post('lastname',  TRUE));
    $email     = trim($this->input->post('email',     TRUE));
    $password  = $this->input->post('password', FALSE);
    $role      = $this->input->post('role',     TRUE);

    if (empty($firstname) || empty($email)) {
        echo json_encode([
            'success' => false,
            'message' => 'Firstname and email are required.'
        ]);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email address.'
        ]);
        return;
    }

    if ($this->User_model->email_exists($email)) {
        echo json_encode([
            'success' => false,
            'message' => 'Email already exists.'
        ]);
        return;
    }

    if ($this->User_model->full_name_exists($firstname, $lastname)) {
        echo json_encode([
            'success' => false,
            'message' => 'This name combination already exists.'
        ]);
        return;
    }

    if (empty($role)) {
        $role = 'user';
    }

    // Default password if admin leaves it blank
    $password = !empty($password) ? $password : 'rms-2026';

    $data = [
        'employee_id' => $this->input->post('employee_id', TRUE),
        'firstname'   => $firstname,
        'lastname'    => $lastname,
        'birthday'    => $this->input->post('birthday',    TRUE),
        'address'     => $this->input->post('address',     TRUE),
        'contactno'   => $this->input->post('contactno',   TRUE),
        'email'       => $email,
        'password'    => password_hash($password, PASSWORD_DEFAULT),
        'role'        => $role,
        'is_active'   => $this->input->post('is_active',   TRUE),
        'job_title'   => $this->input->post('job_title',   TRUE),
        'department'  => $this->input->post('department',  TRUE),
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
        if ($this->session->userdata('role') !== 'admin') {
            echo json_encode([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
            return;
        }

        // XSS clean on all inputs via TRUE flag
        $id        = $this->input->post('id',        TRUE);
        $firstname = trim($this->input->post('firstname', TRUE));
        $lastname  = trim($this->input->post('lastname',  TRUE));
        $email     = trim($this->input->post('email',     TRUE));

        if ($this->User_model->full_name_exists($firstname, $lastname, $id)) {
            echo json_encode([
                'success' => false,
                'message' => 'This name combination already exists.'
            ]);
            return;
        }

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
            'employee_id' => $this->input->post('employee_id', TRUE),
            'birthday'    => $this->input->post('birthday',    TRUE),
            'contactno'   => $this->input->post('contactno',   TRUE),
            'address'     => $this->input->post('address',     TRUE),
            'email'       => $email,
            'role'        => $this->input->post('role',        TRUE),
            'is_active'   => $this->input->post('is_active',   TRUE),
            'job_title'   => $this->input->post('job_title',   TRUE),
            'department'  => $this->input->post('department',  TRUE),
            'updated_at'  => date('Y-m-d H:i:s')
        ];

        $update = $this->User_model->update($id, $data);

        echo json_encode([
            'success' => $update
        ]);
    }

    public function delete($id)
    {
        if ($this->session->userdata('role') !== 'admin') {
            echo json_encode([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
            return;
        }

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