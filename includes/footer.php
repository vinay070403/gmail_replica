<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle sidebar on mobile and desktop
    document.addEventListener('DOMContentLoaded', function() {
        const menuBtn = document.getElementById('menuBtn');
        const sidebar = document.getElementById('sidebar');

        menuBtn.addEventListener('click', function() {
            // Toggle collapsed state for desktop
            if (window.innerWidth > 768) {
                sidebar.classList.toggle('collapsed');
            } else {
                // Toggle sidebar open/close for mobile
                sidebar.classList.toggle('sidebar-open');
            }
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !menuBtn.contains(event.target)) {
                    sidebar.classList.remove('sidebar-open');
                }
            }
        });
    });

    function viewEmail(mailId) {
        window.location.href = 'view_mail.php?id=' + mailId;
    }
</script>
<!-- Bootstrap JS -->
<script src="../assets/bootstrap.bundle.min.js"></script>

<!-- ✅ Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<!-- ✅ Select2 Initialization -->
<script>
    $(document).ready(function() {
        $('#recipients').select2({
            placeholder: "Select recipients",
            allowClear: true
        });
    });
</script>

<!-- bcc and cc-->
<script>
    function toggleField(id) {
        var field = document.getElementById(id);
        if (field.style.display === "none" || field.style.display === "") {
            field.style.display = "block";
        } else {
            field.style.display = "none";
        }
    }
</script>

</body>

</html>