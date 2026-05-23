<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity_log_model extends CI_Model
{
    protected $table = 'activity_logs';

    /**
     * Insert an activity log entry.
     *
     * @param  array $data  Keys: user_id, action, description
     * @return bool
     */
    public function log(array $data)
    {
        if (empty($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        $this->db->insert($this->table, $data);

        if ($this->db->affected_rows() < 1) {
            log_message('error',
                'Activity_log_model::log() failed — data: ' . json_encode($data)
            );
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Get recent activity logs.
     *
     * @param  int $limit
     * @return array
     */
    public function get_recent($limit = 10)
    {
        return $this->db
            ->order_by('created_at', 'DESC')
            ->limit((int) $limit)
            ->get($this->table)
            ->result();
    }
}