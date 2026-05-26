<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    // ─── Fetch all non-deleted users ──────────────────────────────────
    public function get_all($filters = [])
    {
        $this->db->where('deleted_at IS NULL', null, false);

        // Rule 3: hide admins from regular users
        if (!empty($filters['hide_admins'])) {
            $this->db->where('role !=', 'admin');
        }

        return $this->db
            ->order_by('id', 'DESC')
            ->get('users')
            ->result();
    }

    // ─── CRUD ─────────────────────────────────────────────────────────
    public function get_by_id($id)
    {
        return $this->db->get_where('users', ['id' => $id])->row();
    }

    public function email_exists($email, $exclude_id = null)
    {
        $this->db->where('email', $email)
                 ->where('deleted_at IS NULL', null, false);

        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }

        return $this->db->count_all_results('users') > 0;
    }

    // Rule 1: case-insensitive full name combo check
    public function full_name_exists($firstname, $lastname, $exclude_id = null)
    {
        $this->db
            ->where('LOWER(firstname)', strtolower($firstname))
            ->where('LOWER(lastname)', strtolower($lastname))
            ->where('deleted_at IS NULL', null, false);

        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }

        return $this->db->count_all_results('users') > 0;
    }

    public function insert($data)
    {
        return $this->db->insert('users', $data);
    }

    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update('users', $data);
    }

    public function soft_delete($id)
    {
        return $this->db->where('id', $id)->update('users', [
            'deleted_at' => date('Y-m-d H:i:s')
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
}