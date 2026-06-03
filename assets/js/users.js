$(document).ready(function () {

    // DATATABLE

    // RESET CREATE MODAL
    $('#createUserModal').on('hidden.bs.modal', function () {
        clearValidation('create');
        $('#createAlert').html('');
        $(this).find('input').val('');
        $(this).find('select').prop('selectedIndex', 0);
    });

    // RESET EDIT MODAL
    $('#editUserModal').on('hidden.bs.modal', function () {
        clearValidation('edit');
        $('#editAlert').html('');
    });

    // LIVE INPUT CLEAN ERROR
    $(document).on('input change', '.field-wrap input, .field-wrap select', function () {
        const wrap = $(this).closest('.field-wrap');
        $(this).removeClass('is-invalid');
        wrap.removeClass('has-error');
        wrap.find('.error-tooltip').text('');
    });

});


// CSRF
function csrfData() {
    return {
        [$('#csrf_token_name').val()]: $('#csrf_token_value').val()
    };
}


// CLEAR VALIDATION
function clearValidation(type) {
    const modal = type === 'edit' ? '#editUserModal' : '#createUserModal';

    $(modal).find('.field-wrap').each(function () {
        $(this).removeClass('has-error');
        $(this).find('input, select').removeClass('is-invalid');
        $(this).find('.error-tooltip').text('');
    });
}


// SHOW FIELD ERROR
function showFieldError(selector, message)
{
    let input = $(selector);

    input.addClass('is-invalid');

    input.closest('.field-wrap')
         .find('.error-tooltip')
         .text(message);
}


// VALIDATION
function validateFields(prefix, alertSelector) {

    clearValidation(prefix === '' ? 'create' : 'edit');
    $(alertSelector).html('');

    const p = '#' + prefix;
    let hasErr = false;

    const required = [
        'firstname','lastname','employee_id','birthday',
        'contactno','address','email','job_title','department'
    ];

    required.forEach(function (field) {
        const el = $(p + field);
        if (!el.length || !el.val() || !el.val().trim()) {
            showFieldError(p + field, field + ' is required.');
            hasErr = true;
        }
    });

    const email = $(p + 'email').val()?.trim();
    const emailRx = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (email && !emailRx.test(email)) {
        showFieldError(p + 'email', 'Invalid email format.');
        hasErr = true;
    }

    const contact = $(p + 'contactno').val()?.trim();
    if (contact && !/^[0-9]{11}$/.test(contact)) {
        showFieldError(p + 'contactno', 'Must be 11 digits.');
        hasErr = true;
    }

    if (hasErr) {
        $(alertSelector).html('<div class="alert alert-danger">Fix errors first.</div>');
    }

    return hasErr;
}


// CREATE USER
function createUser() {

    if (validateFields('', '#createAlert')) return;

    $.post(base_url + "users/store",
        Object.assign(csrfData(), {
            firstname: $('#firstname').val().trim(),
            lastname: $('#lastname').val().trim(),
            employee_id: $('#employee_id').val().trim(),
            birthday: $('#birthday').val(),
            contactno: $('#contactno').val().trim(),
            address: $('#address').val().trim(),
            email: $('#email').val().trim(),
            password: $('#password').val(),
            role: $('#create_role').val(),
            is_active: $('#is_active').val(),
            job_title: $('#job_title').val().trim(),
            department: $('#department').val().trim()
        }),
        function (res) {

            if (res.success) {
                Swal.fire('Created!', res.message, 'success');
                setTimeout(() => location.reload(), 1200);
                return;
            }

            if (res.errors) {
                applyBackendErrors(res.errors, '', '#createAlert');
                return;
            }

            $('#createAlert').html('<div class="alert alert-danger">' + res.message + '</div>');
        },
        'json'
    );
}


// EDIT USER
function editUser(id) {

    clearValidation('edit');
    $('#editAlert').html('');

    $.get(base_url + "users/get/" + id, function (res) {

        if (!res.success) return;

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


// UPDATE USER
function updateUser() {

    if (validateFields('edit_', '#editAlert')) return;

    $.post(base_url + "users/update",
        Object.assign(csrfData(), {
            id: $('#edit_id').val(),
            firstname: $('#edit_firstname').val().trim(),
            lastname: $('#edit_lastname').val().trim(),
            employee_id: $('#edit_employee_id').val().trim(),
            birthday: $('#edit_birthday').val(),
            contactno: $('#edit_contactno').val().trim(),
            address: $('#edit_address').val().trim(),
            email: $('#edit_email').val().trim(),
            role: $('#edit_role').val(),
            is_active: $('#edit_is_active').val(),
            job_title: $('#edit_job_title').val().trim(),
            department: $('#edit_department').val().trim()
        }),
        function (res) {

            if (res.success) {
                Swal.fire('Updated!', res.message, 'success');
                setTimeout(() => location.reload(), 1200);
                return;
            }

            if (res.errors) {
                applyBackendErrors(res.errors, 'edit_', '#editAlert');
                return;
            }

            $('#editAlert').html('<div class="alert alert-danger">' + res.message + '</div>');
        },
        'json'
    );
}


// DELETE USER
function deleteUser(id) {

    Swal.fire({
        title: 'Delete user?',
        text: 'This action is soft delete.',
        icon: 'warning',
        showCancelButton: true
    }).then((result) => {

        if (!result.isConfirmed) return;

        $.post(base_url + "users/delete/" + id,
            csrfData(),
            function (res) {

                if (res.success) {
                    Swal.fire('Deleted!', res.message, 'success');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    Swal.fire('Error', res.message, 'error');
                }

            }, 'json');

    });
}