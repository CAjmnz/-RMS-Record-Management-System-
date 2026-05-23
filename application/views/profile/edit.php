<!DOCTYPE html>
<html>
<head>
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>

<body>

<?php $this->load->view('templates/navbar'); ?>

<div class="container mt-5">

    <h3>Edit Profile</h3>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger">
            <?php echo $this->session->flashdata('error'); ?>
        </div>
    <?php endif; ?>

    <div class="card p-4">

        <form method="POST" action="<?php echo base_url('profile/update'); ?>">

            <div class="form-group">
                <label>Nickname</label>
                <input type="text" name="nickname" class="form-control"
                       value="<?php echo $user->nickname; ?>">
            </div>
            <div class="form-group">
    <label>Employee ID</label>
    <input type="text"
           class="form-control"
           value="<?php echo $user->employee_id; ?>"
           disabled>
</div>
            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" class="form-control"
                       value="<?php echo $user->address; ?>" required>
            </div>

            <div class="form-group">
                <label>Contact</label>
                <input type="text" name="contactno" class="form-control"
                       value="<?php echo $user->contactno; ?>" required>
            </div>

            <div class="form-group">
                <label>New Password (optional)</label>
                <input type="password" name="password" class="form-control">
            </div>

            <button type="submit" class="btn btn-success">
                Save Changes
            </button>

        </form>

    </div>

</div>

</body>
</html>