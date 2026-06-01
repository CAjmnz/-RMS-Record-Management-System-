<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="base-url" content="<?= base_url() ?>">
    <title><?= isset($title) ? htmlspecialchars($title) : 'RMS' ?></title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <!-- App CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <!-- Page-specific CSS (optional) -->
    <?php if (isset($page_styles)): ?>
        <?php foreach ($page_styles as $style): ?>
            <link rel="stylesheet" href="<?= base_url('assets/css/' . $style) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>

<!-- base_url for JS -->
<script>
    const base_url = "<?= base_url() ?>";
</script>

<!-- jQuery — ONCE, CDN only -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<!-- App config -->
<script src="<?= base_url('assets/js/config.js') ?>"></script>