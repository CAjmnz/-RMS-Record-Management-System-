<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity_log_model extends CI_Model
{
    protected $table = 'activity_logs';

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

    public function get_recent($limit = 10)
    {
        return $this->db
            ->order_by('created_at', 'DESC')
            ->limit((int) $limit)
            ->get($this->table)
            ->result();
    }

    // All logs for DataTables
    public function get_all()
    {
        return $this->db
            ->order_by('created_at', 'DESC')
            ->get($this->table)
            ->result();
    }

    // Log counts per day for the last N days (bar chart)
    public function get_daily_counts($days = 7)
    {
        $results = $this->db
            ->select('DATE(created_at) as log_date, COUNT(*) as total')
            ->where('created_at >=', date('Y-m-d', strtotime("-{$days} days")))
            ->group_by('DATE(created_at)')
            ->order_by('log_date', 'ASC')
            ->get($this->table)
            ->result();

        // Build a full date range so missing days show as 0
        $map = [];
        foreach ($results as $row) {
            $map[$row->log_date] = (int) $row->total;
        }

        $labels = [];
        $counts = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date     = date('Y-m-d', strtotime("-{$i} days"));
            $labels[] = date('M d', strtotime($date));
            $counts[] = isset($map[$date]) ? $map[$date] : 0;
        }

        return ['labels' => $labels, 'counts' => $counts];
    }
}