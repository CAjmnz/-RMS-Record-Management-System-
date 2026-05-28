<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'core/MY_Controller.php';

class Dashboard extends RMS_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Activity_log_model');
    }

    public function index()
    {
        $this->data['title']     = 'Dashboard — RMS';
        $this->data['firstname'] = $this->session->userdata('firstname');
        $this->data['lastname']  = $this->session->userdata('lastname');
        $this->data['email']     = $this->session->userdata('email');
        $this->data['role']      = $this->session->userdata('role');

        $stats               = $this->User_model->get_stats();
        $this->data['stats'] = $stats;

        // All logs for DataTable
        $this->data['logs'] = $this->Activity_log_model->get_all();

        // Bar chart — logs per day
        $daily                          = $this->Activity_log_model->get_daily_counts(7);
        $this->data['chart_log_labels'] = json_encode($daily['labels']);
        $this->data['chart_log_counts'] = json_encode($daily['counts']);

        // Donut 1 — Active vs Inactive
        $this->data['chart_status_data'] = json_encode([
            (int) $stats->active,
            (int) $stats->inactive,
        ]);

        // Donut 2 — Admins vs Regular Users
        $regular = (int) $stats->total - (int) $stats->admins;
        $this->data['chart_role_data'] = json_encode([
            (int) $stats->admins,
            $regular,
        ]);

        $this->load->view('dashboard/index', $this->data);
    }
}