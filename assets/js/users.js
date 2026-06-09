<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends RMS_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('form_validation');
        $this->load->library('hashids');
    }

    public function index()
    {
        $this->data['role'] = $this->session->userdata('role');
        $this->data['logged_user_id'] = $this->session->userdata('user_id');
        $this->data['csrf_token_name'] = $this->security->get_csrf_token_name();
        $this->data['csrf_token_value'] = $this->security->get_csrf_hash();
        $this->load->view('users/index', $this->data);
    }

    // ─────────────────────────────
    // CREATE USER
    // ─────────────────────────────
    public function store()
    {
        if ($this->session->userdata('role') !== 'admin') {
            return $this->jsonFail('Unauthorized.');
        }

        $firstname   = trim($this->input->post('firstname', TRUE));
        $lastname    = trim($this->input->post('lastname', TRUE));
        $employee_id = trim($this->input->post('employee_id', TRUE));
        $birthday    = trim($this->input->post('birthday', TRUE));
        $contactno   = trim($this->input->post('contactno', TRUE));
        $address     = trim($this->input->post('address', TRUE));
        $email       = trim($this->input->post('email', TRUE));
        $password    = $this->input->post('password', FALSE);
        $role        = trim($this->input->post('role', TRUE));
        $is_active   = trim($this->input->post('is_active', TRUE));
        $job_title   = trim($this->input->post('job_title', TRUE));
        $department  = trim($this->input->post('department', TRUE));

        $errors = [];

        // REQUIRED FIELDS
        if ($firstname === '') $errors['firstname'] = 'First Name is required.';
        if ($lastname === '')  $errors['lastname']  = 'Last Name is required.';
        if ($employee_id === '') $errors['employee_id'] = 'Employee ID is required.';


// 📅 BIRTHDAY STRICT (NO FUTURE DATE)
if ($birthday === '') {
    $errors['birthday'] = 'Birthday is required.';
} else {
    $today = date('Y-m-d');

    if ($birthday > $today) {
        $errors['birthday'] = 'Birthday cannot be in the future';
    }
}

        // 📞 CONTACT STRICT 11 DIGITS
        if ($contactno === '') {
            $errors['contactno'] = 'Contact Number is required.';
        } elseif (!preg_match('/^[0-9]{11}$/', $contactno)) {
            $errors['contactno'] = 'Contact number must be exactly 11 digits';
        }

        if ($address === '') $errors['address'] = 'Address is required.';

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email is required.';
        }

        if ($job_title === '') $errors['job_title'] = 'Job Title is required.';
        if ($department === '') $errors['department'] = 'Department is required.';

        if (!empty($errors)) {
            return $this->jsonFail('Validation failed.', $errors);
        }

        // DUPLICATES
        if ($this->User_model->full_name_exists($firstname, $lastname)) {
            $errors['firstname'] = 'Name already exists.';
        }

        if ($this->User_model->email_exists($email)) {
            $errors['email'] = 'Email already exists.';
        }

        if ($this->User_model->employee_id_exists($employee_id)) {
            $errors['employee_id'] = 'Employee ID already exists.';
        }

        if (!empty($errors)) {
            return $this->jsonFail('Validation failed.', $errors);
        }

        // 🖼️ UPLOAD (STRICT 1MB)
        $upload = $this->upload_profile_picture();
        if (is_array($upload)) {
            return $this->jsonFail('Upload failed.', [
                'profile_picture' => $upload['error']
            ]);
        }

        // 🔐 PASSWORD RULE
        $plainPassword = !empty($password) ? $password : 'rms-2026';
        $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

        $this->User_model->insert([
            'employee_id' => $employee_id,
            'firstname'   => $firstname,
            'lastname'    => $lastname,
            'birthday'    => $birthday,
            'contactno'   => $contactno,
            'address'     => $address,
            'email'       => strtolower($email),
            'password'    => $hashedPassword,
            'role'        => $role,
            'is_active'   => (int)$is_active,
            'job_title'   => $job_title,
            'department'  => $department,
            'profile_picture' => $upload,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s')
        ]);

        return $this->jsonSuccess('User created successfully.');
    }

    // ─────────────────────────────
    // UPDATE USER
    // ─────────────────────────────
    public function update()
    {
        if ($this->session->userdata('role') !== 'admin') {
            return $this->jsonFail('Unauthorized.');
        }

        $id = (int)$this->hashids->decode($this->input->post('id'));

        if (!$id) return $this->jsonFail('Invalid ID.');

        $firstname   = trim($this->input->post('firstname', TRUE));
        $lastname    = trim($this->input->post('lastname', TRUE));
        $employee_id = trim($this->input->post('employee_id', TRUE));
        $birthday    = trim($this->input->post('birthday', TRUE));
        $contactno   = trim($this->input->post('contactno', TRUE));
        $address     = trim($this->input->post('address', TRUE));
        $email       = trim($this->input->post('email', TRUE));
        $role        = trim($this->input->post('role', TRUE));
        $is_active   = trim($this->input->post('is_active', TRUE));
        $job_title   = trim($this->input->post('job_title', TRUE));
        $department  = trim($this->input->post('department', TRUE));

        $errors = [];

        if ($birthday > date('Y-m-d')) {
            $errors['birthday'] = 'Birthday cannot be in the future';
        }

        if (!preg_match('/^[0-9]{11}$/', $contactno)) {
            $errors['contactno'] = 'Contact number must be exactly 11 digits';
        }

        if (!empty($errors)) {
            return $this->jsonFail('Validation failed.', $errors);
        }

        $upload = $this->upload_profile_picture();

        $data = [
            'firstname'  => $firstname,
            'lastname'   => $lastname,
            'employee_id'=> $employee_id,
            'birthday'   => $birthday,
            'contactno'  => $contactno,
            'address'    => $address,
            'email'      => strtolower($email),
            'role'       => $role,
            'is_active'  => (int)$is_active,
            'job_title'  => $job_title,
            'department' => $department,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($upload && !is_array($upload)) {
            $data['profile_picture'] = $upload;
        }

        $this->User_model->update($id, $data);

        return $this->jsonSuccess('User updated successfully.');
    }

    // ─────────────────────────────
    // UPLOAD (STRICT)
    // ─────────────────────────────
    private function upload_profile_picture()
    {
        if (empty($_FILES['profile_picture']['name'])) return null;

        if ($_FILES['profile_picture']['size'] > 1024 * 1024) {
            return ['error' => 'Profile picture must not exceed 1MB'];
        }

        $config['upload_path']   = FCPATH . 'uploads/profile_pictures/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size']      = 1024;
        $config['encrypt_name']  = TRUE;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('profile_picture')) {
            return ['error' => $this->upload->display_errors('', '')];
        }

        return 'uploads/profile_pictures/' . $this->upload->data('file_name');
    }

    private function jsonFail($msg, $errors = [])
    {
        echo json_encode(['success'=>false,'message'=>$msg,'errors'=>$errors]);
        exit;
    }

    private function jsonSuccess($msg)
    {
        echo json_encode(['success'=>true,'message'=>$msg]);
        exit;
    }
}