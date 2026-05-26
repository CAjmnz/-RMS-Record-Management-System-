<?php
$firstname = isset($firstname) ? $firstname : $this->session->userdata('firstname');
$lastname  = isset($lastname)  ? $lastname  : $this->session->userdata('lastname');
$page_label = isset($page_label) ? $page_label : (isset($title) ? $title : 'RMS');
// Strip " — RMS" suffix from title for breadcrumb if present
$breadcrumb = preg_replace('/\s*[—–-]\s*RMS\s*$/u', '', $page_label);
?>
<div id="topbar">
 
    <button class="topbar-toggle" id="sidebarToggle" title="Toggle sidebar">
        <i class="fas fa-bars"></i>
    </button>
 
    <div class="topbar-breadcrumb">
        RMS &rsaquo; <span><?php echo htmlspecialchars($breadcrumb); ?></span>
    </div>
 
    <div class="topbar-right">
        <span id="topbarClock"></span>
        <div class="topbar-user">
            <div class="topbar-avatar">
                <?php
                $f = $firstname ? substr($firstname, 0, 1) : '?';
                $l = $lastname  ? substr($lastname,  0, 1) : '';
                echo strtoupper($f . $l);
                ?>
            </div>
            <span><?php echo htmlspecialchars($firstname); ?></span>
        </div>
    </div>
 
</div>
 