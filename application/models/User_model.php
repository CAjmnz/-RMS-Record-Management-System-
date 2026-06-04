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
}