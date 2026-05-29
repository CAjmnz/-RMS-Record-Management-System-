<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<link rel="stylesheet"
      href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

<style>
.field-wrap {
    position: relative;
    width: 100%;
}
.field-wrap input.is-invalid,
.field-wrap select.is-invalid {
    border: 1.5px solid #dc3545 !important;
    box-shadow: none !important;
    padding-right: 36px !important;
}
.field-error-icon {
    display: none;
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #dc3545;
    font-size: 14px;
    cursor: pointer;
    z-index: 10;
}
.field-wrap.has-error .field-error-icon {
    display: block;
}
.field-error-icon .error-tooltip {
    display: none;
    position: absolute;
    right: 24px;
    top: 50%;
    transform: translateY(-50%);
    background: #1e1e2e;
    color: #fff;
    font-size: 11px;
    font-weight: 500;
    padding: 5px 10px;
    border-radius: 6px;
    white-space: nowrap;
    pointer-events: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.25);
    z-index: 9999;
    letter-spacing: .2px;
}
.field-error-icon .error-tooltip::after {
    content: '';
    position: absolute;
    right: -6px;
    top: 50%;
    transform: translateY(-50%);
    border-width: 5px 0 5px 6px;
    border-style: solid;
    border-color: transparent transparent transparent #1e1e2e;
}
.field-error-icon:hover .error-tooltip {
    display: block;
}
</style>

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

<?php $this->load->view('templates/footer'); ?>

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

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

// ── CSRF ──────────────────────────────────────────────────────────────────────
function csrfData() {
    return {
        [document.getElementById('csrf_token_name').value]:
         document.getElementById('csrf_token_value').value
    };
}

// ── DataTable ─────────────────────────────────────────────────────────────────
$(document).ready(function () {
    $('#usersTable').DataTable({
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [],
        columnDefs: [{ orderable: false, targets: -1 }],
        language: {
            search:       'Search:',
            lengthMenu:   'Show _MENU_ entries',
            info:         'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty:    'Showing 0 to 0 of 0 entries',
            infoFiltered: '(filtered from _MAX_ total entries)',
            paginate: { first: 'First', last: 'Last', next: '&raquo;', previous: '&laquo;' }
        }
    });

    // Reset Create modal on close
    $('#createUserModal').on('hidden.bs.modal', function () {
        clearValidation('create');
        $('#createAlert').html('');
        $(this).find('input:not([type=hidden])').val('');
        $(this).find('select').prop('selectedIndex', 0);
    });

    // Reset Edit modal on close
    $('#editUserModal').on('hidden.bs.modal', function () {
        clearValidation('edit');
        $('#editAlert').html('');
    });
});

// ── Clear error while typing ──────────────────────────────────────────────────
$(document).on('input change', '.field-wrap input, .field-wrap select', function () {
    const wrap = $(this).closest('.field-wrap');
    $(this).removeClass('is-invalid');
    wrap.removeClass('has-error');
    wrap.find('.error-tooltip').text('');
});

// ── Validation helpers ────────────────────────────────────────────────────────
function clearValidation(formType) {
    const modal = formType === 'edit' ? '#editUserModal' : '#createUserModal';
    $(modal).find('.field-wrap').each(function () {
        $(this).removeClass('has-error');
        $(this).find('input, select').removeClass('is-invalid');
        $(this).find('.error-tooltip').text('');
    });
}

function showFieldError(selector, message) {
    const input = $(selector);
    const wrap  = input.closest('.field-wrap');
    input.addClass('is-invalid');
    wrap.addClass('has-error');
    wrap.find('.error-tooltip').text(message);
}

// Maps backend errors object → tooltip icons on the correct modal
// prefix = '' for create fields, 'edit_' for edit fields
function applyBackendErrors(errors, prefix, alertSelector) {
    Object.keys(errors).forEach(function(field) {
        showFieldError('#' + prefix + field, errors[field]);
    });

    // Scroll to first error inside the modal
    const firstError = $('.field-wrap.has-error').first();
    if (firstError.length) {
        firstError.closest('.modal-body').scrollTop(
            firstError.position().top - 20
        );
        firstError.find('input, select').first().focus();
    }

    $(alertSelector).html(
        '<div class="alert alert-danger">Please fix the highlighted fields.</div>'
    );
}

// Frontend-only required field check (instant, before AJAX fires)
function validateFields(prefix, alertSelector) {
    clearValidation(prefix === '' ? 'create' : 'edit');
    $(alertSelector).html('');

    const p      = '#' + prefix;
    const emailRx = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    let   hasErr  = false;

    const checks = [
        { id: 'firstname',   msg: 'First name is required.' },
        { id: 'lastname',    msg: 'Last name is required.' },
        { id: 'employee_id', msg: 'Employee ID is required.' },
        { id: 'birthday',    msg: 'Birthday is required.' },
        { id: 'contactno',   msg: 'Contact number is required.' },
        { id: 'address',     msg: 'Address is required.' },
        { id: 'email',       msg: 'Email is required.' },
        { id: 'job_title',   msg: 'Job title is required.' },
        { id: 'department',  msg: 'Department is required.' },
    ];

    checks.forEach(function(c) {
        if (!$(p + c.id).val().trim()) {
            showFieldError(p + c.id, c.msg);
            hasErr = true;
        }
    });

    const contactno = $(p + 'contactno').val().trim();
    if (contactno && !/^[0-9]{11}$/.test(contactno)) {
        showFieldError(p + 'contactno', 'Must be exactly 11 numeric digits.');
        hasErr = true;
    }

    const email = $(p + 'email').val().trim();
    if (email && !emailRx.test(email)) {
        showFieldError(p + 'email', 'Invalid email format.');
        hasErr = true;
    }

    if (hasErr) {
        $(alertSelector).html(
            '<div class="alert alert-danger">Please fix the highlighted fields.</div>'
        );
    }

    return hasErr;
}

// ── Create ────────────────────────────────────────────────────────────────────
function createUser() {

    // Frontend validation first — hard stop if fails
    if (validateFields('', '#createAlert')) return;

    $.post(
        "<?= base_url('users/store') ?>",
        Object.assign(csrfData(), {
            firstname:   $('#firstname').val().trim(),
            lastname:    $('#lastname').val().trim(),
            employee_id: $('#employee_id').val().trim(),
            birthday:    $('#birthday').val(),
            contactno:   $('#contactno').val().trim(),
            address:     $('#address').val().trim(),
            email:       $('#email').val().trim(),
            password:    $('#password').val(),
            role:        $('#create_role').val(),
            is_active:   $('#is_active').val(),
            job_title:   $('#job_title').val().trim(),
            department:  $('#department').val().trim()
        }),
        function(res) {
            if (res.success) {
                Swal.fire({
                    title: 'Created!',
                    text: res.message || 'User created successfully.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
                setTimeout(() => location.reload(), 1500);
                return;
            }

            // Backend field errors → map to tooltip icons
            if (res.errors && Object.keys(res.errors).length) {
                applyBackendErrors(res.errors, '', '#createAlert');
                return;
            }

            // General backend error
            $('#createAlert').html(
                '<div class="alert alert-danger">' +
                (res.message || 'Create failed.') + '</div>'
            );
        },
        'json'
    ).fail(function(xhr) {
        $('#createAlert').html(
            '<div class="alert alert-danger">Server error (' + xhr.status + ').</div>'
        );
    });
}

// ── Edit ──────────────────────────────────────────────────────────────────────
function editUser(id) {
    clearValidation('edit');
    $('#editAlert').html('');

    $.get("<?= base_url('users/get/') ?>" + id, function(res) {
        if (!res.success) return;
        let u = res.data;
        $('#edit_id').val(u.id);
        $('#edit_firstname').val(u.firstname);
        $('#edit_lastname').val(u.lastname);
        $('#edit_employee_id').val(u.employee_id);
        $('#edit_birthday').val(u.birthday);
        $('#edit_contactno').val(u.contactno);
        $('#edit_address').val(u.address);
        $('#edit_email').val(u.email);
        $('#edit_role').val(u.role);
        $('#edit_is_active').val(u.is_active);
        $('#edit_job_title').val(u.job_title);
        $('#edit_department').val(u.department);
        $('#editUserModal').modal('show');
    }, 'json');
}

// ── Update ────────────────────────────────────────────────────────────────────
function updateUser() {

    // Frontend validation first — hard stop if fails
    if (validateFields('edit_', '#editAlert')) return;

    $.post(
        "<?= base_url('users/update') ?>",
        Object.assign(csrfData(), {
            id:          $('#edit_id').val(),
            firstname:   $('#edit_firstname').val().trim(),
            lastname:    $('#edit_lastname').val().trim(),
            employee_id: $('#edit_employee_id').val().trim(),
            birthday:    $('#edit_birthday').val(),
            contactno:   $('#edit_contactno').val().trim(),
            address:     $('#edit_address').val().trim(),
            email:       $('#edit_email').val().trim(),
            role:        $('#edit_role').val(),
            is_active:   $('#edit_is_active').val(),
            job_title:   $('#edit_job_title').val().trim(),
            department:  $('#edit_department').val().trim()
        }),
        function(res) {
            if (res.success) {
                Swal.fire({
                    title: 'Updated!',
                    text: res.message || 'User updated successfully.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
                setTimeout(() => location.reload(), 1500);
                return;
            }

            // Backend field errors → map to tooltip icons
            if (res.errors && Object.keys(res.errors).length) {
                applyBackendErrors(res.errors, 'edit_', '#editAlert');
                return;
            }

            // General backend error
            $('#editAlert').html(
                '<div class="alert alert-danger">' +
                (res.message || 'Update failed.') + '</div>'
            );
        },
        'json'
    ).fail(function(xhr) {
        $('#editAlert').html(
            '<div class="alert alert-danger">Server error (' + xhr.status + ').</div>'
        );
    });
}

// ── Delete ────────────────────────────────────────────────────────────────────
function deleteUser(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This user will be soft-deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "<?= base_url('users/delete/') ?>" + id,
                type: 'POST',
                data: csrfData(),
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'User removed successfully.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        Swal.fire('Error!', res.message || 'Delete failed.', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Server error (' + xhr.status + ')', 'error');
                }
            });
        }
    });
}

</script>