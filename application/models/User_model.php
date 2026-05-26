<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    public function get_paginated($limit, $offset)
    {
        return $this->db
            ->where('deleted_at IS NULL', null, false)
            ->order_by('id', 'DESC')
            ->get('users', $limit, $offset)
            ->result();
    }

    public function count_all()
    {
        return $this->db
            ->where('deleted_at IS NULL', null, false)
            ->count_all_results('users');
    }

    public function get_by_id($id)
    {
        return $this->db->get_where('users', ['id' => $id])->row();
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

    public function get_stats()
    {
        return (object)[
            'total' => $this->db->where('deleted_at IS NULL', null, false)->count_all_results('users'),
            'active' => $this->db->where('is_active', 1)->where('deleted_at IS NULL', null, false)->count_all_results('users'),
            'inactive' => $this->db->where('is_active', 0)->where('deleted_at IS NULL', null, false)->count_all_results('users'),
            'admins' => $this->db->where('role', 'admin')->where('deleted_at IS NULL', null, false)->count_all_results('users')
        ];
    }
}