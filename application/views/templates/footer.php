<!-- App JS -->
<script src="<?= base_url('assets/js/app.js') ?>"></script>

<!-- Page-specific JS modules (optional) -->
<?php if (isset($page_scripts)): ?>
    <?php foreach ($page_scripts as $script): ?>
        <script src="<?= base_url('assets/js/' . $script) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>