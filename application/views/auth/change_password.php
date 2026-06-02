<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
</head>
<body class="bg-light">

<div class="container mt-5">

    <div class="card mx-auto" style="max-width: 400px;">
        <div class="card-header bg-dark text-white">
            Change Your Password
        </div>

        <div class="card-body">

            <div id="alertBox"></div>

            <div class="form-group">
                <label>New Password</label>
                <input type="password" id="new_password" class="form-control">
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" id="confirm_password" class="form-control">
            </div>

            <button class="btn btn-primary btn-block" onclick="updatePassword()">
                Update Password
            </button>

        </div>
    </div>

</div>

<script src="<?= base_url('assets/js/jquery-3.7.1.min.js') ?>"></script>

<script>
function updatePassword() {

    let newPass = $('#new_password').val();
    let confirmPass = $('#confirm_password').val();

    if (newPass !== confirmPass) {
        $('#alertBox').html('<div class="alert alert-danger">Passwords do not match</div>');
        return;
    }

    $.post("<?= base_url('auth/update_password') ?>", {
        password: newPass
    }, function(res) {

        if (res.success) {
            alert("Password updated successfully");
            window.location.href = "<?= base_url('dashboard') ?>";
        } else {
            $('#alertBox').html('<div class="alert alert-danger">' + res.message + '</div>');
        }

    }, 'json');
}
</script>

</body>
</html>