    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Initialize DataTables
        $(document).ready(function() {
            $('.datatable').DataTable({
                responsive: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search..."
                }
            });
            
            // Password toggle
            $('.password-toggle').click(function() {
                const target = $(this).data('target');
                const input = $('#' + target);
                const icon = $(this).find('i');
                
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });
            
            // Confirm delete
            $('.btn-delete').click(function(e) {
                if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
            
            // Image preview
            $('input[type="file"][data-preview]').change(function() {
                const file = this.files[0];
                const previewId = $(this).data('preview');
                const preview = $('#' + previewId);
                
                if (file) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        preview.attr('src', e.target.result);
                        preview.show();
                    }
                    
                    reader.readAsDataURL(file);
                } else {
                    preview.hide();
                }
            });
        });
    </script>
</body>
</html>