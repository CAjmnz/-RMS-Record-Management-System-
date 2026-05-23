<!DOCTYPE html>
<html>
<head>
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        body { background: #f0f2f5; }
        .profile-card {
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(9,151,9,0.15);
            color: rgba(9,151,9,0.89);
            display:flex;
            align-items:center;
            justify-content:center;
            font-size: 28px;
            font-weight: bold;
        }
    </style>
</head>

<body>

<?php $this->load->view('templates/navbar'); ?>

<div class="container mt-5">

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success">
            <?php echo $this->session->flashdata('success'); ?>
        </div>
    <?php endif; ?>

    <div class="row">

        <!-- LEFT CARD -->
        <div class="col-md-4">
            <div class="card profile-card p-3 text- left">

                <div class="avatar mx-auto mb-3">
                    <?php echo strtoupper(substr($user->firstname,0,1).substr($user->lastname,0,1)); ?>
                </div>
                <span class="badge badge-<?php echo $user->role == 'admin' ? 'danger' : 'primary'; ?>">
                    <?php echo ucfirst($user->role); ?>
                </span>
                <h5><?php echo $user->firstname . ' ' . $user->lastname; ?></h5>
                <p><b></b> <?php echo $user->employee_id ?? 'N/A'; ?></p>
                <p class="text-muted"><?php echo $user->email; ?></p>
                <P><b></b><?php echo $user->age ?? 'N/A'; ?></p>
                <hr>
                <p><b>Department:</b> <?php echo $user->department ?? 'N/A'; ?></p>
                <p><b>Job Title:</b> <?php echo $user->job_title ?? 'N/A'; ?></p>

                <a href="<?php echo base_url('profile/edit'); ?>" class="btn btn-primary btn-block">
                    Edit Profile
                </a>

            </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="col-md-8">

            <div class="card profile-card p-4">

                <h4>Employee Information</h4>
                <hr>

                <table class="table table-bordered">
                <tr>
    <th>Employee ID</th>
    <td><?php echo $user->employee_id ?? 'N/A'; ?></td>
</tr>

                    <tr>
                        <th>Firstname</th>
                        <td><?php echo $user->firstname; ?></td>
                    </tr>

                    <tr>
                        <th>Lastname</th>
                        <td><?php echo $user->lastname; ?></td>
                    </tr>

                    <tr>
                        <th>Nickname</th>
                        <td><?php echo $user->nickname; ?></td>
                    </tr>

                    <tr>
                        <th>Birthday</th>
                        <td><?php echo $user->birthday; ?></td>
                    </tr>

                    <tr>
                        <th>Address</th>
                        <td><?php echo $user->address; ?></td>
                    </tr>

                    <tr>
                        <th>Contact</th>
                        <td><?php echo $user->contactno; ?></td>
                    </tr>

                    <tr>
                        <th>Email</th>
                        <td><?php echo $user->email; ?></td>
                    </tr>

                    <tr>
                        <th>Department</th>
                        <td><?php echo $user->department ?? 'N/A'; ?></td>
                    </tr>

                    <tr>
                        <th>Job Title</th>
                        <td><?php echo $user->job_title ?? 'N/A'; ?></td>
                    </tr>

                </table>

            </div>

        </div>

    </div>

</div>

</body>
<?php $this->load->view('templates/footer'); ?>
</html>