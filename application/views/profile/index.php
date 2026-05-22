<!DOCTYPE html>
<html>
<head>
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">

    <h3>My Profile</h3>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success">
            <?php echo $this->session->flashdata('success'); ?>
        </div>
    <?php endif; ?>

    <div class="card p-3">

        <p><b>Name:</b> <?php echo $user->firstname . ' ' . $user->lastname; ?></p>
        <p><b>Email:</b> <?php echo $user->email; ?></p>
        <p><b>Nickname:</b> <?php echo $user->nickname; ?></p>
        <p><b>Address:</b> <?php echo $user->address; ?></p>
        <p><b>Contact:</b> <?php echo $user->contactno; ?></p>

        <a href="<?php echo base_url('profile/edit'); ?>" class="btn btn-primary">
            Edit Profile
        </a>

    </div>

</div>

</body>
</html>