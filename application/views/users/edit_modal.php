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
                    <div class="form-group col-6">
                        <input class="form-control" id="edit_firstname" placeholder="First Name">
                    </div>
                    <div class="form-group col-6">
                        <input class="form-control" id="edit_lastname" placeholder="Last Name">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <input class="form-control" id="edit_employee_id" placeholder="Employee ID">
                    </div>
                    <div class="form-group col-6">
                        <input class="form-control" type="date" id="edit_birthday">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <input class="form-control" id="edit_contactno" placeholder="Contact Number">
                    </div>
                    <div class="form-group col-6">
                        <input class="form-control" id="edit_address" placeholder="Address">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <input class="form-control" id="edit_email" placeholder="Email">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <select class="form-control" id="edit_role">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div class="form-group col-6">
                        <select class="form-control" id="edit_is_active">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <input class="form-control" id="edit_job_title" placeholder="Job Title">
                    </div>
                    <div class="form-group col-6">
                        <input class="form-control" id="edit_department" placeholder="Department">
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