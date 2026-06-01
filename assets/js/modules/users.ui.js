window.UsersUI = (function () {

    function initDataTable() {
        if ($.fn.DataTable.isDataTable('#usersTable')) {
            $('#usersTable').DataTable().destroy();
        }

        $('#usersTable').DataTable({
            pageLength: 5,
            order: [],
            columnDefs: [{ orderable: false, targets: -1 }]
        });
    }

    function resetModals() {

        $('#createUserModal').on('hidden.bs.modal', function () {
            UsersValidation.clearValidation('create');
            $('#createAlert').html('');
            $(this).find('input:not([type=hidden])').val('');
            $(this).find('select').prop('selectedIndex', 0);
        });

        $('#editUserModal').on('hidden.bs.modal', function () {
            UsersValidation.clearValidation('edit');
            $('#editAlert').html('');
        });
    }

    function bindLiveValidation() {

        $(document).on('input change', '.field-wrap input, .field-wrap select', function () {
            const wrap = $(this).closest('.field-wrap');
            $(this).removeClass('is-invalid');
            wrap.removeClass('has-error');
            wrap.find('.error-tooltip').text('');
        });
    }

    function showSuccess(message) {
        Swal.fire({
            title: 'Success',
            text: message,
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });

        setTimeout(() => location.reload(), 1500);
    }

    return {
        initDataTable,
        resetModals,
        bindLiveValidation,
        showSuccess
    };

})();