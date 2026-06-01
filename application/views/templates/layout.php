<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="base-url" content="<?= base_url() ?>">
    <title>RMS</title>

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">

    <?php if (isset($page_css)) echo $page_css; ?>
</head>

<body>

<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<div id="wrapper">
    <?= $content ?>
</div>

<?php $this->load->view('templates/scripts'); ?>

</body>
</html>