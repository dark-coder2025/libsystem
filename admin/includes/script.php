<!-- JQuery JS -->
<script src="assets/js/jquery-3.6.1.min.js"></script>

<!-- Bootstrap JS -->
<script src="assets/js/bootstrap.bundle.min.js"></script>

<!-- JQuery Datatables -->
<script src="assets/js/pdfmake.min.js"></script>
<script src="assets/js/vfs_fonts.js"></script>

<!-- Chart.js -->
<script src="assets/js/chart.min.js"></script>

<!-- Dselect JS -->
<script src="assets/js/dselect.js"></script>

<!-- Alertify JS -->
<script src="assets/js/alertify.min.js"></script>

<!-- Custom JS -->
<script src="assets/js/format_number.js"></script>
<script src="assets/js/disable_future_date.js"></script>
<script src="assets/js/validation.js"></script>
<script src="assets/js/aos.js"></script>
<script src="assets/js/bootstrap-datepicker.min.js"></script>
<script src="assets/js/tooltip.js"></script>
<script src="assets/js/jspdf.umd.min.js"></script>
<script src="assets/js/jspdf.plugin.autotable.min.js"></script>
<script src="assets/js/xlsx.full.min.js"></script>
<script src="assets/js/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.0/dist/sweetalert2.all.min.js"></script>

<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.5.0/js/dataTables.rowReorder.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.5.0/js/rowReorder.dataTables.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.3/js/responsive.dataTables.js"></script>

<?php
if(isset($_SESSION['status']) && $_SESSION['status'] !='')
{
    ?>
    <script>
        Swal.fire({
            title: "<?php echo $_SESSION['status']; ?>",
            icon: "<?php echo $_SESSION['status_code']; ?>",
            confirmButtonText: "OK"
        });
    </script>
    <?php
    unset($_SESSION['status']);
}
?>

<script>
    // Initialize AOS (Animate On Scroll)
    AOS.init();

    $(document).ready(function(){
            $('.toggle-sidebar-btn').click(function(){
                $('body').toggleClass('toggle-sidebar');
            });
        });
</script>
