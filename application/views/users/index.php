<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<!-- Page CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/users.css') ?>">

<!-- CSRF tokens -->
<input type="hidden" id="csrf_token_name"  value="<?= $csrf_token_name ?>">
<input type="hidden" id="csrf_token_value" value="<?= $csrf_token_value ?>">

<!-- BASE_URL for JS -->
<script>var BASE_URL = "<?= base_url() ?>";</script>



<div id="main-content">

    <div class="section-title">User Management</div>

    <div class="card-rms">

        <div class="table-toolbar mb-3">
            <?php if ($role === 'admin'): ?>
                <button class="btn btn-sm"
                        data-toggle="modal"
                        data-target="#createUserModal"
                        style="background:#16c784;color:#fff;">
                    + Create User
                </button>
            <?php else: ?>
                <span class="badge badge-secondary">Read-only</span>
            <?php endif; ?>

            <div class="table-filters mb-3">
                <select id="filterRole" class="form-control form-control-sm d-inline-block" style="width:150px;">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>

                <select id="filterStatus" class="form-control form-control-sm d-inline-block" style="width:150px;">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>

                <input type="date" id="filterDate"
                       class="form-control form-control-sm d-inline-block" style="width:180px;">

                <input type="text" id="filterDepartment"
                       class="form-control form-control-sm d-inline-block"
                       placeholder="Department" style="width:180px;">

                <button id="resetFilters" class="btn btn-sm btn-secondary">Reset</button>
            </div>
                <button id="resetFilters" class="btn btn-sm btn-secondary">Reset</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-rms" id="usersTable">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Created</th>
                        <th>Department</th>
                        <?php if ($role === 'admin'): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $i => $user): ?>
                        <tr>

                            <td>
                            <div style="display:flex;align-items:center;gap:15px;margin-bottom:20px;">

<?php
    $avatar = !empty($user->profile_picture)
        ? base_url($user->profile_picture)
        : null;

    $initials = strtoupper(
        substr($user->firstname, 0, 1) .
        substr($user->lastname, 0, 1)
    );
?>

<!-- AVATAR -->
<?php if ($avatar): ?>
    <img src="<?= $avatar ?>"
         style="
            width:70px;
            height:70px;
            border-radius:50%;
            object-fit:cover;
            border:3px solid #16c784;
         ">
<?php else: ?>
    <div style="
        width:70px;
        height:70px;
        border-radius:50%;
        background:#16c784;
        color:#fff;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:22px;
        font-weight:600;
    ">
        <?= $initials ?>
    </div>
<?php endif; ?>

<!-- NAME + META -->
<div>
    <h3 style="margin:0;font-weight:700;">
        <?= htmlspecialchars($user->firstname . ' ' . $user->lastname) ?>
    </h3>

    <div style="color:#6c757d;font-size:14px;">
        <?= htmlspecialchars($user->email) ?>
    </div>

    <?php if (!empty($user->role)): ?>
        <span class="badge badge-<?= $user->role === 'admin' ? 'danger' : 'secondary' ?>">
            <?= ucfirst($user->role) ?>
        </span>
    <?php endif; ?>
</div>

</div>
                            </td>
                            <td>
                                <span class="badge badge-<?= $user->role === 'admin' ? 'danger' : 'secondary' ?>">
                                    <?= ucfirst($user->role) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?= $user->is_active ? 'success' : 'warning' ?>">
                                    <?= $user->is_active ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($user->contactno) ?></td>
                            <td><?= htmlspecialchars($user->address) ?></td>
                            <td class="created-date" data-date="<?= date('Y-m-d', strtotime($user->created_at)) ?>">
                                <?= date('M d, Y h:i A', strtotime($user->created_at)) ?>
                            </td>
                            <td class="department-cell" data-dept="<?= htmlspecialchars(strtolower($user->department), ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($user->department) ?>
                            </td>
                            <?php if ($role === 'admin'): ?>
                                <td>
                                    <button class="btn btn-primary btn-sm btn-edit"
                                            data-id="<?= $user->id ?>">Edit</button>
                                    <?php if ($user->id != $logged_user_id): ?>
                                        <button class="btn btn-danger btn-sm"
                                                onclick="deleteUser(<?= $user->id ?>)">Delete</button>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= $role === 'admin' ? 9 : 8 ?>"
                            class="text-center text-muted py-4">
                            No users found.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php if ($role === 'admin'): ?>

<!-- ════════════════════════════════════════
     CREATE MODAL
     ════════════════════════════════════════ -->
<div class="modal fade" id="createUserModal"
     tabindex="-1" role="dialog" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createUserModalLabel">Create User</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

                <div id="createAlert" style="display:none;"></div>

                <div class="form-row">
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <label for="firstname">First Name</label>
                            <input class="form-control" id="firstname" autocomplete="off">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <label for="lastname">Last Name</label>
                            <input class="form-control" id="lastname" autocomplete="off">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <label for="employee_id">Employee ID</label>
                            <input class="form-control" id="employee_id" autocomplete="off">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <label for="birthday">Birthday</label>
                            <input class="form-control" type="date" id="birthday">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <label for="contactno">Contact No</label>
                            <input class="form-control" id="contactno" autocomplete="off">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <label for="address">Address</label>
                            <input class="form-control" id="address" autocomplete="off">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <label for="email">Email</label>
                            <input class="form-control" id="email" autocomplete="off">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <label for="password">Password</label>
                            <input class="form-control" type="text" id="password"
                                   value="rms-2026" autocomplete="off">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <label for="create_role">Role</label>
                            <select class="form-control" id="create_role">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <label for="is_active">Status</label>
                            <select class="form-control" id="is_active">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <label for="job_title">Job Title</label>
                            <input class="form-control" id="job_title" autocomplete="off">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <label for="department">Department</label>
                            <input class="form-control" id="department" autocomplete="off">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                </div>
                <div class="form-row">
    <div class="form-group col-12 mb-2">
        <div class="field-wrap">
            <label for="profile_picture">Profile Picture</label>
            <input class="form-control"
                   type="file"
                   id="profile_picture"
                   accept="image/*">
            <span class="field-error-icon">
                ⚠<span class="error-tooltip"></span>
            </span>
        </div>
    </div>
</div>
            </div><!-- /modal-body -->
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button class="btn btn-success" onclick="createUser()">Create User</button>
            </div>
        </div>
    </div>
</div>

<!-- ════════════════════════════════════════
     EDIT MODAL
     ════════════════════════════════════════ -->
<div class="modal fade" id="editUserModal"
     tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

                <div id="editAlert" style="display:none;"></div>

                <input type="hidden" id="edit_id">

                <div class="form-row">
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <label for="edit_firstname">First Name</label>
                            <input class="form-control" id="edit_firstname" autocomplete="off">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <label for="edit_lastname">Last Name</label>
                            <input class="form-control" id="edit_lastname" autocomplete="off">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <label for="edit_employee_id">Employee ID</label>
                            <input class="form-control" id="edit_employee_id" autocomplete="off">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <label for="edit_birthday">Birthday</label>
                            <input class="form-control" type="date" id="edit_birthday">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <label for="edit_contactno">Contact No</label>
                            <input class="form-control" id="edit_contactno" autocomplete="off">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <label for="edit_address">Address</label>
                            <input class="form-control" id="edit_address" autocomplete="off">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <label for="edit_email">Email</label>
                            <input class="form-control" id="edit_email" autocomplete="off">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <label for="edit_role">Role</label>
                            <select class="form-control" id="edit_role">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <label for="edit_is_active">Status</label>
                            <select class="form-control" id="edit_is_active">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <label for="edit_job_title">Job Title</label>
                            <input class="form-control" id="edit_job_title" autocomplete="off">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <label for="edit_department">Department</label>
                            <input class="form-control" id="edit_department" autocomplete="off">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                </div>
                <div class="form-row">
    <div class="form-group col-12 mb-2">
        <div class="field-wrap">
            <label for="edit_profile_picture">Change Profile Picture</label>
            <input class="form-control"
                   type="file"
                   id="edit_profile_picture"
                   accept="image/*">
            <span class="field-error-icon">
                ⚠<span class="error-tooltip"></span>
            </span>
        </div>
    </div>
</div>
            </div><!-- /modal-body -->
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button class="btn btn-primary" onclick="updateUser()">Update User</button>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<!-- Page JS -->
<?php $this->load->view('templates/footer'); ?>

<!-- Page JS — AFTER footer so jQuery/Bootstrap/Swal are loaded first -->
<script src="<?= base_url('assets/js/modules/users.main.js') ?>"></script>