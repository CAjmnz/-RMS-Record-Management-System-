<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'Login — RMS'; ?></title>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f2f5;
        }
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            border-radius: 8px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
        }
        .login-card .card-header {
            background: #343a40;
            color: #fff;
            text-align: center;
            font-size: 1.25rem;
            font-weight: 600;
            border-radius: 8px 8px 0 0;
            padding: 1.25rem;
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="card login-card">
        <div class="card-header">
            <?php echo isset($site_name) ? htmlspecialchars($site_name) : 'RMS'; ?> &mdash; Login
        </div>
        <div class="card-body p-4">

            <?php if ( ! empty($flash_error)) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($flash_error); ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if ( ! empty($flash_success)) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($flash_success); ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php echo validation_errors('<div class="alert alert-warning">', '</div>'); ?>

            <form action="<?php echo base_url('auth/login/submit'); ?>" method="post">

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control <?php echo form_error('email') ? 'is-invalid' : ''; ?>"
                        placeholder="Enter your email"
                        value="<?php echo set_value('email'); ?>"
                        autocomplete="email"
                        required
                    >
                    <?php if (form_error('email')): ?>
                        <div class="invalid-feedback"><?php echo form_error('email'); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control <?php echo form_error('password') ? 'is-invalid' : ''; ?>"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        required
                    >
                    <?php if (form_error('password')): ?>
                        <div class="invalid-feedback"><?php echo form_error('password'); ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-dark btn-block mt-3">
                    Login
                </button>

            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>