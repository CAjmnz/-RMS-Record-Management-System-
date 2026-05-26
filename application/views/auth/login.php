<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo isset($title)
            ? htmlspecialchars($title)
            : 'Login — RMS'; ?>
    </title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family:'Segoe UI',sans-serif;
            background:#f4f6f9;
            min-height:100vh;
            overflow:hidden;
        }

        .login-container{
            width:100%;
            min-height:100vh;
            display:flex;
        }

        /* LEFT SIDE */
        .login-left{
            width:50%;
            background:linear-gradient(135deg,#1a1a2e,#16213e);
            color:#fff;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:60px;
            position:relative;
        }

        .login-left::before{
            content:'';
            position:absolute;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background:rgba(255,255,255,0.02);
            backdrop-filter:blur(2px);
        }

        .left-content{
            position:relative;
            z-index:2;
            max-width:420px;
        }

        .logo-circle{
            width:80px;
            height:80px;
            border-radius:50%;
            background:#16c784;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:36px;
            font-weight:700;
            margin-bottom:25px;
            box-shadow:0 10px 30px rgba(0,0,0,0.25);
        }

        .system-title{
            font-size:38px;
            font-weight:700;
            line-height:1.2;
            margin-bottom:18px;
        }

        .system-subtitle{
            color:rgba(255,255,255,0.75);
            font-size:15px;
            line-height:1.8;
        }

        /* RIGHT SIDE */
        .login-right{
            width:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:40px;
            background:#f8f9fc;
        }

        .login-card{
            width:100%;
            max-width:430px;
            background:#fff;
            border-radius:14px;
            padding:38px;
            box-shadow:0 8px 30px rgba(0,0,0,0.08);
            border:1px solid #e3e6f0;
        }

        .login-header{
            margin-bottom:28px;
        }

        .login-header h2{
            font-size:28px;
            font-weight:700;
            color:#1a1a2e;
            margin-bottom:8px;
        }

        .login-header p{
            font-size:13px;
            color:#858796;
            margin:0;
        }

        .form-group{
            margin-bottom:18px;
        }

        .form-label{
            font-size:12px;
            font-weight:600;
            color:#5a5c69;
            margin-bottom:7px;
        }

        .form-control{
            height:45px;
            border-radius:8px;
            border:1px solid #d1d3e2;
            font-size:14px;
            padding:10px 14px;
            transition:all .2s ease;
        }

        .form-control:focus{
            border-color:#16c784;
            box-shadow:0 0 0 0.15rem rgba(22,199,132,0.15);
        }

        .password-wrapper{
            position:relative;
        }

        .toggle-password{
            position:absolute;
            right:14px;
            top:50%;
            transform:translateY(-50%);
            cursor:pointer;
            color:#858796;
            font-size:14px;
        }

        .login-btn{
            width:100%;
            height:45px;
            border:none;
            border-radius:8px;
            background:#16c784;
            color:#fff;
            font-size:14px;
            font-weight:600;
            transition:all .2s ease;
        }

        .login-btn:hover{
            background:#13b374;
        }

        .login-btn:disabled{
            opacity:.7;
            cursor:not-allowed;
        }

        .alert{
            border-radius:8px;
            font-size:13px;
            border:none;
        }

        .footer-text{
            margin-top:22px;
            text-align:center;
            font-size:12px;
            color:#b7b9cc;
        }

        @media(max-width:991px){

            .login-left{
                display:none;
            }

            .login-right{
                width:100%;
                padding:25px;
            }

            .login-card{
                padding:28px;
            }
        }

    </style>
</head>

<body>

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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>

    // AUTO DISMISS ALERT
    $(document).ready(function () {

setTimeout(function () {

    $('.auto-dismiss').fadeOut('slow');

}, 4000);

});

    // SHOW/HIDE PASSWORD
    function togglePassword(){

        let input = document.getElementById('password');
        let icon  = document.querySelector('.toggle-password i');

        if(input.type === 'password'){
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }else{
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }


    // LOGIN BUTTON LOADING
    document.getElementById('loginForm')
    .addEventListener('submit', function(){

        let btn = document.getElementById('loginBtn');

        btn.disabled = true;

        btn.innerHTML =
            '<i class="fas fa-spinner fa-spin mr-2"></i> Logging in...';

    });

</script>

</body>
</html>