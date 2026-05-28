<?php $this->load->view('templates/head', isset($this->data) ? $this->data : []); ?>
<?php $this->load->view('templates/sidebar', isset($this->data) ? $this->data : []); ?>
<?php $this->load->view('templates/topbar', isset($this->data) ? $this->data : []); ?>

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

    <div class="section-title">My Profile</div>

    <div class="row">

        <!-- Profile Card -->
        <div class="col-lg-4">
            <div class="profile-card">
                <div class="profile-banner"></div>
                <div class="profile-body">

                    <?php
                        $initials = '';
                        if (!empty($user->firstname) && !empty($user->lastname)) {
                            $initials = strtoupper(substr($user->firstname, 0, 1) . substr($user->lastname, 0, 1));
                        }
                    ?>

                    <div class="profile-avatar-wrap">
                        <div class="profile-avatar">
                            <?php echo $initials ?: 'U'; ?>
                        </div>
                    </div>

                    <p class="profile-name">
                        <?php echo htmlspecialchars($user->firstname ?? ''); ?>
                        <?php echo htmlspecialchars($user->lastname ?? ''); ?>
                        (<?php echo htmlspecialchars($user->nickname ?? ''); ?>)
                    </p>

                    <p class="profile-email">
                        <?php echo htmlspecialchars($user->email ?? '—'); ?>
                    </p>
                    <p class="profile-email">
                        <?php echo htmlspecialchars($user->employee_id ?? '—'); ?>
                    </p>
                    <div class="mt-3">
                        <?php if (!empty($user->role) && $user->role === 'admin') : ?>
                            <span class="badge-role-admin">
                                <i class="fas fa-shield-alt mr-1"></i>Admin
                            </span>
                        <?php else : ?>
                            <span class="badge-role-user">
                                <i class="fas fa-user mr-1"></i>User
                            </span>
                        <?php endif; ?>

                        <?php if (!empty($user->is_active)) : ?>
                            <span class="badge-active ml-2">
                                <i class="fas fa-circle mr-1" style="font-size:8px;"></i>Active
                            </span>
                        <?php else : ?>
                            <span class="badge-inactive ml-2">Inactive</span>
                        <?php endif; ?>
                    </div>

                    <div class="mt-4">
                        <a href="<?php echo base_url('profile/edit'); ?>"
                           class="btn btn-sm btn-block"
                           style="background:#1a1a2e;color:#fff;border-radius:6px;font-size:12px;font-weight:600;">
                            <i class="fas fa-pen mr-1"></i> Edit Profile
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <!-- Profile Details -->
        <div class="col-lg-8">
            <div class="card-rms mb-4">
                <div class="card-header-rms">
                    <div class="header-dot"></div>
                    Account Information
                </div>

                <div style="padding: 20px 24px;">
                    <div class="row">

                        <div class="col-md-6 mb-4">
                            <div class="profile-field-label">First Name</div>
                            <div class="profile-field-value">
                                <?php echo htmlspecialchars($user->firstname ?? '—'); ?>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="profile-field-label">Last Name</div>
                            <div class="profile-field-value">
                                <?php echo htmlspecialchars($user->lastname ?? '—'); ?>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="profile-field-label">NickName</div>
                            <div class="profile-field-value">
                                <?php echo htmlspecialchars($user->nickname ?? '—'); ?>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="profile-field-label">Email Address</div>
                            <div class="profile-field-value">
                                <?php echo htmlspecialchars($user->email ?? '—'); ?>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="profile-field-label">Contact Number</div>
                            <div class="profile-field-value">
                                <?php echo htmlspecialchars($user->contactno ?? '—'); ?>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="profile-field-label">Birthday</div>
                            <div class="profile-field-value">
                                <?php
                                $bday = $user->birthday ?? null;
                                echo ($bday && $bday !== '0000-00-00')
                                    ? htmlspecialchars(date('F d, Y', strtotime($bday)))
                                    : '—';
                                ?>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="profile-field-label">Address</div>
                            <div class="profile-field-value">
                                <?php echo htmlspecialchars($user->address ?? '—'); ?>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="profile-field-label">Member Since</div>
                            <div class="profile-field-value">
                                <?php echo !empty($user->created_at) ? date('F d, Y', strtotime($user->created_at)) : '—'; ?>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="profile-field-label">Last Updated</div>
                            <div class="profile-field-value">
                                <?php echo !empty($user->updated_at) ? date('F d, Y', strtotime($user->updated_at)) : '—'; ?>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>

</div>

<?php $this->load->view('templates/footer'); ?>