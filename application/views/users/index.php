<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<!-- Page CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/users.css') ?>">

<!-- CSRF tokens -->
<input type="hidden" id="csrf_token_name"  value="<?= $csrf_token_name ?>">
<input type="hidden" id="csrf_token_value" value="<?= $csrf_token_value ?>">

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
        </div>

        <div class="table-responsive">
            <table class="table-rms" id="usersTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Created</th>
                        <?php if ($role === 'admin'): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $i => $user): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <?= htmlspecialchars($user->firstname . ' ' . $user->lastname) ?>
                                <br>
                                <small class="text-muted"><?= htmlspecialchars($user->email) ?></small>
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
                            <td><?= date('M d, Y h:i A', strtotime($user->created_at)) ?></td>
                            <?php if ($role === 'admin'): ?>
                                <td>
                                    <button class="btn btn-primary btn-sm"
                                            onclick="editUser(<?= $user->id ?>)">Edit</button>
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
                        <td colspan="<?= $role === 'admin' ? 8 : 7 ?>"
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

<!-- CREATE MODAL -->
<div class="modal fade" id="createUserModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Create User</h5>
            </div>
            <div class="modal-body">
                <div id="createAlert"></div>
                <div class="form-row">
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <input class="form-control" id="firstname" placeholder="First Name">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <input class="form-control" id="lastname" placeholder="Last Name">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <input class="form-control" id="employee_id" placeholder="Employee ID">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <input class="form-control" type="date" id="birthday">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <input class="form-control" id="contactno" placeholder="Contact Number">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <input class="form-control" id="address" placeholder="Address">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <input class="form-control" id="email" placeholder="Email">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <input class="form-control" type="password"
                                   id="password" placeholder="Password (default: rms-2026)">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <select class="form-control" id="create_role">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
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
                            <input class="form-control" id="job_title" placeholder="Job Title">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <input class="form-control" id="department" placeholder="Department">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button class="btn btn-success" onclick="createUser()">Create User</button>
            </div>
        </div>
    </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editUserModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Edit User</h5>
            </div>
            <div class="modal-body">
                <div id="editAlert"></div>
                <input type="hidden" id="edit_id">
                <div class="form-row">
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <input class="form-control" id="edit_firstname" placeholder="First Name">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <input class="form-control" id="edit_lastname" placeholder="Last Name">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <input class="form-control" id="edit_employee_id" placeholder="Employee ID">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <input class="form-control" type="date" id="edit_birthday">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <input class="form-control" id="edit_contactno" placeholder="Contact Number">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <input class="form-control" id="edit_address" placeholder="Address">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <input class="form-control" id="edit_email" placeholder="Email">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <select class="form-control" id="edit_role">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
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
                            <input class="form-control" id="edit_job_title" placeholder="Job Title">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                    <div class="form-group col-6 mb-2">
                        <div class="field-wrap">
                            <input class="form-control" id="edit_department" placeholder="Department">
                            <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button class="btn btn-primary" onclick="updateUser()">Update User</button>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<!-- Page JS -->
<script src="<?= base_url('assets/js/modules/users.main.js') ?>"></script>

<?php $this->load->view('templates/footer'); ?>