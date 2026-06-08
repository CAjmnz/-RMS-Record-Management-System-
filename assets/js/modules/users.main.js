$(function () {

    /* ══════════════════════════════════════════════════════════════════
       CSRF
       ══════════════════════════════════════════════════════════════════ */
    function csrfData() {
        return {
            [$("#csrf_token_name").val()]: $("#csrf_token_value").val()
        };
    }

    /* ══════════════════════════════════════════════════════════════════
       DATATABLES — server-side
       ══════════════════════════════════════════════════════════════════ */
    // ✅ FIX 1: assign to `table` so filters and reset can call table.ajax.reload()
    var table = $('#usersTable').DataTable({
        processing : true,
        serverSide : true,
        ajax: {
            url  : BASE_URL + "users/ajax_list",
            type : "POST",
            // ✅ FIX 3: send filter values with every request
            data : function (d) {
                d.role   = $("#filterRole").val()       || "";
                d.status = $("#filterStatus").val()     || "";
                d.date   = $("#filterDate").val()       || "";
                d.dept   = $("#filterDepartment").val() || "";
            }
        },
        columns: [
            // ✅ FIX 2: col 0 — avatar + name/email merged into one cell
            {
                data      : null,
                orderable : true,
                render    : function (row) {
                    var avatar = row.profile_picture
                        ? row.profile_picture
                        : '<div class="profile-initials-sm">'
                            + (row.firstname ? row.firstname.charAt(0).toUpperCase() : "?")
                          + "</div>";
                    return '<div style="display:flex;align-items:center;gap:10px;">'
                         + avatar + row.user + "</div>";
                }
            },
            { data: "role",       orderable: true  },
            { data: "status",     orderable: true  },
            { data: "contact",    orderable: false },
            { data: "address",    orderable: false },
            { data: "created",    orderable: true  },
            { data: "department", orderable: true  },
            { data: "birthday",   orderable: true  },
            // ✅ FIX 2: actions column (backend returns "" for non-admins)
            { data: "actions",    orderable: false }
        ],
        columnDefs : [{ targets: -1, orderable: false }],
        order      : [[5, "desc"]],
        pageLength : 5,
        lengthMenu : [5, 10, 25, 50],
        language: {
            processing  : '<div class="text-center py-3"><div class="spinner-border text-primary" role="status"></div></div>',
            emptyTable  : "No users found.",
            zeroRecords : "No matching users found."
        },
        // Dropdown toggle after each draw
        drawCallback: function () {
            // close any open menus on redraw
            $(".rms-dropdown-menu.show").removeClass("show");
        }
    });

    /* ══════════════════════════════════════════════════════════════════
       CUSTOM DROPDOWN (no Bootstrap dependency — works in DataTables)
       ══════════════════════════════════════════════════════════════════ */
    // Toggle open/close
    $(document).on("click", ".rms-dropdown-toggle", function (e) {
        e.stopPropagation();
        var $menu = $(this).siblings(".rms-dropdown-menu");
        $(".rms-dropdown-menu.show").not($menu).removeClass("show");
        $menu.toggleClass("show");
    });

    // Close when clicking anywhere else
    $(document).on("click", function () {
        $(".rms-dropdown-menu.show").removeClass("show");
    });

    /* ══════════════════════════════════════════════════════════════════
       COLUMN FILTERS
       ══════════════════════════════════════════════════════════════════ */
    $("#filterRole, #filterStatus, #filterDate, #filterDepartment")
        .on("change input", function () {
            table.ajax.reload();
        });

    // ✅ FIX 4: correct IDs matching the view
    $("#resetFilters").on("click", function () {
        $("#filterRole").val("");
        $("#filterStatus").val("");
        $("#filterDate").val("");
        $("#filterDepartment").val("");
        table.ajax.reload();
    });

    /* ══════════════════════════════════════════════════════════════════
       VALIDATION UI HELPERS
       ══════════════════════════════════════════════════════════════════ */
    var MAX_VISIBLE = 3;

    function setFieldError($input, message) {
        $input.removeClass("is-valid").addClass("is-invalid");
        var $wrap = $input.closest(".field-wrap");
        $wrap.addClass("has-error");
        $wrap.find(".error-tooltip").text(message);
    }

    function setFieldValid($input) {
        $input.removeClass("is-invalid").addClass("is-valid");
        var $wrap = $input.closest(".field-wrap");
        $wrap.removeClass("has-error");
        $wrap.find(".error-tooltip").text("");
    }

    function clearAllErrors(modalId) {
        var $modal = $(modalId);
        $modal.find(".form-control").removeClass("is-invalid is-valid");
        $modal.find(".field-wrap").removeClass("has-error");
        $modal.find(".error-tooltip").text("");
        var alertId = modalId === "#createUserModal" ? "#createAlert" : "#editAlert";
        $(alertId).html("").hide();
    }

    function applyErrors(modalId, alertId, errors, fieldMap) {
        $.each(fieldMap, function (name, $input) {
            if (errors[name]) {
                setFieldError($input, errors[name]);
            } else if ($input.val() !== "") {
                setFieldValid($input);
            }
        });

        var items = [];
        $.each(errors, function (name, msg) {
            var label = labelFor(name);
            var short = msg.replace(/\.$/, "");
            items.push(label + " — " + short);
        });

        if (items.length === 0) return;

        var uid  = alertId.replace("#", "");
        var html = '<div style="font-size:.82rem;line-height:1.5;">';
        html += '<strong style="display:block;margin-bottom:4px;">&#9888; Please fix the following:</strong>';
        html += '<ul class="mb-0 pl-3" id="errList_' + uid + '">';

        items.forEach(function (text, idx) {
            var hidden = idx >= MAX_VISIBLE ? ' class="err-extra d-none"' : "";
            html += "<li" + hidden + ">" + $("<span>").text(text).html() + "</li>";
        });

        html += "</ul>";

        if (items.length > MAX_VISIBLE) {
            var remaining = items.length - MAX_VISIBLE;
            html += '<button type="button" class="btn-see-more" data-expanded="0">'
                  + "See More (" + remaining + " more)</button>";
        }

        html += "</div>";

        $(alertId)
            .html(html)
            .removeClass()
            .addClass("alert alert-danger p-2 mt-1 mb-2")
            .show();

        $(alertId).find(".btn-see-more").on("click", function () {
            var $btn     = $(this);
            var expanded = $btn.data("expanded") === 1 || $btn.data("expanded") === "1";
            var rem      = items.length - MAX_VISIBLE;
            if (expanded) {
                $(alertId).find(".err-extra").addClass("d-none");
                $btn.text("See More (" + rem + " more)").data("expanded", 0);
            } else {
                $(alertId).find(".err-extra").removeClass("d-none");
                $btn.text("See Less").data("expanded", 1);
            }
        });
    }

    function labelFor(name) {
        var map = {
            firstname   : "First Name",
            lastname    : "Last Name",
            employee_id : "Employee ID",
            birthday    : "Birthday",
            contactno   : "Contact No",
            address     : "Address",
            email       : "Email",
            password    : "Password",
            role        : "Role",
            is_active   : "Status",
            job_title   : "Job Title",
            department  : "Department"
        };
        return map[name] || (name.charAt(0).toUpperCase() + name.slice(1));
    }

    /* ══════════════════════════════════════════════════════════════════
       REAL-TIME FIELD FEEDBACK
       ══════════════════════════════════════════════════════════════════ */
    $("#createUserModal, #editUserModal").on("input change", ".form-control", function () {
        var $el = $(this);
        if ($el.hasClass("is-invalid") && $.trim($el.val()) !== "") {
            setFieldValid($el);
        }
    });

    /* ══════════════════════════════════════════════════════════════════
       RESET ON MODAL CLOSE
       ══════════════════════════════════════════════════════════════════ */
    $("#createUserModal").on("hidden.bs.modal", function () {
        $(this).find(".form-control").val("");
        $("#password").val("rms-2026");
        $("#create_role").val("user");
        $("#is_active").val("1");
        clearAllErrors("#createUserModal");
    });

    $("#editUserModal").on("hidden.bs.modal", function () {
        clearAllErrors("#editUserModal");
    });

    /* ══════════════════════════════════════════════════════════════════
       FIELD MAPS
       ══════════════════════════════════════════════════════════════════ */
    function createFieldMap() {
        return {
            firstname   : $("#firstname"),
            lastname    : $("#lastname"),
            employee_id : $("#employee_id"),
            birthday    : $("#birthday"),
            contactno   : $("#contactno"),
            address     : $("#address"),
            email       : $("#email"),
            password    : $("#password"),
            role        : $("#create_role"),
            is_active   : $("#is_active"),
            job_title   : $("#job_title"),
            department  : $("#department")
        };
    }

    function editFieldMap() {
        return {
            firstname   : $("#edit_firstname"),
            lastname    : $("#edit_lastname"),
            employee_id : $("#edit_employee_id"),
            birthday    : $("#edit_birthday"),
            contactno   : $("#edit_contactno"),
            address     : $("#edit_address"),
            email       : $("#edit_email"),
            role        : $("#edit_role"),
            is_active   : $("#edit_is_active"),
            job_title   : $("#edit_job_title"),
            department  : $("#edit_department")
        };
    }

    /* ══════════════════════════════════════════════════════════════════
       CREATE USER
       ══════════════════════════════════════════════════════════════════ */
    window.createUser = function () {
        clearAllErrors("#createUserModal");

        var fm       = createFieldMap();
        var formData = new FormData();

        formData.append("firstname",   fm.firstname.val().trim());
        formData.append("lastname",    fm.lastname.val().trim());
        formData.append("employee_id", fm.employee_id.val().trim());
        formData.append("birthday",    fm.birthday.val());
        formData.append("contactno",   fm.contactno.val().trim());
        formData.append("address",     fm.address.val().trim());
        formData.append("email",       fm.email.val().trim());
        formData.append("password",    fm.password.val());
        formData.append("role",        fm.role.val());
        formData.append("is_active",   fm.is_active.val());
        formData.append("job_title",   fm.job_title.val().trim());
        formData.append("department",  fm.department.val().trim());
        formData.append($("#csrf_token_name").val(), $("#csrf_token_value").val());

        var file = $("#profile_picture")[0].files[0];
        if (file) formData.append("profile_picture", file);

        $.ajax({
            url         : BASE_URL + "users/store",
            method      : "POST",
            data        : formData,
            processData : false,
            contentType : false,
            dataType    : "json",
            success: function (res) {
                if (res.success) {
                    $("#createUserModal").modal("hide");
                    showToast("success", res.message || "User created successfully.");
                    setTimeout(function () { table.ajax.reload(null, false); }, 800);
                } else {
                    applyErrors("#createUserModal", "#createAlert", res.errors || {}, fm);
                    if ($.isEmptyObject(res.errors) && res.message) {
                        $("#createAlert")
                            .html("<strong>&#9888; " + res.message + "</strong>")
                            .removeClass().addClass("alert alert-danger p-2 mt-1 mb-2")
                            .show();
                    }
                }
            },
            error: function () {
                $("#createAlert")
                    .html("<strong>&#9888; Server error. Please try again.</strong>")
                    .removeClass().addClass("alert alert-danger p-2 mt-1 mb-2")
                    .show();
            }
        });
    };

    /* ══════════════════════════════════════════════════════════════════
       EDIT USER — load data into modal
       ══════════════════════════════════════════════════════════════════ */
    $(document).on("click", ".btn-edit", function () {
        var id = $(this).data("id");
        $(".rms-dropdown-menu.show").removeClass("show");
        clearAllErrors("#editUserModal");

        $.ajax({
            url      : BASE_URL + "users/get/" + id,
            method   : "GET",
            dataType : "json",
            success: function (res) {
                if (!res.success) {
                    showToast("danger", res.message || "Failed to load user.");
                    return;
                }
                var u = res.data;
                $("#edit_id").val(u.id);
                $("#edit_firstname").val(u.firstname);
                $("#edit_lastname").val(u.lastname);
                $("#edit_employee_id").val(u.employee_id);
                $("#edit_birthday").val(u.birthday);
                $("#edit_contactno").val(u.contactno);
                $("#edit_address").val(u.address);
                $("#edit_email").val(u.email);
                $("#edit_role").val(u.role);
                $("#edit_is_active").val(u.is_active);
                $("#edit_job_title").val(u.job_title);
                $("#edit_department").val(u.department);
                $("#editUserModal").modal("show");
            },
            error: function () {
                showToast("danger", "Server error loading user.");
            }
        });
    });

    /* ══════════════════════════════════════════════════════════════════
       UPDATE USER
       ══════════════════════════════════════════════════════════════════ */
    window.updateUser = function () {
        clearAllErrors("#editUserModal");

        var fm       = editFieldMap();
        var formData = new FormData();

        formData.append("id",          $("#edit_id").val());
        formData.append("firstname",   fm.firstname.val().trim());
        formData.append("lastname",    fm.lastname.val().trim());
        formData.append("employee_id", fm.employee_id.val().trim());
        formData.append("birthday",    fm.birthday.val());
        formData.append("contactno",   fm.contactno.val().trim());
        formData.append("address",     fm.address.val().trim());
        formData.append("email",       fm.email.val().trim());
        formData.append("role",        fm.role.val());
        formData.append("is_active",   fm.is_active.val());
        formData.append("job_title",   fm.job_title.val().trim());
        formData.append("department",  fm.department.val().trim());
        formData.append($("#csrf_token_name").val(), $("#csrf_token_value").val());

        var file = $("#edit_profile_picture")[0] && $("#edit_profile_picture")[0].files[0];
        if (file) formData.append("profile_picture", file);

        $.ajax({
            url         : BASE_URL + "users/update",
            method      : "POST",
            data        : formData,
            processData : false,
            contentType : false,
            dataType    : "json",
            success: function (res) {
                if (res.success) {
                    $("#editUserModal").modal("hide");
                    showToast("success", res.message || "User updated successfully.");
                    setTimeout(function () { table.ajax.reload(null, false); }, 800);
                } else {
                    applyErrors("#editUserModal", "#editAlert", res.errors || {}, fm);
                    if ($.isEmptyObject(res.errors) && res.message) {
                        $("#editAlert")
                            .html("<strong>&#9888; " + res.message + "</strong>")
                            .removeClass().addClass("alert alert-danger p-2 mt-1 mb-2")
                            .show();
                    }
                }
            },
            error: function () {
                $("#editAlert")
                    .html("<strong>&#9888; Server error. Please try again.</strong>")
                    .removeClass().addClass("alert alert-danger p-2 mt-1 mb-2")
                    .show();
            }
        });
    };

    /* ══════════════════════════════════════════════════════════════════
       DELETE USER
       ══════════════════════════════════════════════════════════════════ */
    $(document).on("click", ".btn-delete", function () {
        var id = $(this).data("id");
        $(".rms-dropdown-menu.show").removeClass("show");

        Swal.fire({
            title              : "Are you sure?",
            text               : "This user will be soft-deleted.",
            icon               : "warning",
            showCancelButton   : true,
            confirmButtonColor : "#d33",
            cancelButtonColor  : "#3085d6",
            confirmButtonText  : "Yes, delete it!"
        }).then(function (result) {
            if (!result.isConfirmed) return;

            $.ajax({
                url      : BASE_URL + "users/delete/" + id,
                method   : "POST",
                data     : csrfData(),
                dataType : "json",
                success: function (res) {
                    if (res.success) {
                        showToast("success", res.message || "User deleted.");
                        setTimeout(function () { table.ajax.reload(null, false); }, 800);
                    } else {
                        showToast("danger", res.message || "Delete failed.");
                    }
                },
                error: function () {
                    showToast("danger", "Server error. Please try again.");
                }
            });
        });
    });

    /* ══════════════════════════════════════════════════════════════════
       TOAST NOTIFICATION
       ══════════════════════════════════════════════════════════════════ */
    function showToast(type, message) {
        var bg = {
            success : "#28a745",
            danger  : "#dc3545",
            warning : "#ffc107",
            info    : "#17a2b8"
        };

        var $toast = $("<div>")
            .css({
                position     : "fixed",
                bottom       : "24px",
                right        : "24px",
                zIndex       : 99999,
                background   : bg[type] || "#333",
                color        : "#fff",
                padding      : "10px 18px",
                borderRadius : "6px",
                fontSize     : ".875rem",
                boxShadow    : "0 4px 12px rgba(0,0,0,.25)",
                maxWidth     : "320px",
                opacity      : 0,
                transition   : "opacity .25s"
            })
            .text(message)
            .appendTo("body");

        setTimeout(function () { $toast.css("opacity", 1); }, 20);
        setTimeout(function () {
            $toast.css("opacity", 0);
            setTimeout(function () { $toast.remove(); }, 300);
        }, 3200);
    }

});