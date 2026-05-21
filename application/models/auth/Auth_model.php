<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {

    protected $table = 'users';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Fetch user by email.
     * Login uses email not username.
     */
    public function get_by_email($email)
    {
        $this->db->where('email', $email);
        $query = $this->db->get($this->table);
        return $query->row();
    }

    /**
     * Fetch user by ID.
     * Used by RMS_Controller to load current user.
     */
    public function get_by_id($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get($this->table);
        return $query->row();
    }
}