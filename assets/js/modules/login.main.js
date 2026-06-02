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
    