<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<div id="main-content">

    <div class="section-title">User Management</div>

    <div class="card-rms">

        <!-- TOOLBAR -->
        <div class="table-toolbar">
            <input type="text"
                   id="userSearch"
                   class="table-search"
                   placeholder="Search users...">

            <?php if ($role === 'admin') : ?>
                <button class="btn btn-sm"
                        data-toggle="modal"
                        data-target="#createUserModal"
                        style="background:#16c784;color:#fff;">
                    + Create User
                </button>
            <?php endif; ?>
        </div>

        <!-- TABLE -->
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
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody id="usersTableBody">
                <?php foreach ($users as $i => $user): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>

                        <td>
                            <?= $user->firstname . ' ' . $user->lastname ?><br>
                            <small><?= $user->email ?></small>
                        </td>

                        <td><?= $user->role ?></td>

                        <td><?= $user->is_active ? 'Active' : 'Inactive' ?></td>

                        <td><?= $user->contactno ?></td>

                        <td><?= $user->created_at ?></td>

                        <td>
                            <button class="btn btn-primary btn-sm"
                                    onclick="editUser(<?= $user->id ?>)">
                                Edit
                            </button>

                            <button class="btn btn-danger btn-sm"
                                    onclick="deleteUser(<?= $user->id ?>)">
                                Delete
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>

            </table>
        </div>

    </div>
</div>

<!-- ================= CREATE MODAL ================= -->
<div class="modal fade" id="createUserModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Create User</h5>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">

                <div class="form-row">
                    <input class="form-control mb-2 col-6" id="firstname" placeholder="First Name">
                    <input class="form-control mb-2 col-6" id="lastname" placeholder="Last Name">
                </div>

                <input class="form-control mb-2" id="employee_id" placeholder="Employee ID">

                <div class="form-row">
                    <input class="form-control mb-2 col-6" type="date" id="birthday">
                    <input class="form-control mb-2 col-6" id="contactno" placeholder="Contact Number">
                </div>

                <input class="form-control mb-2" id="address" placeholder="Address">
                <input class="form-control mb-2" id="email" placeholder="Email">

                <div class="form-row">
                    <input class="form-control mb-2 col-6" type="password" id="password" placeholder="Password">
                    <input class="form-control mb-2 col-6" type="password" id="confirm_password" placeholder="Confirm Password">
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

                <input class="form-control mb-2" id="job_title" placeholder="Job Title">
                <input class="form-control mb-2" id="department" placeholder="Department">

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button class="btn btn-success" onclick="createUser()">Create User</button>
            </div>

        </div>
    </div>
</div>

<!-- ================= EDIT MODAL ================= -->
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

                <input class="form-control mb-2" id="edit_employee_id" placeholder="Employee ID">

                <div class="form-row">
                    <input class="form-control mb-2 col-6" type="date" id="edit_birthday">
                    <input class="form-control mb-2 col-6" id="edit_contactno" placeholder="Contact Number">
                </div>

                <input class="form-control mb-2" id="edit_address" placeholder="Address">
                <input class="form-control mb-2" id="edit_email" placeholder="Email">

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

                <input class="form-control mb-2" id="edit_job_title" placeholder="Job Title">
                <input class="form-control mb-2" id="edit_department" placeholder="Department">

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button class="btn btn-primary" onclick="updateUser()">Update User</button>
            </div>

        </div>
    </div>
</div>

<?php $this->load->view('templates/footer'); ?>

<script>

function editUser(id)
{
    $.get("<?= base_url('users/get/') ?>" + id, function(res){

        if (!res.success) return alert("User not found");

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

function updateUser()
{
    $.post("<?= base_url('users/update') ?>", {
        id: $('#edit_id').val(),
        firstname: $('#edit_firstname').val(),
        lastname: $('#edit_lastname').val(),
        employee_id: $('#edit_employee_id').val(),
        birthday: $('#edit_birthday').val(),
        contactno: $('#edit_contactno').val(),
        address: $('#edit_address').val(),
        email: $('#edit_email').val(),
        role: $('#edit_role').val(),
        is_active: $('#edit_is_active').val(),
        job_title: $('#edit_job_title').val(),
        department: $('#edit_department').val()
    }, function(){
        location.reload();
    }, 'json');
}

function createUser()
{
    $.post("<?= base_url('users/store') ?>", {
        firstname: $('#firstname').val(),
        lastname: $('#lastname').val(),
        employee_id: $('#employee_id').val(),
        birthday: $('#birthday').val(),
        contactno: $('#contactno').val(),
        address: $('#address').val(),
        email: $('#email').val(),
        password: $('#password').val(),
        role: $('#role').val(),
        is_active: $('#is_active').val(),
        job_title: $('#job_title').val(),
        department: $('#department').val()
    }, function(){
        location.reload();
    }, 'json');
}

function deleteUser(id)
{
    if (!confirm('Delete user?')) return;

    $.post("<?= base_url('users/delete/') ?>" + id, function(){
        location.reload();
    });
}

</script>