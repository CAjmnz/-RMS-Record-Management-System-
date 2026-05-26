<?php $this->load->view('templates/head', $this->data); ?>
<?php $this->load->view('templates/sidebar', $this->data); ?>
<?php $this->load->view('templates/topbar', $this->data); ?>

<div id="main-content">

    <?php if ( ! empty($flash_error)) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($flash_error); ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <?php if ( ! empty($flash_success)) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($flash_success); ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <div class="section-title">User Management</div>

    <div class="card-rms">

        <!-- Toolbar -->
        <div class="table-toolbar">
            <input type="text"
                   id="userSearch"
                   class="table-search"
                   placeholder="&#xf002; Search users..."
                   style="font-family: 'Segoe UI', FontAwesome, sans-serif;">

            <?php if ($role === 'admin') : ?>
                <button class="btn btn-sm"
                        style="background:#16c784;color:#fff;border-radius:6px;font-size:12px;font-weight:600;padding:6px 14px;border:none;"
                        data-toggle="modal" data-target="#createUserModal">
                    <i class="fas fa-plus mr-1"></i> Create User
                </button>
            <?php else : ?>
                <span class="badge" style="background:#f0f2f5;color:#858796;padding:6px 12px;border-radius:6px;font-size:11px;font-weight:600;">
                    <i class="fas fa-lock mr-1"></i> Read Only
                </span>
            <?php endif; ?>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table-rms" id="usersTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Contact</th>
                        <th>Created</th>
                        <?php if ($role === 'admin') : ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <?php if ( ! empty($users)) : ?>
                        <?php foreach ($users as $i => $user) : ?>
                            <tr>
                                <td style="color:#b7b9cc;"><?php echo $i + 1; ?></td>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-initials">
                                            <?php echo strtoupper(substr($user->firstname, 0, 1) . substr($user->lastname, 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="user-name">
                                                <?php echo htmlspecialchars($user->firstname . ' ' . $user->lastname); ?>
                                            </div>
                                            <div class="user-email">
                                                <?php echo htmlspecialchars($user->email); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($user->role === 'admin') : ?>
                                        <span class="badge-role-admin">Admin</span>
                                    <?php else : ?>
                                        <span class="badge-role-user">User</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user->is_active) : ?>
                                        <span class="badge-active">Active</span>
                                    <?php else : ?>
                                        <span class="badge-inactive">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td style="color:#858796;font-size:12px;">
                                    <?php echo htmlspecialchars($user->contactno ?: '—'); ?>
                                </td>
                                <td style="color:#858796;font-size:12px;">
                                    <?php echo date('M d, Y', strtotime($user->created_at)); ?>
                                </td>
                                <?php if ($role === 'admin') : ?>
                                    <td>
                                        <button class="btn-action btn-edit mr-1"
                                                onclick="editUser(<?php echo $user->id; ?>)">
                                            <i class="fas fa-pen mr-1"></i>Edit
                                        </button>
                                        <button class="btn-action btn-delete"
                                                onclick="deleteUser(<?php echo $user->id; ?>)">
                                            <i class="fas fa-trash mr-1"></i>Delete
                                        </button>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="<?php echo $role === 'admin' ? 7 : 6; ?>">
                                <div class="empty-state">
                                    <i class="fas fa-users" style="font-size:28px;margin-bottom:8px;display:block;"></i>
                                    No users found.
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div><!-- /card-rms -->

    <!-- Row count -->
    <div style="font-size:12px;color:#b7b9cc;margin-top:10px;padding-left:4px;">
        Showing <span id="rowCount"><?php echo count($users); ?></span> user(s)
    </div>

</div><!-- /main-content -->

<!-- ══════════════════════════════════
     CREATE USER MODAL (admin only)
══════════════════════════════════ -->
<?php if ($role === 'admin') : ?>
<div class="modal fade" id="createUserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius:10px;border:none;">

            <div class="modal-header" style="background:#1a1a2e;border-radius:10px 10px 0 0;">
                <h5 class="modal-title" style="color:#fff;font-size:15px;font-weight:700;">
                    <i class="fas fa-user-plus mr-2"></i>Create New User
                </h5>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body" style="padding:24px;">
                <div id="modalAlert" style="display:none;"></div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label style="font-size:12px;font-weight:600;color:#5a5c69;">First Name</label>
                        <input type="text" id="cu_firstname" class="form-control form-control-sm" placeholder="First name">
                    </div>
                    <div class="form-group col-6">
                        <label style="font-size:12px;font-weight:600;color:#5a5c69;">Last Name</label>
                        <input type="text" id="cu_lastname" class="form-control form-control-sm" placeholder="Last name">
                    </div>
                </div>

                <div class="form-group">
                    <label style="font-size:12px;font-weight:600;color:#5a5c69;">Email Address</label>
                    <input type="email" id="cu_email" class="form-control form-control-sm" placeholder="email@example.com">
                </div>

                <div class="form-group">
                    <label style="font-size:12px;font-weight:600;color:#5a5c69;">Password</label>
                    <input type="password" id="cu_password" class="form-control form-control-sm" placeholder="Password">
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label style="font-size:12px;font-weight:600;color:#5a5c69;">Role</label>
                        <select id="cu_role" class="form-control form-control-sm">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group col-6">
                        <label style="font-size:12px;font-weight:600;color:#5a5c69;">Status</label>
                        <select id="cu_is_active" class="form-control form-control-sm">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

            </div>

            <div class="modal-footer" style="border-top:1px solid #e3e6f0;">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm"
                        id="createUserBtn"
                        style="background:#16c784;color:#fff;border:none;border-radius:6px;font-weight:600;">
                    <i class="fas fa-save mr-1"></i> Save User
                </button>
            </div>

        </div>
    </div>
</div>
<?php endif; ?>

<?php $this->load->view('templates/footer'); ?>

<script>
// ── Live Search ──
document.getElementById('userSearch').addEventListener('keyup', function () {
    var term = this.value.toLowerCase();
    var rows  = document.querySelectorAll('#usersTableBody tr');
    var count = 0;
    rows.forEach(function (row) {
        var text = row.textContent.toLowerCase();
        var show = text.indexOf(term) > -1;
        row.style.display = show ? '' : 'none';
        if (show) count++;
    });
    document.getElementById('rowCount').textContent = count;
});

<?php if ($role === 'admin') : ?>

// ── Create User ──
document.getElementById('createUserBtn').addEventListener('click', function () {
    var btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Saving...';

    var data = {
        firstname : document.getElementById('cu_firstname').value.trim(),
        lastname  : document.getElementById('cu_lastname').value.trim(),
        email     : document.getElementById('cu_email').value.trim(),
        password  : document.getElementById('cu_password').value,
        role      : document.getElementById('cu_role').value,
        is_active : document.getElementById('cu_is_active').value
    };

    $.ajax({
        url  : '<?php echo base_url("users/store"); ?>',
        type : 'POST',
        data : data,
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                $('#createUserModal').modal('hide');
                location.reload();
            } else {
                showModalAlert('danger', res.message || 'Failed to create user.');
            }
        },
        error: function () {
            showModalAlert('danger', 'Server error. Please try again.');
        },
        complete: function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save mr-1"></i> Save User';
        }
    });
});

// ── Edit User ──
function editUser(id) {
    window.location.href = '<?php echo base_url("users/edit/"); ?>' + id;
}

// ── Delete User ──
function deleteUser(id) {
    if (!confirm('Delete this user? This cannot be undone.')) return;
    $.ajax({
        url  : '<?php echo base_url("users/delete/"); ?>' + id,
        type : 'POST',
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                location.reload();
            } else {
                alert(res.message || 'Failed to delete user.');
            }
        }
    });
}

function showModalAlert(type, msg) {
    var el = document.getElementById('modalAlert');
    el.innerHTML = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
        msg + '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>';
    el.style.display = 'block';
    setTimeout(function () { $(el).find('.alert').fadeOut(); }, 4000);
}

// Reset modal on close
$('#createUserModal').on('hidden.bs.modal', function () {
    document.getElementById('cu_firstname').value = '';
    document.getElementById('cu_lastname').value  = '';
    document.getElementById('cu_email').value     = '';
    document.getElementById('cu_password').value  = '';
    document.getElementById('cu_role').value      = 'user';
    document.getElementById('cu_is_active').value = '1';
    document.getElementById('modalAlert').style.display = 'none';
});

<?php endif; ?>
</script>