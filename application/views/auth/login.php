<?php $this->load->view('templates/head'); ?>
  
<!-- login.css -->
<link rel="stylesheet" href="<?= base_url('assets/css/login.css') ?>">

<div class="login-container">
    <!-- LEFT -->
    <div class="login-left">
        <div class="left-content">
            <div class="logo-circle">
                R
            </div>
            <div class="system-title">
                Record<br>
                Management<br>
                System
            </div>
            <div class="system-subtitle">
                Securely manage records, users, and organizational data
                using the RMS administrative platform.
            </div>
        </div>
    </div>
    <!-- RIGHT -->
    <div class="login-right">
        <div class="login-card">
            <div class="login-header">
                <h2>Welcome Back</h2>
                <p>Login to continue to RMS dashboard</p>
            </div>
            <!-- ALERTS -->
            <?php if (!empty($flash_error)) : ?>
                <div class="alert alert-danger auto-dismiss">
                    <?php echo htmlspecialchars($flash_error); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($flash_success)) : ?>
                <div class="alert alert-success auto-dismiss">
                    <?php echo htmlspecialchars($flash_success); ?>
                </div>
            <?php endif; ?>
            <?php echo validation_errors(
                '<div class="alert alert-warning auto-dismiss">',
                '</div>'
            ); ?>
            <!-- LOGIN FORM -->
            <form action="<?php echo base_url('auth/login/submit'); ?>"
                  method="post"
                  id="loginForm">
                <div class="form-group">
                    <label class="form-label">
                        Email Address
                    </label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control"
                        autocomplete="off" 
                        placeholder="Enter your email"
                        value="<?php echo set_value('email'); ?>"
                        required
                    >
                </div>

                <div class="form-group">

                    <label class="form-label">
                        Password
                    </label>

                    <div class="password-wrapper">

                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control"
                            autocomplete="off" 
                            placeholder="Enter your password"
                            required
                        >

                        <span class="toggle-password"
                              onclick="togglePassword()">
                            <i class="fas fa-eye"></i>
                        </span>

                    </div>
                </div>

                <button type="submit"
                        class="login-btn"
                        id="loginBtn">

                    <span id="btnText">
                        Login
                    </span>

                </button>

            </form>

            <div class="footer-text">
                RMS © <?php echo date('Y'); ?>
            </div>

        </div>

    </div>

</div>



<script src="<?= base_url('assets/js/modules/login.main.js') ?>"></script>

</body>
</html>