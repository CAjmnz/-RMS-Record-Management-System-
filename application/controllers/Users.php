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

    // ───────────────────────────────
    // INDEX
    // ───────────────────────────────
    public function index()
    {
        $role           = $this->session->userdata('role');
        $logged_user_id = $this->session->userdata('user_id');

        $filters = [];
        if ($role === 'user') $filters['hide_admins'] = true;

        $this->data['users']          = $this->User_model->get_all($filters);
        $this->data['role']           = $role;
        $this->data['logged_user_id'] = $logged_user_id;
        $this->data['csrf_token_name']  = $this->security->get_csrf_token_name();
        $this->data['csrf_token_value'] = $this->security->get_csrf_hash();

        $this->load->view('users/index', $this->data);
    }

    // ───────────────────────────────
    // GET USER (AJAX)
    // ───────────────────────────────
    public function get($id)
    {
        $user = $this->User_model->get_by_id($id);

        if (!$user) {
            return $this->jsonFail('User not found.');
        }

        unset($user->password);
        return $this->jsonSuccess('OK', $user);
    }

    // ───────────────────────────────
    // CREATE USER (AJAX)
    // ───────────────────────────────
    public function store()
    {
        if ($this->session->userdata('role') !== 'admin') {
            return $this->jsonFail('Unauthorized.');
        }

        // Collect inputs
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

        // ── Required field errors ──
        $errors = [];

        if ($firstname   === '') $errors['firstname']   = 'First Name is required.';
        if ($lastname    === '') $errors['lastname']    = 'Last Name is required.';
        if ($employee_id === '') $errors['employee_id'] = 'Employee ID is required.';
        if ($birthday    === '') $errors['birthday']    = 'Birthday is required.';
        if ($contactno   === '') $errors['contactno']   = 'Contact Number is required.';
        elseif (!ctype_digit($contactno))           $errors['contactno'] = 'Contact Number must be numeric.';
        elseif (strlen($contactno) !== 11)          $errors['contactno'] = 'Contact Number must be exactly 11 digits.';
        if ($address     === '') $errors['address']     = 'Address is required.';
        if ($email       === '') $errors['email']       = 'Email is required.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email must be a valid email address.';
        if ($job_title   === '') $errors['job_title']   = 'Job Title is required.';
        if ($department  === '') $errors['department']  = 'Department is required.';

        // ── Birthday: must be before today ──
        if (empty($errors['birthday']) && $birthday !== '') {
            if ($birthday >= date('Y-m-d')) {
                $errors['birthday'] = 'Birthday cannot be today or a future date.';
            }
        }

        if (!empty($errors)) {
            return $this->jsonFail('Validation failed.', $errors);
        }

        // ── Duplicate checks ──
        if ($this->User_model->full_name_exists($firstname, $lastname)) {
            $errors['firstname'] = 'This First Name + Last Name combination already exists.';
            $errors['lastname']  = 'This First Name + Last Name combination already exists.';
        }

        if ($this->User_model->email_exists($email)) {
            $errors['email'] = 'This email is already registered.';
        }

        if ($this->User_model->employee_id_exists($employee_id)) {
            $errors['employee_id'] = 'This Employee ID is already taken.';
        }

        if (!empty($errors)) {
            return $this->jsonFail('Validation failed.', $errors);
        }
        $upload = $this->upload_profile_picture();

if (is_array($upload)) {
    echo json_encode([
        'success' => false,
        'errors'  => ['profile_picture' => $upload['error']]
    ]);
    return;
}
        // ── Insert ──
        $upload = $this->upload_profile_picture();

        if (is_array($upload)) {
            return $this->jsonFail('Upload failed.', [
                'profile_picture' => $upload['error']
            ]);
        }
        
        // fallback if no file uploaded
        $profile_picture = $upload ? $upload : null;
        
        $pass = (!empty($password)) ? $password : 'rms-2026';
        
        $insert = $this->User_model->insert([
            'employee_id'         => $employee_id,
            'firstname'           => $firstname,
            'lastname'            => $lastname,
            'birthday'            => $birthday,
            'address'             => $address,
            'contactno'           => $contactno,
            'email'               => strtolower($email),
            'password'            => password_hash($pass, PASSWORD_DEFAULT),
            'role'                => $role,
            'is_active'           => (int) $is_active,
            'job_title'           => $job_title,
            'department'          => $department,
            'profile_picture'     => $profile_picture, // ✅ FIXED
            'must_change_password'=> 1,
            'created_at'          => date('Y-m-d H:i:s'),
            'updated_at'          => date('Y-m-d H:i:s'),
        ]);

        if (!$insert) {
            return $this->jsonFail('Database insert failed.');
        }

        return $this->jsonSuccess('User created successfully.');
    }

    // ───────────────────────────────
    // UPDATE USER (AJAX)
    // ───────────────────────────────
    public function update()
    {
        if ($this->session->userdata('role') !== 'admin') {
            return $this->jsonFail('Unauthorized.');
        }

        $id          = (int) trim($this->input->post('id',          TRUE));
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

        if (!$id) {
            return $this->jsonFail('Invalid user ID.');
        }

        // ── Required field errors ──
        $errors = [];

        if ($firstname   === '') $errors['firstname']   = 'First Name is required.';
        if ($lastname    === '') $errors['lastname']    = 'Last Name is required.';
        if ($employee_id === '') $errors['employee_id'] = 'Employee ID is required.';
        if ($birthday    === '') $errors['birthday']    = 'Birthday is required.';
        if ($contactno   === '') $errors['contactno']   = 'Contact Number is required.';
        elseif (!ctype_digit($contactno))           $errors['contactno'] = 'Contact Number must be numeric.';
        elseif (strlen($contactno) !== 11)          $errors['contactno'] = 'Contact Number must be exactly 11 digits.';
        if ($address     === '') $errors['address']     = 'Address is required.';
        if ($email       === '') $errors['email']       = 'Email is required.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email must be a valid email address.';
        if ($job_title   === '') $errors['job_title']   = 'Job Title is required.';
        if ($department  === '') $errors['department']  = 'Department is required.';

        // ── Birthday: must be before today ──
        if (empty($errors['birthday']) && $birthday !== '') {
            if ($birthday >= date('Y-m-d')) {
                $errors['birthday'] = 'Birthday cannot be today or a future date.';
            }
        }

        if (!empty($errors)) {
            return $this->jsonFail('Validation failed.', $errors);
        }

        // ── Duplicate checks (exclude self) ──
        if ($this->User_model->full_name_exists($firstname, $lastname, $id)) {
            $errors['firstname'] = 'This First Name + Last Name combination already exists.';
            $errors['lastname']  = 'This First Name + Last Name combination already exists.';
        }

        if ($this->User_model->email_exists($email, $id)) {
            $errors['email'] = 'This email is already registered.';
        }

        if ($this->User_model->employee_id_exists($employee_id, $id)) {
            $errors['employee_id'] = 'This Employee ID is already taken.';
        }

        if (!empty($errors)) {
            return $this->jsonFail('Validation failed.', $errors);
        }

        // ── Update ──
        $upload = $this->upload_profile_picture();

        if (is_array($upload)) {
            return $this->jsonFail('Upload failed.', [
                'profile_picture' => $upload['error']
            ]);
        }
        
        // build update data properly
        $data = [
            'firstname'   => $firstname,
            'lastname'    => $lastname,
            'employee_id' => $employee_id,
            'birthday'    => $birthday,
            'contactno'   => $contactno,
            'address'     => $address,
            'email'       => strtolower($email),
            'role'        => $role,
            'is_active'   => (int) $is_active,
            'job_title'   => $job_title,
            'department'  => $department,
            'updated_at'  => date('Y-m-d H:i:s'),
        ];
        
        // only update profile picture if new file uploaded
        if ($upload) {
            $data['profile_picture'] = $upload;
        }
        
        $updated = $this->User_model->update($id, $data);
        
        if (!$updated) {
            return $this->jsonFail('Update failed.');
        }
        
        return $this->jsonSuccess('User updated successfully.');

if (is_array($upload)) {
    echo json_encode([
        'success' => false,
        'errors'  => ['profile_picture' => $upload['error']]
    ]);
    return;
}

if ($upload) {
    $data['profile_picture'] = $upload;
}

    }

    // ───────────────────────────────
    // DELETE (AJAX)
    // ───────────────────────────────
    public function delete($id)
    {
        if ($this->session->userdata('role') !== 'admin') {
            return $this->jsonFail('Unauthorized.');
        }

        $logged_user_id = $this->session->userdata('user_id');

        if ((int) $id === (int) $logged_user_id) {
            return $this->jsonFail('You cannot delete your own account.');
        }

        $user = $this->User_model->get_by_id($id);
        if (!$user) {
            return $this->jsonFail('User not found.');
        }

        $deleted = $this->User_model->soft_delete($id);

        if (!$deleted) {
            return $this->jsonFail('Delete failed.');
        }

        return $this->jsonSuccess('User deleted successfully.');
    }

    // ───────────────────────────────
    // JSON HELPERS
    // ───────────────────────────────
    private function jsonFail($message, $errors = [])
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ]);
        exit;
    }

    private function jsonSuccess($message, $data = null)
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ]);
        exit;
    }
     // ───────────────────────────────
    // UPLOAD(PROFILE PICTURE)
    // ───────────────────────────────
    private function upload_profile_picture()
{
    if (empty($_FILES['profile_picture']['name'])) {
        return null;
    }

    $config['upload_path']   = './uploads/profile_pictures/';
    $config['allowed_types'] = 'jpg|jpeg|png|webp';
    $config['max_size']      = 2048; // 2MB
    $config['encrypt_name']  = TRUE;

    $this->load->library('upload', $config);

    if (!$this->upload->do_upload('profile_picture')) {
        return [
            'error' => $this->upload->display_errors('', '')
        ];
    }

    $data = $this->upload->data();

    return 'uploads/profile_pictures/' . $data['file_name'];
}
}