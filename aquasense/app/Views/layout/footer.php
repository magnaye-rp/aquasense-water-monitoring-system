    </div> <!-- Close container-fluid -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <?php if (isset($scripts) && !empty($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <?php if (isset($custom_scripts)): ?>
        <script>
            <?= $custom_scripts ?>
        </script>
    <?php endif; ?>
</body>
</html>