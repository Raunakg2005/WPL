/**
 * Admin JavaScript file for Movie Booking System
 */

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // File upload preview
    const fileInputs = document.querySelectorAll('.file-upload input[type=file]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const previewId = this.getAttribute('data-preview');
            const preview = document.getElementById(previewId);
            
            if (preview && this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
    
    // Confirm delete
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
    
    // Toggle status
    const statusToggles = document.querySelectorAll('.status-toggle');
    statusToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const form = this.closest('form');
            if (form) {
                form.submit();
            }
        });
    });
    
    // Show/hide password
    const passwordToggles = document.querySelectorAll('.password-toggle');
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const passwordInput = document.getElementById(this.getAttribute('data-target'));
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                passwordInput.type = 'password';
                this.innerHTML = '<i class="fas fa-eye"></i>';
            }
        });
    });
    
    // Date range picker initialization
    const dateRangePickers = document.querySelectorAll('.date-range-picker');
    if (dateRangePickers.length > 0 && typeof daterangepicker !== 'undefined') {
        dateRangePickers.forEach(picker => {
            $(picker).daterangepicker({
                opens: 'left',
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD'
                }
            });
            
            $(picker).on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            });
            
            $(picker).on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        });
    }
    
    // Initialize charts if they exist
    if (typeof Chart !== 'undefined') {
        // Revenue Chart
        const revenueChartCanvas = document.getElementById('revenueChart');
        if (revenueChartCanvas) {
            const revenueChart = new Chart(revenueChartCanvas, {
                type: 'line',
                data: {
                    labels: revenueChartData.labels,
                    datasets: [{
                        label: 'Revenue',
                        data: revenueChartData.data,
                        backgroundColor: 'rgba(229, 9, 20, 0.1)',
                        borderColor: 'rgba(229, 9, 20, 1)',
                        borderWidth: 2,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Bookings Chart
        const bookingsChartCanvas = document.getElementById('bookingsChart');
        if (bookingsChartCanvas) {
            const bookingsChart = new Chart(bookingsChartCanvas, {
                type: 'bar',
                data: {
                    labels: bookingsChartData.labels,
                    datasets: [{
                        label: 'Bookings',
                        data: bookingsChartData.data,
                        backgroundColor: 'rgba(229, 9, 20, 0.7)',
                        borderColor: 'rgba(229, 9, 20, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
        
        // Movies Chart
        const moviesChartCanvas = document.getElementById('moviesChart');
        if (moviesChartCanvas) {
            const moviesChart = new Chart(moviesChartCanvas, {
                type: 'pie',
                data: {
                    labels: moviesChartData.labels,
                    datasets: [{
                        data: moviesChartData.data,
                        backgroundColor: [
                            'rgba(229, 9, 20, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)'
                        ],
                        borderColor: [
                            'rgba(229, 9, 20, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
    }
});