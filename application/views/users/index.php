<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        body { background-color: #f0f2f5; }

        .navbar { background-color: rgba(9, 151, 9, 0.89); }

        .card-custom {
            border-radius: 10px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }

        .modal-error { font-size: 14px; }

        .flash-msg { transition: all 0.3s ease; }

        .nav-link-active {
            background-color: rgba(255,255,255,0.2) !important;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<?php $this->load->view('templates/navbar'); ?>

<div class="container mt-5">

    <!-- Flash -->
    <?php if ($this->session->flashdata('success')) : ?>
        <div class="alert alert-success flash-msg">
            <?php echo $this->session->flashdata('success'); ?>
        </div>
    <?php endif; ?>

    <div class="card card-custom">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Users Table</h4>

            <!-- Create button: admin only -->
            <?php if ($role === 'admin') : ?>
                <button class="btn btn-success btn-sm"
                        data-toggle="modal"
                        data-target="#createUserModal">
                    + Create User
                </button>
            <?php else : ?>
                <!-- Non-admin sees a read-only label instead -->
                <span class="badge badge-secondary px-3 py-2">
                    Read-only access
                </span>
            <?php endif; ?>
        </div>

        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>LastName</th>
                    <th>FirstName</th>
                    <th>Email</th>
                    <th>Contact No</th>
                    <th>Role</th>
                    <th>Status</th>
                    <?php if ($role === 'admin') : ?>
                        <th width="180">Actions</th>
                    <?php endif; ?>
                </tr>
                </thead>

                <tbody>
                <?php if (!empty($users)) : ?>
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <td><?php echo (int)$user->id; ?></td>
                            <td>
                                <?php echo htmlspecialchars($user->lastname); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($user->firstname ); ?>
                            </td>

                            <td><?php echo htmlspecialchars($user->email); ?></td>

                            <td><?php echo htmlspecialchars($user->contactno); ?></td>

                            <td>
                                <span class="badge badge-<?php echo $user->role === 'admin' ? 'danger' : 'primary'; ?>">
                                    <?php echo ucfirst($user->role); ?>
                                </span>
                            </td>

                            <td>
                                <span class="badge badge-<?php echo $user->is_active ? 'success' : 'secondary'; ?>">
                                    <?php echo $user->is_active ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>

                            <?php if ($role === 'admin') : ?>
                                <td>
                                    <button class="btn btn-primary btn-sm">Edit</button>
                                    <button class="btn btn-danger btn-sm">Delete</button>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="<?php echo $role === 'admin' ? 7 : 6; ?>"
                            class="text-center text-muted py-3">
                            No users found.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>

            </table>

        </div>
    </div>

</div>


<!-- CREATE USER MODAL — rendered only for admin -->
<?php if ($role === 'admin') : ?>

<div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form id="createUserForm">

                <div class="modal-header">
                    <h5 class="modal-title">Create User</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <!-- Errors appear here ONLY -->
                    <div id="modalErrorBox"
                         class="alert alert-danger modal-error d-none">
                        <ul id="modalErrorList" class="mb-0 pl-3"></ul>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>First Name</label>
                            <input type="text" name="firstname"
                                   class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Last Name</label>
                            <input type="text" name="lastname"
                                   class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Birthday</label>
                            <input type="date" name="birthday"
                                   class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Contact Number</label>
                            <input type="text" name="contactno"
                                   class="form-control" maxlength="11" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Address</label>
                        <textarea name="address" class="form-control"
                                  rows="2" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email"
                               class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Password</label>
                            <input type="password" name="password"
                                   class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password"
                                   class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Role</label>
                            <select name="role" class="form-control">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Status</label>
                            <select name="is_active" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">Close</button>
                    <button type="submit" id="createUserSubmit"
                            class="btn btn-success">Create User</button>
                </div>

            </form>

        </div>
    </div>
</div>

<?php endif; ?>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {

    // Flash messages auto-hide
    setTimeout(function () {
        $('.flash-msg').fadeOut(500);
    }, 3000);

    <?php if ($role === 'admin') : ?>

    var STORE_URL  = '<?php echo base_url("users/store"); ?>';
    var $modal     = $('#createUserModal');
    var $form      = $('#createUserForm');
    var $errorBox  = $('#modalErrorBox');
    var $errorList = $('#modalErrorList');
    var $submitBtn = $('#createUserSubmit');
    var errorTimer = null;

    function showErrors(errors) {
        $errorList.empty();

        if (typeof errors === 'object' && errors !== null) {
            $.each(errors, function (field, msg) {
                $errorList.append($('<li>').text(msg));
            });
        } else {
            $errorList.append($('<li>').text(errors));
        }

        $errorBox.removeClass('d-none');

        clearTimeout(errorTimer);
        errorTimer = setTimeout(function () {
            $errorBox.fadeOut(500, function () {
                $(this).addClass('d-none').show();
                $errorList.empty();
            });
        }, 4000);
    }

    function clearErrors() {
        clearTimeout(errorTimer);
        $errorBox.addClass('d-none').show();
        $errorList.empty();
    }

    $modal.on('show.bs.modal', function () {
        $form[0].reset();
        clearErrors();
    });

    $form.on('submit', function (e) {
        e.preventDefault();

        clearErrors();
        $submitBtn.prop('disabled', true).text('Saving…');

        $.ajax({
            url      : STORE_URL,
            type     : 'POST',
            data     : $form.serialize(),
            dataType : 'json',

            success: function (res) {
                if (res.status === 'success') {
                    $modal.modal('hide');
                    location.reload();
                }
            },

            error: function (xhr) {
                var res = null;
                try { res = JSON.parse(xhr.responseText); } catch (e) {}

                if (res && res.errors) {
                    showErrors(res.errors);
                } else {
                    showErrors({ server: 'Server error. Please try again.' });
                }

                $submitBtn.prop('disabled', false).text('Create User');
            }
        });
    });

    <?php endif; ?>

});
</script>

</body>
</html>