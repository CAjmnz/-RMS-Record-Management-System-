<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="base-url" content="<?= base_url() ?>">
    <title><?= isset($title) ? htmlspecialchars($title) : 'RMS' ?></title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css')?>">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="<?= base_url('assets/css/all.min.css')?>">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/dataTables.bootstrap4.min.css') ?>">
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
<script src="<?= base_url('assets/js/jquery-3.7.1.min.js')?>"></script>
<!-- Bootstrap -->
<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
<!-- DataTables -->
<script src="<?= base_url('assets/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/js/dataTables.bootstrap4.min.js') ?>"></script>
<!-- SweetAlert -->
<script src="<?= base_url('assets/js/sweetalert2.all.min.js') ?>"></script>
<!-- Chart.js -->
<script src="<?= base_url('assets/js/chart.umd.min.js')?>"></script>
<!-- App config -->
<script src="<?= base_url('assets/js/config.js') ?>"></script>