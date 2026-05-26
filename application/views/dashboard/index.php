<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<div id="main-content">

    <?php if (!empty($flash_error)) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($flash_error); ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($flash_success)) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($flash_success); ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div class="wb-text">
            <h2>
                Welcome back,
                <?php echo htmlspecialchars($firstname . ' ' . $lastname); ?>!
            </h2>
            <p><?php echo htmlspecialchars($email); ?></p>
            <span class="wb-badge"><?php echo ucfirst($role); ?></span>
        </div>
        <div class="wb-avatar">
            <?php echo strtoupper(substr($firstname, 0, 1) . substr($lastname, 0, 1)); ?>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="section-title">System Overview MY bor</div>

    <?php
        // SAFE FALLBACK (fix CI_Loader undefined $this->data issue)
        $stats = isset($stats) ? $stats : (object)[
            'total' => 0,
            'active' => 0,
            'inactive' => 0,
            'admins' => 0
        ];
    ?>

    <div class="row">

        <div class="col-xl-3 col-md-6">
            <div class="stat-card sc-total">
                <div class="stat-info">
                    <div class="stat-label">Total Users</div>
                    <div class="stat-number"><?php echo $stats->total; ?></div>
                    <div class="stat-sub">All registered accounts</div>
                </div>
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-footer">
                    <a href="<?php echo base_url('users'); ?>">View All &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card sc-active">
                <div class="stat-info">
                    <div class="stat-label">Active Users</div>
                    <div class="stat-number"><?php echo $stats->active; ?></div>
                    <div class="stat-sub">Currently enabled</div>
                </div>
                <div class="stat-icon"><i class="fas fa-user-check"></i></div>
                <div class="stat-footer">
                    <a href="<?php echo base_url('users'); ?>">View All &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card sc-inactive">
                <div class="stat-info">
                    <div class="stat-label">Inactive Users</div>
                    <div class="stat-number"><?php echo $stats->inactive; ?></div>
                    <div class="stat-sub">Currently disabled</div>
                </div>
                <div class="stat-icon"><i class="fas fa-user-slash"></i></div>
                <div class="stat-footer">
                    <a href="<?php echo base_url('users'); ?>">View All &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card sc-admins">
                <div class="stat-info">
                    <div class="stat-label">Admins</div>
                    <div class="stat-number"><?php echo $stats->admins; ?></div>
                    <div class="stat-sub">Administrator accounts</div>
                </div>
                <div class="stat-icon"><i class="fas fa-user-shield"></i></div>
                <div class="stat-footer">
                    <a href="<?php echo base_url('users'); ?>">View All &rarr;</a>
                </div>
            </div>
        </div>

    </div>

    <!-- Recent Activity -->
    <div class="section-title mt-2">Recent Activity</div>

    <div class="card-rms mb-4">
        <div class="card-header-rms">
            <div class="header-dot"></div>
            Activity Log
        </div>

        <div>
            <?php if (!empty($logs)) : ?>
                <?php foreach ($logs as $log) : ?>
                    <div class="activity-item">
                        <div class="activity-dot"></div>
                        <div class="activity-content">
                            <div class="activity-action">
                                <?php echo htmlspecialchars($log->action); ?>
                            </div>
                            <div class="activity-desc">
                                <?php echo htmlspecialchars($log->description); ?>
                            </div>
                        </div>
                        <div class="activity-time">
                            <?php echo htmlspecialchars($log->created_at); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="empty-state">
                    <i class="fas fa-inbox" style="font-size:28px;margin-bottom:8px;display:block;"></i>
                    No activity recorded yet.
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php $this->load->view('templates/footer'); ?>