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

        .navbar-rms {
            background-color: rgba(9, 151, 9, 0.89) !important;
        }

        .card-custom {
            border-radius: 10px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }

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
        }

        .stat-card {
            border-radius: 10px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 20px 18px;
            color: #fff;
            position: relative;
        }

        .stat-number {
            font-size: 2.4rem;
            font-weight: 700;
        }

        .stat-label {
            font-size: 0.82rem;
            opacity: 0.9;
            text-transform: uppercase;
        }

        .bg-total    { background-color: #2d6a4f; }
        .bg-active   { background-color: #007bff; }
        .bg-inactive { background-color: #6c757d; }
        .bg-admins   { background-color: #dc3545; }

        #dashboardClock {
            font-weight: 700;
            letter-spacing: 2px;
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<?php $this->load->view('templates/navbar'); ?>

<div class="container mt-5">

    <!-- PROFILE CARD -->
    <div class="card card-custom welcome-card mb-4">
        <div class="card-body d-flex align-items-center">

            <div class="avatar mr-3">
                <?php
                    echo strtoupper(
                        substr($firstname, 0, 1) .
                        substr($lastname,  0, 1)
                    );
                ?>
            </div>

            <div>
                <h4 class="mb-1">
                    Welcome back, <?php echo htmlspecialchars($firstname . ' ' . $lastname); ?>
                </h4>

                <p class="text-muted mb-1">
                    <?php echo htmlspecialchars($email); ?>
                </p>

                <span class="badge badge-<?php echo $role === 'admin' ? 'danger' : 'primary'; ?>">
                    <?php echo ucfirst($role); ?>
                </span>
            </div>

        </div>
    </div>

    <!-- CLOCK -->
    <div class="card card-custom p-4 mb-4 text-center">
        <h6 class="text-muted">Current System Time</h6>
        <h1 id="dashboardClock"></h1>
    </div>

    <!-- SYSTEM OVERVIEW -->
    <h5 class="font-weight-bold mb-3">System Overview</h5>

    <div class="row">

        <div class="col-md-3 mb-3">
            <div class="stat-card bg-total">
                <div class="stat-number"><?php echo $stats->total; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="stat-card bg-active">
                <div class="stat-number"><?php echo $stats->active; ?></div>
                <div class="stat-label">Active</div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="stat-card bg-inactive">
                <div class="stat-number"><?php echo $stats->inactive; ?></div>
                <div class="stat-label">Inactive</div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="stat-card bg-admins">
                <div class="stat-number"><?php echo $stats->admins; ?></div>
                <div class="stat-label">Admins</div>
            </div>
        </div>

    </div>

    <!-- RECENT ACTIVITY -->
    <div class="card card-custom mt-4 p-3 mb-5">

        <h5>Recent Activity</h5>
        <hr>

        <?php if ( ! empty($logs)) : ?>
            <ul class="list-group">
                <?php foreach ($logs as $log) : ?>
                    <li class="list-group-item">
                        <small class="text-muted">
                            <?php echo htmlspecialchars($log->created_at); ?>
                        </small><br>
                        <b><?php echo htmlspecialchars($log->action); ?></b> —
                        <?php echo htmlspecialchars($log->description); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p class="text-muted mb-0">No activity yet.</p>
        <?php endif; ?>

    </div>

</div>

<!-- FOOTER -->
<?php $this->load->view('templates/footer'); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function () {

    function updateClock() {
        var now = new Date();
        document.getElementById('dashboardClock').innerText =
            now.toLocaleTimeString();
    }

    updateClock();
    setInterval(updateClock, 1000);

});
</script>

</body>
</html>