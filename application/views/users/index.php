<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<!-- DataTables CSS -->
<link rel="stylesheet"
      href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

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
                            <td><?= $user->created_at ?></td>
                            <?php if ($role === 'admin'): ?>
                                <td>
                                    <button class="btn btn-primary btn-sm"
                                            onclick="editUser(<?= $user->id ?>)">Edit</button>
                                    <button class="btn btn-danger btn-sm"
                                            onclick="deleteUser(<?= $user->id ?>)">Delete</button>
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

<!-- CREATE MODAL — admin only, not rendered for regular users -->
<?php if ($role === 'admin'): ?>
<div class="modal fade" id="createUserModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Create User</h5>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="createAlert"></div>
                <div class="form-row">
                    <input class="form-control mb-2 col-6" id="firstname" placeholder="First Name">
                    <input class="form-control mb-2 col-6" id="lastname" placeholder="Last Name">
                </div>
                <div class="form-row">
                    <input class="form-control mb-2 col-6" id="employee_id" placeholder="Employee ID">
                    <input class="form-control mb-2 col-6" type="date" id="birthday">
                </div>
                <div class="form-row">
                    <input class="form-control mb-2 col-6" id="contactno" placeholder="Contact Number">
                    <input class="form-control mb-2 col-6" id="address" placeholder="Address">
                </div>
                <div class="form-row">
                    <input class="form-control mb-2 col-6" id="email" placeholder="Email">
                    <input class="form-control mb-2 col-6" type="password"
                           id="password" placeholder="Password">
                </div>
                <div class="form-row">
                    <select class="form-control mb-2 col-6" id="role">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                    <select class="form-control mb-2 col-6" id="is_active">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="form-row">
                    <input class="form-control mb-2 col-6" id="job_title" placeholder="Job Title">
                    <input class="form-control mb-2 col-6" id="department" placeholder="Department">
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
                <button class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_id">
                <div class="form-row">
                    <input class="form-control mb-2 col-6" id="edit_firstname" placeholder="First Name">
                    <input class="form-control mb-2 col-6" id="edit_lastname" placeholder="Last Name">
                </div>
                <div class="form-row">
                    <input class="form-control mb-2 col-6" id="edit_employee_id" placeholder="Employee ID">
                    <input class="form-control mb-2 col-6" type="date" id="edit_birthday">
                </div>
                <div class="form-row">
                    <input class="form-control mb-2 col-6" id="edit_contactno" placeholder="Contact Number">
                    <input class="form-control mb-2 col-6" id="edit_address" placeholder="Address">
                </div>
                <div class="form-row">
                    <input class="form-control mb-2 col-6" id="edit_email" placeholder="Email">
                </div>
                <div class="form-row">
                    <select class="form-control mb-2 col-6" id="edit_role">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                    <select class="form-control mb-2 col-6" id="edit_is_active">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="form-row">
                    <input class="form-control mb-2 col-6" id="edit_job_title" placeholder="Job Title">
                    <input class="form-control mb-2 col-6" id="edit_department" placeholder="Department">
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

<!-- SCRIPTS -->
<!-- DataTables JS (Bootstrap 4 build) -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
    $('#usersTable').DataTable({
        pageLength: 5,                        // default rows per page
        lengthMenu: [5, 10, 25, 50, 100],    // page size options
        order: [],                            // keep PHP sort order (DESC id)
        columnDefs: [
            { orderable: false, targets: -1 } // disable sort on Actions column
        ],
        language: {
            search:         'Search:',
            lengthMenu:     'Show _MENU_ entries',
            info:           'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty:      'Showing 0 to 0 of 0 entries',
            infoFiltered:   '(filtered from _MAX_ total entries)',
            paginate: {
                first:    'First',
                last:     'Last',
                next:     '&raquo;',
                previous: '&laquo;'
            }
        }
    });
});

function createUser() {
    $.post("<?= base_url('users/store') ?>", {
        firstname:   $('#firstname').val(),
        lastname:    $('#lastname').val(),
        employee_id: $('#employee_id').val(),
        birthday:    $('#birthday').val(),
        contactno:   $('#contactno').val(),
        address:     $('#address').val(),
        email:       $('#email').val(),
        password:    $('#password').val(),
        role:        $('#role').val(),
        is_active:   $('#is_active').val(),
        job_title:   $('#job_title').val(),
        department:  $('#department').val()
    }, function(res) {
        if (res.success) {
            location.reload();
        } else {
            $('#createAlert').html(
                '<div class="alert alert-danger">' + (res.message || 'Create failed.') + '</div>'
            );
        }
    }, 'json');
}

function editUser(id) {
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

function updateUser() {
    $.post("<?= base_url('users/update') ?>", {
        id:          $('#edit_id').val(),
        firstname:   $('#edit_firstname').val(),
        lastname:    $('#edit_lastname').val(),
        employee_id: $('#edit_employee_id').val(),
        birthday:    $('#edit_birthday').val(),
        contactno:   $('#edit_contactno').val(),
        address:     $('#edit_address').val(),
        email:       $('#edit_email').val(),
        role:        $('#edit_role').val(),
        is_active:   $('#edit_is_active').val(),
        job_title:   $('#edit_job_title').val(),
        department:  $('#edit_department').val()
    }, function() {
        location.reload();
    }, 'json');
}

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
                error: function() {
                    Swal.fire('Error!', 'Server error while deleting.', 'error');
                }
            });
        }
    });
}
</script>