<div class="modal fade" id="createUserModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Create User</h5>
            </div>

            <div class="modal-body">

                <div id="createAlert"></div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <input class="form-control" id="firstname" placeholder="First Name">
                    </div>
                    <div class="form-group col-6">
                        <input class="form-control" id="lastname" placeholder="Last Name">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <input class="form-control" id="employee_id" placeholder="Employee ID">
                    </div>
                    <div class="form-group col-6">
                        <input class="form-control" type="date" id="birthday">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <input class="form-control" id="contactno" placeholder="Contact Number">
                    </div>
                    <div class="form-group col-6">
                        <input class="form-control" id="address" placeholder="Address">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <input class="form-control" id="email" placeholder="Email">
                    </div>
                    <div class="form-group col-6">
                        <input class="form-control" type="password" id="password" placeholder="Password">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <select class="form-control" id="create_role">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div class="form-group col-6">
                        <select class="form-control" id="is_active">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <input class="form-control" id="job_title" placeholder="Job Title">
                    </div>
                    <div class="form-group col-6">
                        <input class="form-control" id="department" placeholder="Department">
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