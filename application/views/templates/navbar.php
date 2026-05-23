<style>
.navbar-rms {
    background-color: rgba(9, 151, 9, 0.89);
}
</style>

<nav class="navbar navbar-dark navbar-rms px-4">

    <span class="navbar-brand font-weight-bold">
        RMS — Record Management System
    </span>

    <div class="ml-auto d-flex align-items-center">

        <!-- LIVE CLOCK -->
        <span class="text-white mr-3" id="navClock" style="font-weight:600;"></span>

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
            Profile
        </a>

        <a href="<?php echo base_url('logout'); ?>"
           class="btn btn-outline-light btn-sm">
            Logout
        </a>

    </div>

</nav>

<script>
function updateClock() {
    const now = new Date();
    document.getElementById('navClock').innerText =
        now.toLocaleTimeString();
}

updateClock();
setInterval(updateClock, 1000);
</script>