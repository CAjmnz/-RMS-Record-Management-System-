// FILE: assets/js/modules/users.validation.js

window.UsersValidation = (function () {

    function clearValidation(formType) {

        const modal = formType === 'edit'
            ? '#editUserModal'
            : '#createUserModal';

        $(modal).find('.field-wrap').each(function () {
            $(this).removeClass('has-error');
            $(this).find('input, select').removeClass('is-invalid');
            $(this).find('.error-tooltip').text('');
        });
    }

    function showFieldError(selector, message) {
        const input = $(selector);
        const wrap = input.closest('.field-wrap');

        input.addClass('is-invalid');
        wrap.addClass('has-error');
        wrap.find('.error-tooltip').text(message);
    }

    function applyBackendErrors(errors, prefix, alertSelector) {

        Object.keys(errors).forEach(function (field) {
            showFieldError('#' + prefix + field, errors[field]);
        });

        const firstError = $('.field-wrap.has-error').first();

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

        const p = '#' + prefix;
        const emailRx = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        let hasErr = false;

        const checks = [
            { id: 'firstname', msg: 'First name is required.' },
            { id: 'lastname', msg: 'Last name is required.' },
            { id: 'employee_id', msg: 'Employee ID is required.' },
            { id: 'birthday', msg: 'Birthday is required.' },
            { id: 'contactno', msg: 'Contact number is required.' },
            { id: 'address', msg: 'Address is required.' },
            { id: 'email', msg: 'Email is required.' },
            { id: 'job_title', msg: 'Job title is required.' },
            { id: 'department', msg: 'Department is required.' }
        ];

        checks.forEach(function (c) {
            if (!$(p + c.id).val().trim()) {
                showFieldError(p + c.id, c.msg);
                hasErr = true;
            }
        });

        const contactno = $(p + 'contactno').val().trim();
        if (contactno && !/^[0-9]{11}$/.test(contactno)) {
            showFieldError(p + 'contactno', 'Must be exactly 11 numeric digits.');
            hasErr = true;
        }

        const email = $(p + 'email').val().trim();
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

    return {
        clearValidation,
        showFieldError,
        applyBackendErrors,
        validateFields
    };
    
    
})();