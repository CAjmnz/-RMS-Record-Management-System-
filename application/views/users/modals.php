<?php if ($role === 'admin'): ?>

<div class="modal fade" id="createUserModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Create User</h5>
            </div>

            <div class="modal-body">
                <input class="form-control" id="firstname" placeholder="First Name">
                <input class="form-control mt-2" id="lastname" placeholder="Last Name">
                <input class="form-control mt-2" id="email" placeholder="Email">
            </div>

            <div class="modal-footer">
                <button class="btn btn-success" onclick="createUser()">Create</button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="editUserModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Edit User</h5>
            </div>

            <div class="modal-body">
                <input type="hidden" id="edit_id">
                <input class="form-control" id="edit_firstname">
                <input class="form-control mt-2" id="edit_lastname">
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" onclick="updateUser()">Update</button>
            </div>

        </div>
    </div>
</div>

<?php endif; ?>