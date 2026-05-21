<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'Dashboard — RMS'; ?></title>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f0f2f5; }
        .navbar { background-color: #343a40; }
        .welcome-card {
            border-radius: 8px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark px-4">
    <span class="navbar-brand font-weight-bold">RMS</span>
    <div class="ml-auto">
        <span class="text-white mr-3">
            <?php echo htmlspecialchars($firstname . ' ' . $lastname); ?>
        </span>
        <a href="<?php echo base_url('logout'); ?>" class="btn btn-outline-light btn-sm">
            Logout
        </a>
    </div>
</nav>

<div class="container mt-5">
    <div class="card welcome-card p-4">
        <h4 class="mb-1">Welcome, <?php echo htmlspecialchars($firstname); ?>!</h4>

    </div>

    <?php if ( ! empty($flash_success)) : ?>
        <div class="alert alert-success mt-3">
            <?php echo htmlspecialchars($flash_success); ?>
        </div>
    <?php endif; ?>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-center p-4 welcome-card">
                <h6 class="text-muted">Module</h6>
                <p class="mb-0">Records</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center p-4 welcome-card">
                <h6 class="text-muted">Module</h6>
                <p class="mb-0">Users</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center p-4 welcome-card">
                <h6 class="text-muted">Module</h6>
                <p class="mb-0">Reports</p>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>