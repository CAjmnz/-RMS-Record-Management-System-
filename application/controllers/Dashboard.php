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
    $this->data['title']           = 'Dashboard — RMS';
    $this->data['firstname']       = $this->session->userdata('firstname');
    $this->data['lastname']        = $this->session->userdata('lastname');
    $this->data['email']           = $this->session->userdata('email');
    $this->data['role']            = $this->session->userdata('role');
    $this->data['profile_picture'] = $this->session->userdata('profile_picture'); // ← add

    $stats               = $this->User_model->get_stats();
    $this->data['stats'] = $stats;

    $this->data['logs'] = $this->Activity_log_model->get_all();

    $daily                          = $this->Activity_log_model->get_daily_counts(7);
    $this->data['chart_log_labels'] = json_encode($daily['labels']);
    $this->data['chart_log_counts'] = json_encode($daily['counts']);

    $this->data['chart_status_data'] = json_encode([
        (int) $stats->active,
        (int) $stats->inactive,
    ]);

    $regular = (int) $stats->total - (int) $stats->admins;
    $this->data['chart_role_data'] = json_encode([
        (int) $stats->admins,
        $regular,
    ]);

    // Birth year chart
    $this->data['chart_birth_data'] = json_encode(
        $this->User_model->get_birth_year_counts()
    );

    $this->load->view('dashboard/index', $this->data);
}
}