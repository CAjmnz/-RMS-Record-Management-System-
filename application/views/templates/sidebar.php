<?php
// Determine active page for sidebar highlighting
$current = $this->router->fetch_class();
?>
<div id="sidebar">

    <div class="sidebar-brand">
        <div class="brand-icon">R</div>
        <span class="brand-text">RMS</span>
    </div>

    <nav class="sidebar-nav">

        <div class="nav-section-label">Main</div>

        <a href="<?php echo base_url('dashboard'); ?>"
           class="sidebar-link <?php echo ($current === 'dashboard') ? 'active' : ''; ?>"
           data-title="Dashboard">
            <i class="fas fa-tachometer-alt nav-icon"></i>
            <span class="nav-label">Dashboard</span>
        </a>

        <a href="<?php echo base_url('users'); ?>"
           class="sidebar-link <?php echo ($current === 'users') ? 'active' : ''; ?>"
           data-title="Users">
            <i class="fas fa-users nav-icon"></i>
            <span class="nav-label">Users</span>
        </a>

        <div class="nav-section-label">Account</div>

        <a href="<?php echo base_url('profile'); ?>"
           class="sidebar-link <?php echo ($current === 'profile') ? 'active' : ''; ?>"
           data-title="My Profile">
            <i class="fas fa-user-circle nav-icon"></i>
            <span class="nav-label">My Profile</span>
        </a>

    </nav>

    <div class="sidebar-footer">
        <a href="<?php echo base_url('logout'); ?>"
           class="sidebar-link"
           data-title="Logout">
            <i class="fas fa-sign-out-alt nav-icon"></i>
            <span class="nav-label">Logout</span>
        </a>
    </div>

</div>