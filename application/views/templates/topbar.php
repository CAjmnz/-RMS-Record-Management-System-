<?php
$firstname = $this->session->userdata('firstname') ?? '';
$lastname  = $this->session->userdata('lastname') ?? '';
$page_label = isset($page_label) ? $page_label : (isset($title) ? $title : 'RMS');

// Strip " — RMS" suffix if present
$breadcrumb = preg_replace('/\s*[—–-]\s*RMS\s*$/u', '', $page_label);

// Session-based avatar (IMPORTANT: must be stored during login/update)
$profile_picture = $this->session->userdata('profile_picture');

// Safe initials
$initials = strtoupper(substr($firstname, 0, 1) . substr($lastname, 0, 1));
?>

<div id="topbar">

    <!-- Sidebar toggle -->
    <button class="topbar-toggle" id="sidebarToggle" title="Toggle sidebar">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Breadcrumb -->
    <div class="topbar-breadcrumb">
        RMS &rsaquo; <span><?= htmlspecialchars($breadcrumb) ?></span>
    </div>

    <!-- Right side -->
    <div class="topbar-right">

        <span id="topbarClock"></span>

        <div class="topbar-user">
             <!-- Username -->
            <span class="topbar-username ms-3">
                <?= htmlspecialchars(trim($firstname . ' ' . $lastname)) ?>
            </span>
            <!-- Avatar -->
            <div class="topbar-avatar">

                <?php
                // Normalize path safety
                $avatar_path = !empty($profile_picture) ? FCPATH . $profile_picture : null;
                $avatar_url  = !empty($profile_picture) ? base_url($profile_picture) : null;
                ?>

                <?php if (!empty($profile_picture) && file_exists($avatar_path)): ?>

                    <img src="<?= $avatar_url ?>?v=<?= time() ?>"
                         alt="Profile Picture"
                         style="margin: 2px;width:50px;height:50px;border-radius:50%;
                                object-fit:cover;border:3px solid #16c784;">

                <?php else: ?>

                    <div class="profile-avatar">
                        <?= $initials ?: 'U' ?>
                    </div>

                <?php endif; ?>
            
            </div>
          
        </div>

    </div>

</div>