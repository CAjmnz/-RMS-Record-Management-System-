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

    // ───────────────────────────────
    // INDEX
    // ───────────────────────────────
    public function index()
    {
        $role           = $this->session->userdata('role');
        $logged_user_id = $this->session->userdata('user_id');

        $this->data['users']            = [];
        $this->data['role']             = $role;
        $this->data['logged_user_id']   = $logged_user_id;
        $this->data['csrf_token_name']  = $this->security->get_csrf_token_name();
        $this->data['csrf_token_value'] = $this->security->get_csrf_hash();

        $this->load->view('users/index', $this->data);
    }

    // ───────────────────────────────
    // GET USER (AJAX)
    // ───────────────────────────────
    public function get($id)
    {
        $id = $this->hashids->decode($id);
        if (!$id) return $this->jsonFail("Invalid ID.");

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

        $errors = [];

        if ($firstname   === '') $errors['firstname']   = 'First Name is required.';
        if ($lastname    === '') $errors['lastname']    = 'Last Name is required.';
        if ($employee_id === '') $errors['employee_id'] = 'Employee ID is required.';
        if ($birthday    === '') $errors['birthday']    = 'Birthday is required.';
        if ($contactno   === '') $errors['contactno']   = 'Contact Number is required.';
        elseif (!ctype_digit($contactno))      $errors['contactno'] = 'Contact Number must be numeric.';
        elseif (strlen($contactno) !== 11)     $errors['contactno'] = 'Contact Number must be exactly 11 digits.';
        if ($address     === '') $errors['address']     = 'Address is required.';
        if ($email       === '') $errors['email']       = 'Email is required.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email must be a valid email address.';
        if ($job_title   === '') $errors['job_title']   = 'Job Title is required.';
        if ($department  === '') $errors['department']  = 'Department is required.';

        if (empty($errors['birthday']) && $birthday !== '') {
            if ($birthday >= date('Y-m-d')) {
                $errors['birthday'] = 'Birthday cannot be today or a future date.';
            }
        }

        if (!empty($errors)) {
            return $this->jsonFail('Validation failed.', $errors);
        }

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

        // ── Upload (called ONCE) ──
        $upload = $this->upload_profile_picture();

        if (is_array($upload)) {
            return $this->jsonFail('Upload failed.', [
                'profile_picture' => $upload['error']
            ]);
        }

        $profile_picture = $upload ? $upload : null;
        $pass = (!empty($password)) ? $password : 'rms-2026';

        $insert = $this->User_model->insert([
            'employee_id'          => $employee_id,
            'firstname'            => $firstname,
            'lastname'             => $lastname,
            'birthday'             => $birthday,
            'address'              => $address,
            'contactno'            => $contactno,
            'email'                => strtolower($email),
            'password'             => password_hash($pass, PASSWORD_DEFAULT),
            'role'                 => $role,
            'is_active'            => (int) $is_active,
            'job_title'            => $job_title,
            'department'           => $department,
            'profile_picture'      => $profile_picture,
            'must_change_password' => 1,
            'created_at'           => date('Y-m-d H:i:s'),
            'updated_at'           => date('Y-m-d H:i:s'),
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

        $raw_id = trim($this->input->post('id', TRUE));
        $id     = (int) $this->hashids->decode($raw_id);
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

        $errors = [];

        if ($firstname   === '') $errors['firstname']   = 'First Name is required.';
        if ($lastname    === '') $errors['lastname']    = 'Last Name is required.';
        if ($employee_id === '') $errors['employee_id'] = 'Employee ID is required.';
        if ($birthday    === '') $errors['birthday']    = 'Birthday is required.';
        if ($contactno   === '') $errors['contactno']   = 'Contact Number is required.';
        elseif (!ctype_digit($contactno))      $errors['contactno'] = 'Contact Number must be numeric.';
        elseif (strlen($contactno) !== 11)     $errors['contactno'] = 'Contact Number must be exactly 11 digits.';
        if ($address     === '') $errors['address']     = 'Address is required.';
        if ($email       === '') $errors['email']       = 'Email is required.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email must be a valid email address.';
        if ($job_title   === '') $errors['job_title']   = 'Job Title is required.';
        if ($department  === '') $errors['department']  = 'Department is required.';

        if (empty($errors['birthday']) && $birthday !== '') {
            if ($birthday >= date('Y-m-d')) {
                $errors['birthday'] = 'Birthday cannot be today or a future date.';
            }
        }

        if (!empty($errors)) {
            return $this->jsonFail('Validation failed.', $errors);
        }

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

        // ── Upload (called ONCE) ──
        $upload = $this->upload_profile_picture();

        if (is_array($upload)) {
            return $this->jsonFail('Upload failed.', [
                'profile_picture' => $upload['error']
            ]);
        }

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

        if ($upload) {
            $data['profile_picture'] = $upload;
        }

        $updated = $this->User_model->update($id, $data);

        if (!$updated) {
            return $this->jsonFail('Update failed.');
        }

        return $this->jsonSuccess('User updated successfully.');
    }

    // ───────────────────────────────
    // RESET PASSWORD (AJAX)
    // ───────────────────────────────
    public function reset_password($id)
    {
        if ($this->session->userdata('role') !== 'admin') {
            return $this->jsonFail('Unauthorized.');
        }

        $id = $this->hashids->decode($id);
        if (!$id) return $this->jsonFail('Invalid ID.');

        $user = $this->User_model->get_by_id($id);

        if (!$user) {
            return $this->jsonFail('User not found.');
        }

        $updated = $this->User_model->reset_password($id);

        if (!$updated) {
            return $this->jsonFail('Password reset failed.');
        }

        return $this->jsonSuccess('Password has been reset to the default.');
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

        $id = $this->hashids->decode($id);
        if (!$id) return $this->jsonFail('Invalid ID.');

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
    // AJAX LIST (SERVER-SIDE DATATABLE)
    // ───────────────────────────────
    public function ajax_list()
    {
        // ── DataTables params ──
        $draw   = (int) $this->input->post('draw');
        $start  = (int) $this->input->post('start');
        $length = (int) $this->input->post('length');
        if ($length <= 0) $length = 10;

        $searchArr = $this->input->post('search');
        $search    = isset($searchArr['value']) ? trim($searchArr['value']) : '';

        $orderArr         = $this->input->post('order');
        $orderColumnIndex = isset($orderArr[0]['column']) ? (int) $orderArr[0]['column'] : 5;
        $orderDir         = isset($orderArr[0]['dir'])    ? $orderArr[0]['dir']          : 'desc';

        // ── Filter params from custom dropdowns ──
        $filterRole   = $this->input->post('role')   ?: null;
        $filterStatus = $this->input->post('status');
        $filterStatus = ($filterStatus !== null && $filterStatus !== '') ? $filterStatus : null;
        $filterDate   = $this->input->post('date')   ?: null;
        $filterDept   = $this->input->post('dept')   ?: null;

        $sessionRole = $this->session->userdata('role');

        // Non-admin users must never see admin accounts
        $hideAdmins = ($sessionRole !== 'admin');
        // ── Column index → DB column mapping ──
        // <thead> column order (profile_picture is merged into the User cell in JS):
        // 0:User(avatar+name+email)  1:role  2:status  3:contact
        // 4:address  5:created  6:department  7:birthday  8:actions
        $columnMap = [
            0 => 'firstname',   // User col — sort by firstname
            1 => 'role',
            2 => 'is_active',
            3 => 'contactno',
            4 => 'address',
            5 => 'created_at',
            6 => 'department',
            7 => 'birthday',
            8 => 'id',          // actions — not sortable, fallback to id
        ];

        $orderColumn = isset($columnMap[$orderColumnIndex])
            ? $columnMap[$orderColumnIndex]
            : 'created_at';

        // ── Query ──
        $result = $this->User_model->get_datatable(
            $start,
            $length,
            $search,
            $orderColumn,
            $orderDir,
            $filterRole,
            $filterStatus,
            $filterDate,
            $filterDept,
            $hideAdmins
        );

        // ── Build rows ──
        $data = [];

        foreach ($result['data'] as $row) {

            // Profile picture
            if (!empty($row->profile_picture)) {
                $profile = '<img src="' . base_url($row->profile_picture) . '" '
                         . 'class="profile-avatar" alt="avatar">';
            } else {
                $initial = strtoupper(substr($row->firstname, 0, 1));
                $profile = '<div class="profile-initials-sm">' . $initial . '</div>';
            }

            // Hash the real DB id — never expose raw integers in the DOM
            $hashed_id = $this->hashids->encode($row->id);

            // Actions — always present in structure; empty string for non-admins
            $actions = '';
            if ($sessionRole === 'admin') {
                $deleteBtn = '';
                if ((int) $row->id !== (int) $this->session->userdata('user_id')) {
                    $deleteBtn = '<button class="dropdown-item text-danger btn-delete" '
                               . 'data-id="' . $hashed_id . '">Delete</button>';
                }

                $actions = '
                    <div class="rms-dropdown">
                        <button class="btn btn-light btn-sm rms-dropdown-toggle" type="button">&#8942;</button>
                        <div class="rms-dropdown-menu">
                            <button class="dropdown-item btn-edit" data-id="' . $hashed_id . '">Edit</button>
                            <button class="dropdown-item btn-reset-password" data-id="' . $hashed_id . '">Reset Password</button>
                            ' . $deleteBtn . '
                        </div>
                    </div>';
            }

            $data[] = [
                'profile_picture' => $profile,
                'user'            => '<div><strong>' . htmlspecialchars($row->firstname . ' ' . $row->lastname)
                                   . '</strong><br><small>' . htmlspecialchars($row->email) . '</small></div>',
                'role'            => ucfirst($row->role),
                'status'          => $row->is_active
                                   ? '<span class="badge badge-success">Active</span>'
                                   : '<span class="badge badge-secondary">Inactive</span>',
                'contact'         => htmlspecialchars($row->contactno ?? ''),
                'address'         => htmlspecialchars($row->address   ?? ''),
                'created'         => date('M d, Y h:i A', strtotime($row->created_at)),
                'department'      => htmlspecialchars($row->department ?? ''),
                'birthday'        => !empty($row->birthday) ? date('M d, Y', strtotime($row->birthday)) : '',
                'actions'         => $actions,
            ];
        }

        header('Content-Type: application/json');
        echo json_encode([
            'draw'            => $draw,
            'recordsTotal'    => (int) ($result['total']    ?? 0),
            'recordsFiltered' => (int) ($result['filtered'] ?? 0),
            'data'            => $data,
        ]);
        exit;
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
    // PROFILE PICTURE UPLOAD
    // ───────────────────────────────
    private function upload_profile_picture()
    {
        if (empty($_FILES['profile_picture']['name'])) {
            return null;
        }

        $config = [
            'upload_path'   => FCPATH . 'uploads/profile_pictures/',
            'allowed_types' => 'jpg|jpeg|png|webp',
            'max_size'      => 2048,
            'encrypt_name'  => TRUE,
        ];

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('profile_picture')) {
            return ['error' => $this->upload->display_errors('', '')];
        }

        $uploadData = $this->upload->data();
        return 'uploads/profile_pictures/' . $uploadData['file_name'];
    }
}