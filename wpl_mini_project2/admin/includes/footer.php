    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/js/admin.js"></script>
    <?php if (isset($page_specific_js)): ?>
    <script src="<?php echo $page_specific_js; ?>"></script>
    <?php endif; ?>
    <script>
        $(document).ready(function() {
            $('.datatable').DataTable();
        });
    </script>
</body>
</html>
