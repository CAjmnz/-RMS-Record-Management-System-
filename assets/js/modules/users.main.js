/**
 * users.main.js
 * Handles: Create, Edit, Delete users via AJAX
 * Validation UI: field-wrap / field-error-icon / error-tooltip pattern
 * Error summary: compact alert, 3-line cap with See More / See Less toggle
 */

$(function () {

    /* ══════════════════════════════════════════════════════════════════
       CSRF
       ══════════════════════════════════════════════════════════════════ */
    function csrfData() {
        return {
            [$("#csrf_token_name").val()]: $("#csrf_token_value").val()
        };
    }

    // Refresh CSRF token from server response headers isn't available in CI3
    // so we reload on success instead of re-using the token.


    /* ══════════════════════════════════════════════════════════════════
       COLUMN FILTERS  (Role / Status / Date / Department)
       ══════════════════════════════════════════════════════════════════ */
    function applyFilters() {
        var role       = $("#filterRole").val().toLowerCase();
        var status     = $("#filterStatus").val();
        var dateFilter = $("#filterDate").val();
        var dept       = $("#filterDepartment").val().toLowerCase().trim();

        $("#usersTable tbody tr").each(function () {
            var $row    = $(this);
            var cells   = $row.find("td");

            // If no-data row, always show
            if (cells.length <= 1) { $row.show(); return; }

            var rowRole   = cells.eq(2).text().trim().toLowerCase();
            var rowStatus = cells.eq(3).text().trim().toLowerCase();
            var rowDate   = cells.eq(6).attr("data-date") || "";
            var rowDept   = cells.eq(7).text().trim().toLowerCase();

            var show = true;

            if (role   && rowRole.indexOf(role)     === -1) show = false;
            if (status !== "") {
                var wantActive = status === "1";
                var isActive   = rowStatus === "active";
                if (wantActive !== isActive) show = false;
            }
            if (dateFilter && rowDate.indexOf(dateFilter) === -1) show = false;
            if (dept   && rowDept.indexOf(dept)      === -1) show = false;

            $row.toggle(show);
        });
    }

    $("#filterRole, #filterStatus, #filterDate, #filterDepartment").on("change input", applyFilters);

    $("#resetFilters").on("click", function () {
        $("#filterRole, #filterStatus, #filterDate, #filterDepartment").val("");
        applyFilters();
    });


    /* ══════════════════════════════════════════════════════════════════
       VALIDATION UI HELPERS
       ══════════════════════════════════════════════════════════════════ */
    var MAX_VISIBLE = 3;

    /**
     * Mark one field as invalid: red border + tooltip icon.
     * @param {jQuery} $input  - the input/select element
     * @param {string} message - error message for the tooltip
     */
    function setFieldError($input, message) {
        $input
            .removeClass("is-valid")
            .addClass("is-invalid");

        var $wrap = $input.closest(".field-wrap");
        $wrap.addClass("has-error");
        $wrap.find(".error-tooltip").text(message);
    }

    /**
     * Mark one field as valid: green border, remove icon.
     */
    function setFieldValid($input) {
        $input
            .removeClass("is-invalid")
            .addClass("is-valid");

        var $wrap = $input.closest(".field-wrap");
        $wrap.removeClass("has-error");
        $wrap.find(".error-tooltip").text("");
    }

    /**
     * Clear all validation state inside a modal.
     * @param {string} modalId - e.g. "#createUserModal"
     */
    function clearAllErrors(modalId) {
        var $modal = $(modalId);

        $modal.find(".form-control")
              .removeClass("is-invalid is-valid");

        $modal.find(".field-wrap")
              .removeClass("has-error");

        $modal.find(".error-tooltip")
              .text("");

        // Clear summary alert
        var alertId = modalId === "#createUserModal" ? "#createAlert" : "#editAlert";
        $(alertId).html("").hide();
    }

    /**
     * Apply a full errors object to a modal's fields + summary box.
     *
     * @param {string} modalId  - "#createUserModal" or "#editUserModal"
     * @param {string} alertId  - "#createAlert"     or "#editAlert"
     * @param {object} errors   - { fieldName: "message", ... }
     * @param {object} fieldMap - { fieldName: jQuery $input, ... }
     */
    function applyErrors(modalId, alertId, errors, fieldMap) {

        // ── Per-field UI ──────────────────────────────────────────────
        $.each(fieldMap, function (name, $input) {
            if (errors[name]) {
                setFieldError($input, errors[name]);
            } else if ($input.val() !== "") {
                // Only green if the field has a value and no error
                setFieldValid($input);
            }
        });

        // ── Build summary list ────────────────────────────────────────
        var items = [];
        $.each(errors, function (name, msg) {
            var label = labelFor(name);
            // Shorten message: remove trailing period for compactness
            var short = msg.replace(/\.$/, "");
            items.push(label + " — " + short);
        });

        if (items.length === 0) return;

        var $alert = $(alertId);
        var html   = '<div style="font-size:.82rem;line-height:1.5;">';
        html += '<strong style="display:block;margin-bottom:4px;">&#9888; Please fix the following:</strong>';
        html += '<ul class="mb-0 pl-3" id="errSummaryList_' + alertId.replace('#','') + '">';

        items.forEach(function (text, idx) {
            var hidden = idx >= MAX_VISIBLE ? ' class="err-extra d-none"' : '';
            html += '<li' + hidden + '>' + $('<span>').text(text).html() + '</li>';
        });

        html += '</ul>';

        if (items.length > MAX_VISIBLE) {
            var remaining = items.length - MAX_VISIBLE;
            html += '<button type="button" class="btn-see-more" data-expanded="0">'
                  + 'See More (' + remaining + ' more)</button>';
        }

        html += '</div>';

        $alert
            .html(html)
            .removeClass()
            .addClass("alert alert-danger p-2 mt-1 mb-2")
            .show();

        // ── See More / See Less toggle ────────────────────────────────
        $alert.find(".btn-see-more").on("click", function () {
            var $btn      = $(this);
            var expanded  = $btn.data("expanded") === 1 || $btn.data("expanded") === "1";
            var remaining = items.length - MAX_VISIBLE;

            if (expanded) {
                $alert.find(".err-extra").addClass("d-none");
                $btn.text("See More (" + remaining + " more)").data("expanded", 0);
            } else {
                $alert.find(".err-extra").removeClass("d-none");
                $btn.text("See Less").data("expanded", 1);
            }
        });
    }

    /** Human-readable label for each field name */
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
       Red → green as user corrects each field
       ══════════════════════════════════════════════════════════════════ */
    $("#createUserModal, #editUserModal").on("input change", ".form-control", function () {
        var $el = $(this);
        if ($el.hasClass("is-invalid")) {
            if ($.trim($el.val()) !== "") {
                setFieldValid($el);
            }
        }
    });


    /* ══════════════════════════════════════════════════════════════════
       RESET ON MODAL CLOSE
       ══════════════════════════════════════════════════════════════════ */
    $("#createUserModal").on("hidden.bs.modal", function () {
        $(this).find(".form-control").val("");
        // Reset password default
        $("#password").val("rms-2026");
        $("#create_role").val("user");
        $("#is_active").val("1");
        clearAllErrors("#createUserModal");
    });

    $("#editUserModal").on("hidden.bs.modal", function () {
        clearAllErrors("#editUserModal");
    });


    /* ══════════════════════════════════════════════════════════════════
       CREATE USER
       ══════════════════════════════════════════════════════════════════ */

    // Field map: name → jQuery object (create modal)
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
            department  : $("#department"),
        };
    }

    window.createUser = function () {
        clearAllErrors("#createUserModal");

        var fm = createFieldMap();

        var payload = $.extend({}, csrfData(), {
            firstname   : fm.firstname.val(),
            lastname    : fm.lastname.val(),
            employee_id : fm.employee_id.val(),
            birthday    : fm.birthday.val(),
            contactno   : fm.contactno.val(),
            address     : fm.address.val(),
            email       : fm.email.val(),
            password    : fm.password.val(),
            role        : fm.role.val(),
            is_active   : fm.is_active.val(),
            job_title   : fm.job_title.val(),
            department  : fm.department.val(),
        });

        $.ajax({
            url     : BASE_URL + "users/store",
            method  : "POST",
            data    : payload,
            dataType: "json",
            success : function (res) {
                if (res.success) {
                    $("#createUserModal").modal("hide");
                    showToast("success", res.message);
                    setTimeout(function () { location.reload(); }, 1000);
                } else {
                    applyErrors("#createUserModal", "#createAlert", res.errors || {}, fm);
                    if ($.isEmptyObject(res.errors) && res.message) {
                        $("#createAlert")
                            .html('<strong>&#9888; ' + res.message + '</strong>')
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
       EDIT USER — Load data into modal
       ══════════════════════════════════════════════════════════════════ */
    $(document).on("click", ".btn-edit", function () {
        var id = $(this).data("id");
        clearAllErrors("#editUserModal");

        $.ajax({
            url     : BASE_URL + "users/get/" + id,
            method  : "GET",
            dataType: "json",
            success : function (res) {
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

    // Field map: name → jQuery object (edit modal)
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
            department  : $("#edit_department"),
        };
    }

    window.updateUser = function () {
        clearAllErrors("#editUserModal");

        var fm = editFieldMap();

        var payload = $.extend({}, csrfData(), {
            id          : $("#edit_id").val(),
            firstname   : fm.firstname.val(),
            lastname    : fm.lastname.val(),
            employee_id : fm.employee_id.val(),
            birthday    : fm.birthday.val(),
            contactno   : fm.contactno.val(),
            address     : fm.address.val(),
            email       : fm.email.val(),
            role        : fm.role.val(),
            is_active   : fm.is_active.val(),
            job_title   : fm.job_title.val(),
            department  : fm.department.val(),
        });

        $.ajax({
            url     : BASE_URL + "users/update",
            method  : "POST",
            data    : payload,
            dataType: "json",
            success : function (res) {
                if (res.success) {
                    $("#editUserModal").modal("hide");
                    showToast("success", res.message);
                    setTimeout(function () { location.reload(); }, 1000);
                } else {
                    applyErrors("#editUserModal", "#editAlert", res.errors || {}, fm);
                    if ($.isEmptyObject(res.errors) && res.message) {
                        $("#editAlert")
                            .html('<strong>&#9888; ' + res.message + '</strong>')
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
    window.deleteUser = function (id) {
        if (!confirm("Are you sure you want to delete this user? This cannot be undone.")) return;

        $.ajax({
            url     : BASE_URL + "users/delete/" + id,
            method  : "POST",
            data    : csrfData(),
            dataType: "json",
            success : function (res) {
                if (res.success) {
                    showToast("success", res.message);
                    setTimeout(function () { location.reload(); }, 1000);
                } else {
                    showToast("danger", res.message || "Delete failed.");
                }
            },
            error: function () {
                showToast("danger", "Server error. Please try again.");
            }
        });
    };


    /* ══════════════════════════════════════════════════════════════════
       TOAST NOTIFICATION
       ══════════════════════════════════════════════════════════════════ */
    function showToast(type, message) {
        var bg = { success: "#28a745", danger: "#dc3545", warning: "#ffc107", info: "#17a2b8" };

        var $toast = $("<div>")
            .css({
                position    : "fixed",
                bottom      : "24px",
                right       : "24px",
                zIndex      : 99999,
                background  : bg[type] || "#333",
                color       : "#fff",
                padding     : "10px 18px",
                borderRadius: "6px",
                fontSize    : ".875rem",
                boxShadow   : "0 4px 12px rgba(0,0,0,.25)",
                maxWidth    : "320px",
                opacity     : 0,
                transition  : "opacity .25s",
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