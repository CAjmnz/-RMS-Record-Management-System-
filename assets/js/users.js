window.UsersUI = (function () {

    var dt = null;

    // ─── Init Server-Side DataTable ───────────────────────────────────
    function initDataTable() {
        if ($.fn.DataTable.isDataTable('#usersTable')) {
            $('#usersTable').DataTable().destroy();
        }

        dt = $('#usersTable').DataTable({
            processing : true,
            serverSide : true,
            ajax: {
                url  : BASE_URL + 'users/ajax_list',
                type : 'POST',
                data : function (d) {
                    d.role   = $('#filterRole').val()       || '';
                    d.status = $('#filterStatus').val()     || '';
                    d.date   = $('#filterDate').val()       || '';
                    d.dept   = $('#filterDepartment').val() || '';
                }
            },

            columns: [
                // col 0 — User: avatar + name + email merged
                {
                    data      : null,
                    orderable : true,
                    render    : function (data) {
                        var avatar = data.profile_picture
                            ? data.profile_picture
                            : '<div class="profile-initials-sm">'
                                + data.user.charAt(0).toUpperCase()
                              + '</div>';
                        return '<div style="display:flex;align-items:center;gap:10px;">'
                             + avatar
                             + data.user
                             + '</div>';
                    }
                },
                { data: 'role',       orderable: true  },
                { data: 'status',     orderable: true  },
                { data: 'contact',    orderable: false },
                { data: 'address',    orderable: false },
                { data: 'created',    orderable: true  },
                { data: 'department', orderable: true  },
                { data: 'birthday',   orderable: true  },
                { data: 'actions',    orderable: false }
            ],

            columnDefs : [{ targets: -1, orderable: false }],
            order      : [[5, 'desc']],
            pageLength : 10,
            lengthMenu : [5, 10, 25, 50],

            language: {
                processing  : '<div class="text-center py-3"><div class="spinner-border text-primary" role="status"></div></div>',
                emptyTable  : 'No users found.',
                zeroRecords : 'No matching users found.'
            },

            drawCallback: function () { /* dropdown handled by delegated click below */ }
        });

        // Custom filters
        $(document).on('change input', '#filterRole, #filterStatus, #filterDate, #filterDepartment', function () {
            dt.ajax.reload();
        });

        $('#resetFilters').on('click', function () {
            $('#filterRole, #filterStatus').val('');
            $('#filterDate, #filterDepartment').val('');
            dt.ajax.reload();
        });

        // ── Delegated: Edit button ────────────────────────────────────
        $(document).on('click', '.btn-edit', function () {
            var id = $(this).data('id');
            loadEditUser(id);
        });

        // ── Delegated: Delete button ──────────────────────────────────
        $(document).on('click', '.btn-delete', function () {
            var id = $(this).data('id');
            deleteUser(id);
        });

        // ── Custom dropdown toggle (no Bootstrap dependency) ─────────
        $(document).on("click", ".rms-dropdown-toggle", function (e) {
            e.stopPropagation();
            var menu = $(this).siblings(".rms-dropdown-menu");
            $(".rms-dropdown-menu.show").not(menu).removeClass("show");
            menu.toggleClass("show");
        });

        // Close dropdown when clicking anywhere else
        $(document).on("click", function () {
            $(".rms-dropdown-menu.show").removeClass("show");
        });
    }

    // ─── Reload (no page jump) ────────────────────────────────────────
    function reloadTable() {
        if (dt) dt.ajax.reload(null, false);
    }

    // ─── Load user into edit modal ────────────────────────────────────
    function loadEditUser(id) {
        $.get(BASE_URL + 'users/get/' + id, function (res) {
            if (!res.success) {
                Swal.fire('Error', res.message, 'error');
                return;
            }
            var u = res.data;
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

    // ─── Modal reset handlers ─────────────────────────────────────────
    function resetModals() {
        $('#createUserModal').on('hidden.bs.modal', function () {
            if (window.UsersValidation) UsersValidation.clearValidation('create');
            $('#createAlert').html('').hide();
            $(this).find('input:not([type=hidden])').val('');
            $(this).find('select').prop('selectedIndex', 0);
        });

        $('#editUserModal').on('hidden.bs.modal', function () {
            if (window.UsersValidation) UsersValidation.clearValidation('edit');
            $('#editAlert').html('').hide();
        });
    }

    // ─── Live validation clear ────────────────────────────────────────
    function bindLiveValidation() {
        $(document).on('input change', '.field-wrap input, .field-wrap select', function () {
            var wrap = $(this).closest('.field-wrap');
            $(this).removeClass('is-invalid');
            wrap.removeClass('has-error');
            wrap.find('.error-tooltip').text('');
        });
    }

    // ─── Success toast → reload table (no page refresh) ──────────────
    function showSuccess(message) {
        Swal.fire({
            title             : 'Success',
            text              : message,
            icon              : 'success',
            timer             : 1500,
            showConfirmButton : false
        }).then(function () {
            reloadTable();
        });
    }

    return {
        initDataTable      : initDataTable,
        reloadTable        : reloadTable,
        resetModals        : resetModals,
        bindLiveValidation : bindLiveValidation,
        showSuccess        : showSuccess
    };

})();