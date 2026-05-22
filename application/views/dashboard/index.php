<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        body { background-color: #f0f2f5; }

        .navbar { background-color: rgba(9, 151, 9, 0.89); }

        .card-custom {
            border-radius: 10px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }

        /* Profile welcome card */
        .welcome-card {
            border-left: 5px solid rgba(9, 151, 9, 0.89);
        }

        .welcome-card .avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background-color: rgba(9, 151, 9, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: rgba(9, 151, 9, 0.89);
            flex-shrink: 0;
        }

        /* Stat cards */
        .stat-card {
            border-radius: 10px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 20px 18px;
            color: #fff;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .stat-card .stat-number {
            font-size: 2.4rem;
            font-weight: 700;
            line-height: 1;
        }

        .stat-card .stat-label {
            font-size: 0.82rem;
            opacity: 0.88;
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .stat-card .stat-bg-icon {
            position: absolute;
            right: 14px;
            top: 12px;
            font-size: 2.8rem;
            opacity: 0.15;
            line-height: 1;
        }

        .bg-total    { background-color: #2d6a4f; }
        .bg-active   { background-color: #007bff; }
        .bg-inactive { background-color: #6c757d; }
        .bg-admins   { background-color: #dc3545; }
        .bg-regular  { background-color: #17a2b8; }

        .flash-msg { transition: all 0.3s ease; }

        .nav-link-active {
            background-color: rgba(255,255,255,0.2) !important;
            border-radius: 4px;
        }

        /* Recent users table — read-only row highlight */
        .table-read-only tbody tr:hover {
            background-color: #f8f9fa;
            cursor: default;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark px-4">
    <span class="navbar-brand font-weight-bold">RMS — Dashboard</span>
    <div class="ml-auto d-flex align-items-center">

        <a href="<?php echo base_url('dashboard'); ?>"
           class="btn btn-outline-light btn-sm mr-2 nav-link-active">
            Dashboard
        </a>

        <a href="<?php echo base_url('users'); ?>"
           class="btn btn-outline-light btn-sm mr-2">
            Users
        </a>

        <a href="<?php echo base_url('logout'); ?>"
           class="btn btn-outline-light btn-sm">
            Logout
        </a>

    </div>
</nav>

<div class="container mt-5">

    <!-- Flash messages -->
    <?php if ($this->session->flashdata('success')) : ?>
        <div class="alert alert-success flash-msg">
            <?php echo $this->session->flashdata('success'); ?>
        </div>
    <?php endif; ?>

    <!-- ── PROFILE WELCOME CARD (everyone) ───────────────── -->
    <div class="card card-custom welcome-card mb-4">
        <div class="card-body py-4">
            <div class="d-flex align-items-center">

                <!-- Avatar initials -->
                <div class="avatar mr-3">
                    <?php
                        echo strtoupper(
                            substr($firstname, 0, 1) .
                            substr($lastname,  0, 1)
                        );
                    ?>
                </div>

                <div>
                    <h4 class="font-weight-bold mb-1">
                        Welcome back,
                        <?php echo htmlspecialchars($firstname . ' ' . $lastname); ?>!
                    </h4>
                    <p class="text-muted mb-1">
                        <?php echo htmlspecialchars($email); ?>
                    </p>
                    <span class="badge badge-<?php echo $role === 'admin' ? 'danger' : 'primary'; ?> px-2 py-1">
                        <?php echo ucfirst($role); ?>
                    </span>
                </div>

            </div>
        </div>
    </div>

    <!-- ── STAT CARDS (everyone sees these) ──────────────── -->
    <h5 class="font-weight-bold mb-3">System Overview</h5>

    <div class="row mb-4">

        <div class="col-6 col-lg mb-3">
            <div class="stat-card bg-total">
                <span class="stat-bg-icon">&#9783;</span>
                <div class="stat-number"><?php echo $stats->total; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
        </div>

        <div class="col-6 col-lg mb-3">
            <div class="stat-card bg-active">
                <span class="stat-bg-icon">&#10003;</span>
                <div class="stat-number"><?php echo $stats->active; ?></div>
                <div class="stat-label">Active</div>
            </div>
        </div>

        <div class="col-6 col-lg mb-3">
            <div class="stat-card bg-inactive">
                <span class="stat-bg-icon">&#8722;</span>
                <div class="stat-number"><?php echo $stats->inactive; ?></div>
                <div class="stat-label">Inactive</div>
            </div>
        </div>

        <div class="col-6 col-lg mb-3">
            <div class="stat-card bg-admins">
                <span class="stat-bg-icon">&#9733;</span>
                <div class="stat-number"><?php echo $stats->admins; ?></div>
                <div class="stat-label">Admins</div>
            </div>
        </div>

        <div class="col-6 col-lg mb-3">
            <div class="stat-card bg-regular">
                <span class="stat-bg-icon">&#9786;</span>
                <div class="stat-number"><?php echo $stats->regular; ?></div>
                <div class="stat-label">Regular Users</div>
            </div>
        </div>

    </div>

    <!-- ── RECENTLY ADDED USERS TABLE (everyone — read only) -->
    <div class="card card-custom mb-5">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 font-weight-bold">Recently Added Users</h6>

            <?php if ($role === 'admin') : ?>
                <!-- Admin gets the View All link -->
                <a href="<?php echo base_url('users'); ?>"
                   class="btn btn-outline-success btn-sm">
                    View All Users
                </a>
            <?php else : ?>
                <!-- Regular user sees a read-only label -->
                <span class="badge badge-secondary px-3 py-2">
                    Read-only
                </span>
            <?php endif; ?>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-read-only mb-0">
                    <thead class="thead-dark">
                    <tr>
                        <th class="pl-3">Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Date Added</th>
                        <?php if ($role === 'admin') : ?>
                            <th width="160">Actions</th>
                        <?php endif; ?>
                    </tr>
                    </thead>

                    <tbody>
                    <?php if (!empty($recent_users)) : ?>
                        <?php foreach ($recent_users as $u) : ?>
                            <tr>
                                <td class="pl-3">
                                    <?php echo htmlspecialchars($u->firstname . ' ' . $u->lastname); ?>
                                </td>

                                <td><?php echo htmlspecialchars($u->email); ?></td>

                                <td>
                                    <span class="badge badge-<?php echo $u->role === 'admin' ? 'danger' : 'primary'; ?>">
                                        <?php echo ucfirst($u->role); ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="badge badge-<?php echo $u->is_active ? 'success' : 'secondary'; ?>">
                                        <?php echo $u->is_active ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>

                                <td>
                                    <?php echo date('M d, Y', strtotime($u->created_at)); ?>
                                </td>

                                <!-- Edit / Delete: admin only -->
                                <?php if ($role === 'admin') : ?>
                                    <td>
                                        <button class="btn btn-primary btn-sm">Edit</button>
                                        <button class="btn btn-danger btn-sm">Delete</button>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="<?php echo $role === 'admin' ? 6 : 5; ?>"
                                class="text-center text-muted py-3">
                                No users found.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>

    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {
    setTimeout(function () {
        $('.flash-msg').fadeOut(500);
    }, 3000);
});
</script>

</body>
</html>