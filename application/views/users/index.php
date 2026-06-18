<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<!-- Page CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/users.css') ?>">

<!-- CSRF tokens -->
<input type="hidden" id="csrf_token_name" value="<?= $csrf_token_name ?>">
<input type="hidden" id="csrf_token_value" value="<?= $csrf_token_value ?>">

<!-- BASE_URL and role for JS -->
<script>
    var BASE_URL = "<?= base_url() ?>";
    var RMS_ROLE = "<?= $role ?>";
</script>

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

                <!-- ✅ id kept as filterDepartment (matches users.js) -->
                <input type="text" id="filterDepartment"
                    class="form-control form-control-sm d-inline-block"
                    placeholder="Department" style="width:180px;">

                <button id="resetFilters" class="btn btn-secondary btn-sm">
                    Reset Filters
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-rms" id="usersTable">
                <thead>
                    <tr>
                        <!--
                            Col 0 : User  (avatar + name + email — combined in JS render)
                            Col 1 : Role
                            Col 2 : Status
                            Col 3 : Contact
                            Col 4 : Address
                            Col 5 : Created
                            Col 6 : Department
                            Col 7 : Birthday
                            Col 8 : Actions  (admin only — always last)
                        -->
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Created</th>
                        <th>Department</th>
                        <th>Birthday</th>
                        <?php if ($role === 'admin'): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody></tbody>
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
                            <div class="form-row">
                                <div class="form-group col-6 mb-2">
                                    <div class="field-wrap">
                                        <label for="employee_id">Employee ID</label>

                                        <input
                                            type="text"
                                            class="form-control"
                                            id="employee_id"
                                            readonly>

                                        <span class="field-error-icon">
                                            &#9888;
                                            <span class="error-tooltip"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-6 mb-2">
                            <div class="field-wrap">
                                <label for="birthday">Birthday</label>
                                <input class="form-control" type="date" id="birthday"
                                    max="<?= date('Y-m-d') ?>">
                                <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-6 mb-2">
                            <div class="field-wrap">
                                <label for="contactno">Contact No</label>
                                <input class="form-control" id="contactno" autocomplete="off"
                                    inputmode="numeric" maxlength="11" pattern="[0-9]{11}"
                                    placeholder="09XXXXXXXXX">
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
                                <input type="text"
                                    class="form-control"
                                    id="password"
                                    value="rms-2026"
                                    readonly>
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
                                <input class="form-control" type="file" id="profile_picture" accept="image/*">
                                <span class="field-error-icon">⚠<span class="error-tooltip"></span></span>
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
                                <input type="text"
                                    class="form-control"
                                    id="edit_employee_id"
                                    readonly>
                                <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                            </div>
                        </div>
                        <div class="form-group col-6 mb-2">
                            <div class="field-wrap">
                                <label for="edit_birthday">Birthday</label>
                                <input class="form-control" type="date" id="edit_birthday"
                                    max="<?= date('Y-m-d') ?>">
                                <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-6 mb-2">
                            <div class="field-wrap">
                                <label for="edit_contactno">Contact No</label>
                                <input class="form-control" id="edit_contactno" autocomplete="off"
                                    inputmode="numeric" maxlength="11" pattern="[0-9]{11}"
                                    placeholder="09XXXXXXXXX">
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
                                <input class="form-control" type="file" id="edit_profile_picture" accept="image/*">
                                <span class="field-error-icon">⚠<span class="error-tooltip"></span></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-12 mb-2">
                            <div class="p-2 rounded" style="background:#f8f9fa;border:1px solid #e9ecef;font-size:.85rem;">
                                <span class="text-muted">Password Reset Count:</span>
                                <strong id="edit_reset_count">0</strong>
                                <span class="text-muted ml-2" id="edit_reset_count_note"></span>
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
    <!-- ═══════════════════════════════════════════════════════
  ATTACH DOCUMENTS MODAL
═══════════════════════════════════════════════════════ -->
    <div class="modal fade" id="attachDocsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Attach Documents</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="attach_user_id">

                    <!-- SELECTED FILES PREVIEW GRID -->
                    <div class="mb-3">
                        <h6 class="mb-2">Selected Files</h6>
                        <div id="filePreview" class="drive-grid">
                            <small class="text-muted">No files selected.</small>
                        </div>
                    </div>
                    <!-- FILE INPUT -->
                    <div class="form-group mb-2">
                        <input type="file"
                            name="documents[]"
                            class="form-control"
                            multiple
                            accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
                            
                        <small class="text-muted">Allowed: JPG, PNG, PDF, DOC, DOCX, XLS, XLSX • Max 10MB</small>
                    </div>


                    <hr>

                    <!-- UPLOADED FILES GRID -->
                    <div>
                        <div class="d-flex justify-content-between align-items-center md-2">
                        <h6 class="mb-0">Uploaded Files</h6>
                        <button id="btnDeleteSelected" class="btn btn-sm btn-danger" style="display:none;">
                            <i class="fa-solid fa-trash mr-1"></i> Delete Selected (<span id="selectedCount">0</span>)
                        </button>
                        </div>
                    <div id="uploadedFiles" class="drive-grid">
                            <small class="text-muted">No uploaded files yet.</small>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button id="uploadDocs" type="button" class="btn btn-success">
                        <i class="fa-solid fa-upload mr-1"></i> Upload Documents
                    </button>
                </div>

            </div>
        </div>
    </div>

    
<!-- ═══════════════════════════════════════════════════════
  VIEW DOCUMENTS MODAL
═══════════════════════════════════════════════════════ -->
    <!-- DOCUMENT VIEWER MODAL -->
    <div class="modal fade" id="docViewerModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="docViewerTitle">Document</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body text-center" id="docViewerBody" style="min-height:300px;">
                    <div class="text-muted">Loading...</div>
                </div>

            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Page JS -->
<?php $this->load->view('templates/footer'); ?>