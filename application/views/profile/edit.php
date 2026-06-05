<?php $this->load->view('templates/head', isset($this->data) ? $this->data : []); ?>
<?php $this->load->view('templates/sidebar', isset($this->data) ? $this->data : []); ?>
<?php $this->load->view('templates/topbar', isset($this->data) ? $this->data : []); ?>

<div id="main-content">

    <?php if (!empty($flash_error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $flash_error ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($flash_success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash_success) ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <div class="section-title">Edit Profile</div>

    <div class="row">

        <!-- Left — profile summary card -->
        <div class="col-lg-4">
            <div class="profile-card">
                <div class="profile-banner"></div>
                <div class="profile-body">

                    <?php
                        $initials = '';
                        if (!empty($user->firstname) && !empty($user->lastname)) {
                            $initials = strtoupper(
                                substr($user->firstname, 0, 1) .
                                substr($user->lastname,  0, 1)
                            );
                        }
                    ?>

                    <!-- ── Avatar — photo or initials ── -->
                    <div class="profile-avatar-wrap">
                        <?php if (!empty($user->profile_picture) && file_exists(FCPATH . $user->profile_picture)): ?>
                            <img src="<?= base_url($user->profile_picture) ?>?v=<?= time() ?>"
                                 alt="Profile Picture"
                                 id="avatarPreview"
                                 style="width:90px;height:90px;border-radius:50%;
                                        object-fit:cover;border:3px solid #16c784;">
                        <?php else: ?>
                            <div class="profile-avatar" id="avatarInitials">
                                <?= $initials ?: 'U' ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <p class="profile-name">
                        <?= htmlspecialchars($user->firstname ?? '') ?>
                        <?= htmlspecialchars($user->lastname  ?? '') ?>
                    </p>

                    <p class="profile-email">
                        <?= htmlspecialchars($user->email ?? '—') ?>
                    </p>

                    <p class="profile-email">
                        <?= htmlspecialchars($user->employee_id ?? '—') ?>
                    </p>

                    <div class="mt-3">
                        <?php if (!empty($user->role) && $user->role === 'admin'): ?>
                            <span class="badge-role-admin">
                                <i class="fas fa-shield-alt mr-1"></i>Admin
                            </span>
                        <?php else: ?>
                            <span class="badge-role-user">
                                <i class="fas fa-user mr-1"></i>User
                            </span>
                        <?php endif; ?>

                        <?php if (!empty($user->is_active)): ?>
                            <span class="badge-active ml-2">
                                <i class="fas fa-circle mr-1" style="font-size:8px;"></i>Active
                            </span>
                        <?php else: ?>
                            <span class="badge-inactive ml-2">Inactive</span>
                        <?php endif; ?>
                    </div>

                    <div class="mt-4">
                        <a href="<?= base_url('profile') ?>"
                           class="btn btn-sm btn-block"
                           style="background:#1a1a2e;color:#fff;border-radius:6px;
                                  font-size:12px;font-weight:600;">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Profile
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <!-- Right — edit form -->
        <div class="col-lg-8">

            <!-- multipart REQUIRED for file upload -->
            <?= form_open('profile/update', ['enctype' => 'multipart/form-data']) ?>

                <!-- ── Profile Picture ────────────────────────────────── -->
                <div class="card-rms mb-4">
                    <div class="card-header-rms">
                        <div class="header-dot"></div>
                        Profile Picture
                    </div>
                    <div style="padding: 20px 24px;">
                        <div class="form-group mb-0">
                            <label>Choose a new photo
                                <small class="text-muted">(JPG, PNG, GIF, WEBP — max 2MB)</small>
                            </label>
                            <input type="file"
                                   name="profile_picture"
                                   id="profile_picture_input"
                                   class="form-control"
                                   accept="image/*">
                        </div>
                    </div>
                </div>

                <!-- ── Personal Information ───────────────────────────── -->
                <div class="card-rms mb-4">
                    <div class="card-header-rms">
                        <div class="header-dot"></div>
                        Personal Information
                    </div>
                    <div style="padding: 20px 24px;">

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>First Name</label>
                                <input type="text"
                                       name="firstname"
                                       class="form-control"
                                       value="<?= htmlspecialchars($user->firstname ?? '') ?>"
                                       required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Last Name</label>
                                <input type="text"
                                       name="lastname"
                                       class="form-control"
                                       value="<?= htmlspecialchars($user->lastname ?? '') ?>"
                                       required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Nickname</label>
                                <input type="text"
                                       name="nickname"
                                       class="form-control"
                                       value="<?= htmlspecialchars($user->nickname ?? '') ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Birthday</label>
                                <input type="date"
                                       name="birthday"
                                       class="form-control"
                                       value="<?= htmlspecialchars($user->birthday ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Contact Number</label>
                                <input type="text"
                                       name="contactno"
                                       class="form-control"
                                       value="<?= htmlspecialchars($user->contactno ?? '') ?>"
                                       required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Employee ID
                                    <small class="text-muted">(read-only)</small>
                                </label>
                                <input type="text"
                                       class="form-control"
                                       value="<?= htmlspecialchars($user->employee_id ?? '—') ?>"
                                       disabled>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Address</label>
                            <input type="text"
                                   name="address"
                                   class="form-control"
                                   value="<?= htmlspecialchars($user->address ?? '') ?>"
                                   required>
                        </div>

                    </div>
                </div>

                <!-- ── Change Password ────────────────────────────────── -->
                <div class="card-rms mb-4">
                    <div class="card-header-rms">
                        <div class="header-dot"></div>
                        Change Password
                        <small class="text-muted ml-2" style="font-size:11px;">
                            (leave all blank to keep current password)
                        </small>
                    </div>
                    <div style="padding: 20px 24px;">

                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password"
                                   name="current_password"
                                   class="form-control"
                                   placeholder="Required only if changing password">
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>New Password</label>
                                <input type="password"
                                       name="new_password"
                                       class="form-control"
                                       placeholder="Min. 6 characters">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Confirm New Password</label>
                                <input type="password"
                                       name="confirm_password"
                                       class="form-control"
                                       placeholder="Repeat new password">
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ── Actions ────────────────────────────────────────── -->
                <div class="d-flex justify-content-end">
                    <a href="<?= base_url('profile') ?>"
                       class="btn btn-secondary mr-2">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save mr-1"></i> Save Changes
                    </button>
                </div>

            <?= form_close() ?>

        </div>
    </div>
</div>

<!-- Live preview when a new image is selected -->
<script>
document.getElementById('profile_picture_input').addEventListener('change', function () {
    var file = this.files[0];
    if (!file) return;

    var reader = new FileReader();
    reader.onload = function (e) {
        var preview = document.getElementById('avatarPreview');
        var initials = document.getElementById('avatarInitials');

        if (preview) {
            preview.src = e.target.result;
        } else if (initials) {
            // Replace initials div with an img tag
            var img = document.createElement('img');
            img.id = 'avatarPreview';
            img.src = e.target.result;
            img.style.cssText = 'width:90px;height:90px;border-radius:50%;object-fit:cover;border:3px solid #16c784;';
            initials.parentNode.replaceChild(img, initials);
        }
    };
    reader.readAsDataURL(file);
});
</script>

<?php $this->load->view('templates/footer'); ?>