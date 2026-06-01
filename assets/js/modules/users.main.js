// ── CSRF helper ───────────────────────────────────────────────────────────────
function csrfData() {
    return AppConfig.csrfData();
}

// ── DataTable init ────────────────────────────────────────────────────────────
$(document).ready(function () {

    if ($('#usersTable').length) {
        $('#usersTable').DataTable({
            pageLength: 5,
            lengthMenu: [5, 10, 25, 50, 100],
            order: [],
            columnDefs: [{ orderable: false, targets: -1 }],
            language: {
                search:       'Search:',
                lengthMenu:   'Show _MENU_ entries',
                info:         'Showing _START_ to _END_ of _TOTAL_ entries',
                infoEmpty:    'Showing 0 to 0 of 0 entries',
                infoFiltered: '(filtered from _MAX_ total entries)',
                paginate: {
                    first:    'First',
                    last:     'Last',
                    next:     '&raquo;',
                    previous: '&laquo;'
                }
            }
        });
    }

    // Reset Create modal on close
    $('#createUserModal').on('hidden.bs.modal', function () {
        clearValidation('create');
        $('#createAlert').html('');
        $(this).find('input:not([type=hidden])').val('');
        $(this).find('select').prop('selectedIndex', 0);
    });

    // Reset Edit modal on close
    $('#editUserModal').on('hidden.bs.modal', function () {
        clearValidation('edit');
        $('#editAlert').html('');
    });

    // Clear error while typing
    $(document).on('input change', '.field-wrap input, .field-wrap select', function () {
        var wrap = $(this).closest('.field-wrap');
        $(this).removeClass('is-invalid');
        wrap.removeClass('has-error');
        wrap.find('.error-tooltip').text('');
    });

});

// ── Validation helpers ────────────────────────────────────────────────────────
function clearValidation(formType) {
    var modal = formType === 'edit' ? '#editUserModal' : '#createUserModal';
    $(modal).find('.field-wrap').each(function () {
        $(this).removeClass('has-error');
        $(this).find('input, select').removeClass('is-invalid');
        $(this).find('.error-tooltip').text('');
    });
}

function showFieldError(selector, message) {
    var input = $(selector);
    var wrap  = input.closest('.field-wrap');
    input.addClass('is-invalid');
    wrap.addClass('has-error');
    wrap.find('.error-tooltip').text(message);
}

function applyBackendErrors(errors, prefix, alertSelector) {
    Object.keys(errors).forEach(function (field) {
        showFieldError('#' + prefix + field, errors[field]);
    });

    var firstError = $('.field-wrap.has-error').first();
    if (firstError.length) {
        firstError.closest('.modal-body').scrollTop(
            firstError.position().top - 20
        );
        firstError.find('input, select').first().focus();
    }

    $(alertSelector).html(
        '<div class="alert alert-danger">Please fix the highlighted fields.</div>'
    );
}

function validateFields(prefix, alertSelector) {
    clearValidation(prefix === '' ? 'create' : 'edit');
    $(alertSelector).html('');

    var p       = '#' + prefix;
    var emailRx = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var hasErr  = false;

    var checks = [
        { id: 'firstname',   msg: 'First name is required.'     },
        { id: 'lastname',    msg: 'Last name is required.'      },
        { id: 'employee_id', msg: 'Employee ID is required.'    },
        { id: 'birthday',    msg: 'Birthday is required.'       },
        { id: 'contactno',   msg: 'Contact number is required.' },
        { id: 'address',     msg: 'Address is required.'        },
        { id: 'email',       msg: 'Email is required.'          },
        { id: 'job_title',   msg: 'Job title is required.'      },
        { id: 'department',  msg: 'Department is required.'     }
    ];

    checks.forEach(function (c) {
        if (!$(p + c.id).val().trim()) {
            showFieldError(p + c.id, c.msg);
            hasErr = true;
        }
    });

    var contactno = $(p + 'contactno').val().trim();
    if (contactno && !/^[0-9]{11}$/.test(contactno)) {
        showFieldError(p + 'contactno', 'Must be exactly 11 numeric digits.');
        hasErr = true;
    }

    var email = $(p + 'email').val().trim();
    if (email && !emailRx.test(email)) {
        showFieldError(p + 'email', 'Invalid email format.');
        hasErr = true;
    }

    if (hasErr) {
        $(alertSelector).html(
            '<div class="alert alert-danger">Please fix the highlighted fields.</div>'
        );
    }

    return hasErr;
}

// ── Create ────────────────────────────────────────────────────────────────────
function createUser() {
    if (validateFields('', '#createAlert')) return;

    $.post(
        base_url + 'users/store',
        Object.assign(csrfData(), {
            firstname:   $('#firstname').val().trim(),
            lastname:    $('#lastname').val().trim(),
            employee_id: $('#employee_id').val().trim(),
            birthday:    $('#birthday').val(),
            contactno:   $('#contactno').val().trim(),
            address:     $('#address').val().trim(),
            email:       $('#email').val().trim(),
            password:    $('#password').val(),
            role:        $('#create_role').val(),
            is_active:   $('#is_active').val(),
            job_title:   $('#job_title').val().trim(),
            department:  $('#department').val().trim()
        }),
        function (res) {
            if (res.success) {
                Swal.fire({
                    title: 'Created!',
                    text:  res.message || 'User created successfully.',
                    icon:  'success',
                    timer: 1500,
                    showConfirmButton: false
                });
                setTimeout(function () { location.reload(); }, 1500);
                return;
            }

            if (res.errors && Object.keys(res.errors).length) {
                applyBackendErrors(res.errors, '', '#createAlert');
                return;
            }

            $('#createAlert').html(
                '<div class="alert alert-danger">' +
                (res.message || 'Create failed.') + '</div>'
            );
        },
        'json'
    ).fail(function (xhr) {
        $('#createAlert').html(
            '<div class="alert alert-danger">Server error (' + xhr.status + ').</div>'
        );
    });
}

// ── Edit ──────────────────────────────────────────────────────────────────────
function editUser(id) {
    clearValidation('edit');
    $('#editAlert').html('');

    $.get(base_url + 'users/get/' + id, function (res) {
        if (!res.success) return;
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

// ── Update ────────────────────────────────────────────────────────────────────
function updateUser() {
    if (validateFields('edit_', '#editAlert')) return;

    $.post(
        base_url + 'users/update',
        Object.assign(csrfData(), {
            id:          $('#edit_id').val(),
            firstname:   $('#edit_firstname').val().trim(),
            lastname:    $('#edit_lastname').val().trim(),
            employee_id: $('#edit_employee_id').val().trim(),
            birthday:    $('#edit_birthday').val(),
            contactno:   $('#edit_contactno').val().trim(),
            address:     $('#edit_address').val().trim(),
            email:       $('#edit_email').val().trim(),
            role:        $('#edit_role').val(),
            is_active:   $('#edit_is_active').val(),
            job_title:   $('#edit_job_title').val().trim(),
            department:  $('#edit_department').val().trim()
        }),
        function (res) {
            if (res.success) {
                Swal.fire({
                    title: 'Updated!',
                    text:  res.message || 'User updated successfully.',
                    icon:  'success',
                    timer: 1500,
                    showConfirmButton: false
                });
                setTimeout(function () { location.reload(); }, 1500);
                return;
            }

            if (res.errors && Object.keys(res.errors).length) {
                applyBackendErrors(res.errors, 'edit_', '#editAlert');
                return;
            }

            $('#editAlert').html(
                '<div class="alert alert-danger">' +
                (res.message || 'Update failed.') + '</div>'
            );
        },
        'json'
    ).fail(function (xhr) {
        $('#editAlert').html(
            '<div class="alert alert-danger">Server error (' + xhr.status + ').</div>'
        );
    });
}

// ── Delete ────────────────────────────────────────────────────────────────────
function deleteUser(id) {
    Swal.fire({
        title: 'Are you sure?',
        text:  'This user will be soft-deleted.',
        icon:  'warning',
        showCancelButton:     true,
        confirmButtonColor:   '#d33',
        cancelButtonColor:    '#3085d6',
        confirmButtonText:    'Yes, delete it!'
    }).then(function (result) {
        if (result.isConfirmed) {
            $.ajax({
                url:      base_url + 'users/delete/' + id,
                type:     'POST',
                data:     csrfData(),
                dataType: 'json',
                success: function (res) {
                    if (res.success) {
                        Swal.fire({
                            title: 'Deleted!',
                            text:  'User removed successfully.',
                            icon:  'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(function () { location.reload(); }, 1500);
                    } else {
                        Swal.fire('Error!', res.message || 'Delete failed.', 'error');
                    }
                },
                error: function (xhr) {
                    Swal.fire('Error!', 'Server error (' + xhr.status + ')', 'error');
                }
            });
        }
    });
}