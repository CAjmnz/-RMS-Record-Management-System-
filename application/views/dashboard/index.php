<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>


<!-- Page CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/users.css') ?>">
<!-- charts -->
<input type="hidden" id="chart_status_data" value='<?= $chart_status_data ?>'>
<input type="hidden" id="chart_role_data"   value='<?= $chart_role_data ?>'>
<input type="hidden" id="chart_log_labels"  value='<?= $chart_log_labels ?>'>
<input type="hidden" id="chart_log_counts"  value='<?= $chart_log_counts ?>'>
<!-- CSRF tokens -->

<div id="main-content">


    <?php if (!empty($flash_error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash_error) ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>


    <?php if (!empty($flash_success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash_success) ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>


    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div class="wb-text">
            <h2>
                Welcome back,
                <?= htmlspecialchars($firstname . ' ' . $lastname) ?>!
            </h2>
            <p><?= htmlspecialchars($email) ?></p>
            <span class="wb-badge"><?= ucfirst($role) ?></span>
        </div>
        <div class="wb-avatar">
            <?= strtoupper(substr($firstname, 0, 1) . substr($lastname, 0, 1)) ?>
        </div>
    </div>


    <!-- Stat Cards -->
    <div class="section-title">System Overview</div>


    <?php
        $stats = isset($stats) ? $stats : (object)[
            'total'    => 0,
            'active'   => 0,
            'inactive' => 0,
            'admins'   => 0,
            'nonadmins' => 0
        ];
    ?>
 
        <div class="col-xl-6  col-md-8">
            <div class="stat-card sc-total">
                <div class="stat-info">
                    <div class="stat-label">Total Users</div>
                    <div class="stat-number"><?= $stats->total ?></div>
                    <div class="stat-sub">All registered accounts</div>
                </div>
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-footer">
                    <a href="<?= base_url('users') ?>">View All &rarr;</a>
                </div>
            </div>
        </div>
    <div class="row">


        <div class="col-xl-3 col-md-6">
            <div class="stat-card sc-active">
                <div class="stat-info">
                    <div class="stat-label">Active Users</div>
                    <div class="stat-number"><?= $stats->active ?></div>
                    <div class="stat-sub">Currently enabled</div>
                </div>
                <div class="stat-icon"><i class="fas fa-user-check"></i></div>
                <div class="stat-footer">
                    <a href="<?= base_url('users?filter=active') ?>">View All →</a>


                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card sc-inactive">
                <div class="stat-info">
                    <div class="stat-label">Inactive Users</div>
                    <div class="stat-number"><?= $stats->inactive ?></div>
                    <div class="stat-sub">Currently disabled</div>
                </div>
                <div class="stat-icon"><i class="fas fa-user-slash"></i></div>
                <div class="stat-footer">
                  <a href="<?= base_url('users?filter=inactive') ?>">View All →</a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card sc-admins">
                <div class="stat-info">
                    <div class="stat-label">Admins</div>
                    <div class="stat-number"><?= $stats->admins ?></div>
                    <div class="stat-sub">Administrator accounts</div>
                </div>
                <div class="stat-icon"><i class="fas fa-user-shield"></i></div>
                <div class="stat-footer">
<a href="<?= base_url('users?filter=admins') ?>">View All →</a>


                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card sc-nonadmins">
                <div class="stat-info">
                    <div class="stat-label">Non-Admin Users</div>
                    <div class="stat-number"><?= $stats->nonadmins ?></div>
                    <div class="stat-sub">Non-Administrator accounts</div>
                </div>
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-footer">
                    <a href="<?= base_url('users?filter=nonadmins') ?>">View All →</a>
                </div>
            </div>
        </div>
    </div>


    <!-- Logs + Charts -->
    <div class="section-title mt-2">Recent Activity</div>


    <div class="row">


        <!-- LEFT — Logs DataTable -->
        <div class="col-lg-6">
            <div class="card-rms mb-4">
                <div class="card-header-rms">
                    <div class="header-dot"></div>
                    Activity Log
                </div>
                <div style="padding: 16px;">
                    <?php if (!empty($logs)): ?>
                        <table class="table-rms w-100" id="logsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $i => $log): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($log->action) ?></td>
                                        <td><?= htmlspecialchars($log->description) ?></td>
                                        <td><?= date('M d, Y h:i A', strtotime($log->created_at)) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"
                               style="font-size:28px;margin-bottom:8px;display:block;"></i>
                            No activity recorded yet.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>


        <!-- RIGHT — Charts stacked -->
        <div class="col-lg-6">


            <!-- Top — Two donuts side by side -->
            <div class="card-rms mb-4">
                <div class="card-header-rms">
                    <div class="header-dot"></div>
                    Users Overview
                </div>
                <div style="padding: 16px;">
                    <div class="row">


                        <!-- Donut 1: Active vs Inactive -->
                        <div class="col-6 text-center">
                            <div style="font-size:11px;font-weight:600;
                                        text-transform:uppercase;
                                        letter-spacing:.5px;
                                        color:#6b7280;
                                        margin-bottom:8px;">
                                Status
                            </div>
                            <div style="position:relative;width:100%;max-width:160px;margin:0 auto;">
                                <canvas id="statusDonutChart"></canvas>
                            </div>
                        </div>


                        <!-- Donut 2: Admins vs Regular -->
                        <div class="col-6 text-center">
                            <div style="font-size:11px;font-weight:600;
                                        text-transform:uppercase;
                                        letter-spacing:.5px;
                                        color:#6b7280;
                                        margin-bottom:8px;">
                                Role
                            </div>
                            <div style="position:relative;width:100%;max-width:160px;margin:0 auto;">
                                <canvas id="roleDonutChart"></canvas>
                            </div>
                        </div>


                    </div>
                </div>
            </div>


            <!-- Bottom — Logs bar chart -->
            <div class="card-rms mb-4">
                <div class="card-header-rms">
                    <div class="header-dot"></div>
                    Log Activity (Last 7 Days)
                </div>
                <div style="padding: 16px;">
                    <canvas id="logsBarChart"></canvas>
                </div>
            </div>


        </div>


    </div>


</div>


<?php $this->load->view('templates/footer'); ?>


