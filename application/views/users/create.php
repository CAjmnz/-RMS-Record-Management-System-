<!DOCTYPE html>
<html>
<head>
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
<?php if (validation_errors()) : ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo validation_errors(); ?>
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
<?php endif; ?>

    <h3>Create User</h3>

    <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>

    <form method="post" action="<?php echo base_url('users/store'); ?>">

        <input type="text" name="firstname" class="form-control mb-2" placeholder="Firstname">

        <input type="text" name="lastname" class="form-control mb-2" placeholder="Lastname">
        
        <input type="text" name="employee_id" class="form-control mb-2" placeholder="Employee_id">

        <input type="date" name="birthday" class="form-control mb-2">

        <input type="text" name="address" class="form-control mb-2" placeholder="Address">

        <input type="text" name="contactno" class="form-control mb-2" placeholder="Contact No">

        <input type="email" name="email" class="form-control mb-2" placeholder="Email">

        <input type="password" name="password" class="form-control mb-2" placeholder="Password">

        <select name="role" class="form-control mb-3">
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>

        <button type="submit" class="btn btn-primary">Create User</button>

        <a href="<?php echo base_url('users'); ?>" class="btn btn-secondary">Back</a>

    </form>

</div>

</body>
</html>