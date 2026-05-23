<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    protected $table = 'users';

    /**
     * Get all non-deleted users.
     *
     * @return array
     */
    public function get_all()
    {
        return $this->db
            ->where('deleted_at', NULL)
            ->get($this->table)
            ->result();
    }

    /**
     * Insert new user — returns new row ID or FALSE.
     *
     * @param  array $data
     * @return int|false
     */
    public function insert($data)
    {
        $this->db->insert($this->table, $data);

        if ($this->db->affected_rows() < 1) {
            return FALSE;
        }

        return $this->db->insert_id();
    }

    /**
     * Get user by ID (non-deleted only).
     *
     * @param  int $id
     * @return object|null
     */
    public function get_by_id($id)
    {
        return $this->db
            ->where('id', $id)
            ->where('deleted_at', NULL)
            ->get($this->table)
            ->row();
    }

    /**
     * Update user by ID.
     *
     * @param  int   $id
     * @param  array $data
     * @return bool
     */
    public function update($id, $data)
    {
        return $this->db
            ->where('id', $id)
            ->update($this->table, $data);
    }

    /**
     * Dashboard analytics — counts by state/role.
     *
     * @return object
     */
    public function get_stats()
    {
        $t = $this->table;

        $total = $this->db
            ->where('deleted_at', NULL)
            ->count_all_results($t);

        $active = $this->db
            ->where('deleted_at', NULL)
            ->where('is_active', 1)
            ->count_all_results($t);

        $inactive = $this->db
            ->where('deleted_at', NULL)
            ->where('is_active', 0)
            ->count_all_results($t);

        $admins = $this->db
            ->where('deleted_at', NULL)
            ->where('role', 'admin')
            ->count_all_results($t);

        $regular = $this->db
            ->where('deleted_at', NULL)
            ->where('role', 'user')
            ->count_all_results($t);

        return (object) [
            'total'    => $total,
            'active'   => $active,
            'inactive' => $inactive,
            'admins'   => $admins,
            'regular'  => $regular,
        ];
    }

    /**
     * Get most recently created users.
     *
     * @param  int $limit
     * @return array
     */
    public function get_recent($limit = 5)
    {
        return $this->db
            ->where('deleted_at', NULL)
            ->order_by('created_at', 'DESC')
            ->limit($limit)
            ->get($this->table)
            ->result();
    }
}