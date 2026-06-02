// ── CSRF helper ─────────────────────────────
function csrfData() {
    return AppConfig.csrfData();
}

// ── INIT ─────────────────────────────────────
$(document).ready(function () {

    if ($('#usersTable').length) {
        $('#usersTable').DataTable({
            pageLength: 5,
            lengthMenu: [5, 10, 25, 50, 100],
            order: [],
            columnDefs: [{ orderable: false, targets: -1 }]
        });
    }

    // reset create modal
    $('#createUserModal').on('hidden.bs.modal', function () {
        clearValidation('#createUserModal');
        $('#createAlert').html('');
        $(this).find('input, select').val('');
    });

    // reset edit modal
    $('#editUserModal').on('hidden.bs.modal', function () {
        clearValidation('#editUserModal');
        $('#editAlert').html('');
    });

});

// ── CLEAR VALIDATION ─────────────────────────
function clearValidation(modal) {
    $(modal).find('.is-invalid').removeClass('is-invalid');
    $(modal).find('.error-text').text('');
}

// ── SHOW FIELD ERROR ─────────────────────────
function showFieldError(selector, message) {
    $(selector).addClass('is-invalid');
    $(selector).closest('.form-group')
        .find('.error-text')
        .text(message);
}

// ── BACKEND ERROR HANDLER ────────────────────
function applyBackendErrors(errors, prefix, alertBox) {

    clearValidation('#createUserModal');
    clearValidation('#editUserModal');

    $.each(errors, function (field, msg) {
        showFieldError('#' + prefix + field, msg);
    });

    $(alertBox).html(`
        <div class="alert alert-danger">
            Please fix the highlighted fields.
        </div>
    `);
}

// ── CREATE USER ──────────────────────────────
function createUser() {

    let payload = {
        ...csrfData(),
        firstname: $('#firstname').val(),
        lastname: $('#lastname').val(),
        employee_id: $('#employee_id').val(),
        birthday: $('#birthday').val(),
        contactno: $('#contactno').val(),
        address: $('#address').val(),
        email: $('#email').val(),
        password: $('#password').val(),
        role: $('#create_role').val(),
        is_active: $('#is_active').val(),
        job_title: $('#job_title').val(),
        department: $('#department').val()
    };

    $.post(base_url + "users/store", payload, function (res) {

        if (res.success) {
            Swal.fire("Success", res.message, "success");
            setTimeout(() => location.reload(), 1000);
            return;
        }

        if (res.errors) {
            applyBackendErrors(res.errors, '', '#createAlert');
            return;
        }

        $('#createAlert').html(
            `<div class="alert alert-danger">${res.message}</div>`
        );

    }, "json");

}

// ── UPDATE USER ──────────────────────────────
function updateUser() {

    let payload = {
        ...csrfData(),
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
    };

    $.post(base_url + "users/update", payload, function (res) {

        if (res.success) {
            Swal.fire("Updated", res.message, "success");
            setTimeout(() => location.reload(), 1000);
            return;
        }

        if (res.errors) {
            applyBackendErrors(res.errors, 'edit_', '#editAlert');
            return;
        }

        $('#editAlert').html(
            `<div class="alert alert-danger">${res.message}</div>`
        );

    }, "json");
}

// ── DELETE ───────────────────────────────────
function deleteUser(id) {

    Swal.fire({
        title: "Delete user?",
        icon: "warning",
        showCancelButton: true
    }).then((result) => {

        if (!result.isConfirmed) return;

        $.post(base_url + "users/delete/" + id, csrfData(), function (res) {

            if (res.success) {
                Swal.fire("Deleted", res.message, "success");
                setTimeout(() => location.reload(), 1000);
            } else {
                Swal.fire("Error", res.message, "error");
            }

        }, "json");

    });

}