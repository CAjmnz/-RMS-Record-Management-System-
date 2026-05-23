<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title><?php echo htmlspecialchars($title); ?></title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>

        body {
            background-color: #f0f2f5;
        }

        .navbar {
            background-color: rgba(9,151,9,0.89);
        }

        .card-custom {
            border-radius: 10px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }

    </style>
</head>
<body>

<nav class="navbar navbar-dark px-4">

    <span class="navbar-brand font-weight-bold">
        RMS — View Profile
    </span>

    <div class="ml-auto">

        <a href="<?php echo base_url('dashboard'); ?>"
           class="btn btn-outline-light btn-sm mr-2">
            Dashboard
        </a>

        <a href="<?php echo base_url('users'); ?>"
           class="btn btn-outline-light btn-sm mr-2">
            Users
        </a>

        <a href="<?php echo base_url('profile'); ?>"
           class="btn btn-outline-light btn-sm mr-2">
            My Profile
        </a>

        <a href="<?php echo base_url('logout'); ?>"
           class="btn btn-outline-light btn-sm">
            Logout
        </a>

    </div>

</nav>

<div class="container mt-5">

    <div class="card card-custom">

        <div class="card-body">

            <h3 class="font-weight-bold mb-4">
                <?php echo htmlspecialchars($user->firstname . ' ' . $user->lastname); ?>
            </h3>

            <div class="row">

                <div class="col-md-6 mb-3">
                    <strong>Employee ID</strong>
                    <p>
                        <?php echo htmlspecialchars($user->employee_id ?: 'N/A'); ?>
                    </p>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Nickname</strong>
                    <p>
                        <?php echo htmlspecialchars($user->nickname ?: 'N/A'); ?>
                    </p>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Email</strong>
                    <p>
                        <?php echo htmlspecialchars($user->email); ?>
                    </p>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Contact Number</strong>
                    <p>
                        <?php echo htmlspecialchars($user->contactno); ?>
                    </p>
                </div>

                <div class="col-md-12 mb-3">
                    <strong>Address</strong>
                    <p>
                        <?php echo htmlspecialchars($user->address); ?>
                    </p>
                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>