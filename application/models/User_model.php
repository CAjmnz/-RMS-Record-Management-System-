<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    protected $table = 'users';

    public function get_all()
    {
        return $this->db->where('deleted_at', NULL)->get($this->table)->result();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->where('id', $id)
            ->where('deleted_at', NULL)
            ->get($this->table)
            ->row();
    }

    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    public function soft_delete($id)
    {
        return $this->db->where('id', $id)->update($this->table, [
            'deleted_at' => date('Y-m-d H:i:s'),
            'is_active'  => 0
        ]);
    }
    public function get_stats()
    {
        $stats = new stdClass();
        $stats->total    = $this->db->where('deleted_at', NULL)->count_all_results($this->table);
        $stats->active   = $this->db->where('deleted_at', NULL)->where('is_active', 1)->count_all_results($this->table);
        $stats->inactive = $this->db->where('deleted_at', NULL)->where('is_active', 0)->count_all_results($this->table);
        $stats->admins   = $this->db->where('deleted_at', NULL)->where('role', 'admin')->count_all_results($this->table);
        return $stats;
    }
}