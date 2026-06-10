<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    // ─── Fetch all non-deleted users ──────────────────────────────────

    public function get_all($filters = [])
    {
        $this->db->where('deleted_at IS NULL', null, false);

        if (!empty($filters['hide_admins'])) {
            $this->db->where('role !=', 'admin');
        }

        if (!empty($filters['role'])) {
            $this->db->where('role', $filters['role']);
        }

        if (!empty($filters['role_not'])) {
            $this->db->where('role !=', $filters['role_not']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $this->db->where('is_active', $filters['is_active']);
        }

        return $this->db
            ->order_by('id', 'DESC')
            ->get('users')
            ->result();
    }

    // ─── Single user ──────────────────────────────────────────────────

    public function get_by_id($id)
    {
        return $this->db
            ->where('id', $id)
            ->where('deleted_at IS NULL', null, false)
            ->get('users')
            ->row();
    }

    // ─── Duplicate checks ─────────────────────────────────────────────

    public function full_name_exists($firstname, $lastname, $exclude_id = null)
    {
        $this->db
            ->where('LOWER(firstname)', strtolower(trim($firstname)))
            ->where('LOWER(lastname)',  strtolower(trim($lastname)))
            ->where('deleted_at IS NULL', null, false);

        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }

        return $this->db->count_all_results('users') > 0;
    }

    public function email_exists($email, $exclude_id = null)
    {
        $this->db
            ->where('LOWER(email)', strtolower(trim($email)))
            ->where('deleted_at IS NULL', null, false);

        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }

        return $this->db->count_all_results('users') > 0;
    }

    public function employee_id_exists($employee_id, $exclude_id = null)
    {
        $this->db
            ->where('employee_id', trim($employee_id))
            ->where('deleted_at IS NULL', null, false);

        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }

        return $this->db->count_all_results('users') > 0;
    }

    // ─── CRUD ─────────────────────────────────────────────────────────

    public function insert($data)
    {
        $this->db->insert('users', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update('users', $data);
    }

    public function soft_delete($id)
    {
        return $this->db->where('id', $id)->update('users', [
            'deleted_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
    // ─── Reset password to default ────────────────────────────────────
    // Add this method to User_model.php
    public function reset_password($id)
    {
        $this->db->where('id', $id);
        $this->db->set('password',             password_hash('rms-2026', PASSWORD_DEFAULT));
        $this->db->set('must_change_password', 1);
        $this->db->set('password_reset_count', 'password_reset_count + 1', false); // false = no quotes, raw SQL
        $this->db->set('updated_at',           date('Y-m-d H:i:s'));
        return $this->db->update('users');
    }
    // ─── Stats (Dashboard) ────────────────────────────────────────────

    public function get_stats()
    {
        return (object)[
            'total'    => $this->db->where('deleted_at IS NULL', null, false)->count_all_results('users'),
            'active'   => $this->db->where('is_active', 1)->where('deleted_at IS NULL', null, false)->count_all_results('users'),
            'inactive' => $this->db->where('is_active', 0)->where('deleted_at IS NULL', null, false)->count_all_results('users'),
            'admins'   => $this->db->where('role', 'admin')->where('deleted_at IS NULL', null, false)->count_all_results('users'),
            'nonadmins'=> $this->db->where('role !=', 'admin')->where('deleted_at IS NULL', null, false)->count_all_results('users'),
        ];
    }

    public function get_recent($limit = 5)
    {
        return $this->db
            ->where('deleted_at IS NULL', null, false)
            ->order_by('created_at', 'DESC')
            ->get('users', $limit)
            ->result();
    }
    // ─── update (profile_picture) ────────────────────────────────────────────   
    public function update_profile_picture($id, $path)
{
    return $this->db->where('id', $id)->update('users', [
        'profile_picture' => $path,
        'updated_at'      => date('Y-m-d H:i:s')
    ]);
}
public function get_birth_year_counts()
{
    $rows = $this->db
        ->select('YEAR(birthday) as birth_year, COUNT(*) as total')
        ->where('deleted_at IS NULL', null, false)
        ->where('birthday IS NOT NULL', null, false)
        ->where('birthday !=', '0000-00-00')
        ->group_by('YEAR(birthday)')
        ->order_by('birth_year', 'ASC')
        ->get('users')
        ->result();

    $labels = [];
    $counts = [];

    foreach ($rows as $row) {
        if (!empty($row->birth_year)) {
            $labels[] = (string) $row->birth_year;
            $counts[]  = (int) $row->total;
        }
    }

    return ['labels' => $labels, 'counts' => $counts];
}
// ─── DATATABLE ────────────────────────────────────────────   
public function get_datatable($start, $length, $search, $orderColumn, $orderDir, $role = null, $status = null, $date = null, $dept = null)
{
    // =========================
    // BASE QUERY (REUSABLE BUILDER)
    // =========================
    $this->db->from('users');
    $this->db->where('deleted_at IS NULL', null, false);

    // =========================
    // TOTAL (NO FILTERS)
    // =========================
    $total = $this->db->count_all_results('', false);

    // =========================
    // APPLY FILTERS
    // =========================
    if (!empty($role)) {
        $this->db->where('role', $role);
    }

    if ($status !== null && $status !== '') {
        $this->db->where('is_active', $status);
    }

    if (!empty($date)) {
        $this->db->where('DATE(created_at)', $date);
    }

    if (!empty($dept)) {
        $this->db->like('department', $dept);
    }

    // =========================
    // SEARCH
    // =========================
    if (!empty($search)) {
        $this->db->group_start();
        $this->db->like('firstname', $search);
        $this->db->or_like('lastname', $search);
        $this->db->or_like('email', $search);
        $this->db->or_like('employee_id', $search);
        $this->db->group_end();
    }

    // =========================
    // FILTERED COUNT (SAFE CLONE FIX)
    // =========================
    $filtered_db = clone $this->db;
    $filtered = $filtered_db->count_all_results();

    // =========================
    // FINAL DATA QUERY
    // =========================
    $this->db->order_by($orderColumn, $orderDir);
    $this->db->limit($length, $start);

    $data = $this->db->get()->result();

    return [
        'total' => $total,
        'filtered' => $filtered,
        'data' => $data
    ];
}
public function saveDocument($data)
{
    return $this->db->insert('user_doucments', $data);
}
public function getDocuments($user_id)
{
    return $this->db
        ->where('user_id',$user_id)
        ->order_by('id','DESC')
        ->get('user_documents')
        ->result();
}
}