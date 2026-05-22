<nav class="navbar navbar-dark px-4">
    <span class="navbar-brand font-weight-bold">(RMS) RECORD MANAGEMENT SYSTEM</span>

    <div class="ml-auto">
        <span class="text-white mr-3">
            <?php echo htmlspecialchars($firstname . ' ' . $lastname); ?>
        </span>

        <a href="<?php echo base_url('logout'); ?>" class="btn btn-outline-light btn-sm">
            Logout
        </a>
    </div>
</nav>