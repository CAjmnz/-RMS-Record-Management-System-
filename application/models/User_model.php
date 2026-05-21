<?php
class User_model extends CI_Model {

    public function getByEmail($email) {
        return $this->db->get_where('users', ['email' => $email])->row();
    }

    public function getAllUsers() {
        return $this->db->get('users')->result();
    }

    public function countUsers() {
        return $this->db->count_all('users');
    }

    public function countAdmins() {
        return $this->db->where('is_admin', 1)->count_all_results('users');
    }

    public function countRegular() {
        return $this->db->where('is_admin', 0)->count_all_results('users');
    }
}