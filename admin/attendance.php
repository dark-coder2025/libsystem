<?php 
include('authentication.php');
include('includes/header.php'); 
include('./includes/sidebar.php'); 

// // Bulk delete handler with SweetAlert
// if (isset($_POST['bulk_delete']) && isset($_POST['delete_ids'])) {
//     $ids = $_POST['delete_ids'];
//     $ids_str = implode(",", array_map('intval', $ids)); // sanitize IDs
//     $delete_query = "DELETE FROM user_log WHERE user_log_id IN ($ids_str)";
    
//     if (mysqli_query($con, $delete_query)) {
//         echo "
//         <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
//         <script>
//         document.addEventListener('DOMContentLoaded', function() {
//             Swal.fire({
//                 icon: 'success',
//                 title: 'Deleted!',
//                 text: 'Selected logs have been deleted.',
//                 confirmButtonColor: '#3085d6',
//                 confirmButtonText: 'OK'
//             }).then(() => {
//                 window.location.href = window.location.href;
//             });
//         });
//         </script>";
//     } else {
//         $error = mysqli_error($con);
//         echo "
//         <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
//         <script>
//         document.addEventListener('DOMContentLoaded', function() {
//             Swal.fire({
//                 icon: 'error',
//                 title: 'Error!',
//                 text: 'Could not delete logs: $error',
//                 confirmButtonColor: '#d33',
//                 confirmButtonText: 'Close'
//             });
//         });
//         </script>";
//     }
// }
?>

<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.dataTables.css">

<main id="main" class="main">
     <div class="pagetitle" data-aos="fade-down">
          <h1>Attendance</h1>
          <nav>
               <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href=".">Home</a></li>
                    <li class="breadcrumb-item active">Attendance</li>
               </ol>
          </nav>
     </div>

     <section class="section dashboard">
          <div class="row">
               <div class="col-lg-12">
                    <div class="row">
                         <div data-aos="fade-down" class="col-12">
                              <div class="card recent-sales overflow-auto border-3 border-top border-info">
                                   <div class="card-body">
                                        <div class="row d-flex justify-content-end align-items-center mt-2">
                                             <form action="" method="POST" class="col-12 col-md-5 d-flex">
                                                  <?php date_default_timezone_set('Asia/Manila'); ?>
                                                  <div class="form-group form-group-sm">
                                                       <label><small>From Date</small></label>
                                                       <input type="date" name="from_date" class="form-control form-control-sm">
                                                  </div>
                                                  <div class="form-group form-group-sm mx-2">
                                                       <label><small>To Date</small></label>
                                                       <input type="date" name="to_date" class="form-control form-control-sm">
                                                  </div>
                                                  <div class="form-group form-group-sm">
                                                       <label><small>Click to Filter</small></label>
                                                       <button type="submit" name="filter_attendance" class="btn text-white fw-semibold btn-info btn-sm d-block">Filter</button>
                                                  </div>
                                             </form>
                                        </div>

                                        <form method="POST" id="bulkDeleteForm">
                                        <div class="container mt-3">
                                             <div class="row">
                                                  <div class="col-12">
                                                       <div class="data_table">
                                                            <table id="example3" class="display" style="width:100%">
                                                                 <thead>
                                                                      <tr>
                                                                           <!-- <th><input type="checkbox" id="select-all"></th> -->
                                                                           <th>Date</th>
                                                                           <th>Time In</th>
                                                                           <th>Full Name</th>
                                                                           <th>Program</th>
                                                                           <th>Time Out</th>
                                                                      </tr>
                                                                 </thead>
                                                                 <tbody>
                                                                      <?php
                                                                      if (isset($_POST['from_date']) && isset($_POST['to_date'])) {
                                                                           $from_date = $_POST['from_date'];
                                                                           $to_date = $_POST['to_date'];
                                                                           $query = "SELECT * FROM user_log WHERE date_log BETWEEN '$from_date' AND '$to_date' ORDER BY date_log DESC, time_log DESC";
                                                                           $query_run = mysqli_query($con, $query);
                                                                           if (mysqli_num_rows($query_run) > 0) {
                                                                                foreach ($query_run as $row) {
                                                                      ?>
                                                                                     <tr>
                                                                                          <!-- <td><input type="checkbox" name="delete_ids[]" value="<?= $row['user_log_id']; ?>"></td> -->
                                                                                          <td><?= date("M d, Y", strtotime($row['date_log'])); ?></td>
                                                                                          <td><?= date("h:i a", strtotime($row['time_log'])); ?></td>
                                                                                          <td><?= $row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname']; ?></td>
                                                                                          <td><?= $row['year_level'] . ' - ' . $row['course']; ?></td>
                                                                                          <td><?= date("h:i a", strtotime($row['time_out'])); ?></td>
                                                                                     </tr>
                                                                      <?php
                                                                                }
                                                                           }
                                                                      } else {
                                                                           $result = mysqli_query($con, "SELECT * FROM user_log WHERE course = ''");
                                                                           while ($row = mysqli_fetch_array($result)) {
                                                                      ?>
                                                                                     <tr>
                                                                                          <!-- <td><input type="checkbox" name="delete_ids[]" value="<?= $row['user_log_id']; ?>"></td> -->
                                                                                          <td><?= date("M d, Y", strtotime($row['date_log'])); ?></td>
                                                                                          <td><?= date("h:i a", strtotime($row['time_log'])); ?></td>
                                                                                          <td><?= $row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname']; ?></td>
                                                                                          <td><?= $row['year_level'] . ' - ' . $row['course']; ?></td>
                                                                                          <td><?= date("h:i a", strtotime($row['time_out'])); ?></td>
                                                                                     </tr>
                                                                      <?php
                                                                           }
                                                                      }
                                                                      ?>
                                                                 </tbody>
                                                            </table>
                                                       </div>
                                                       <!-- <button type="submit" name="bulk_delete" class="btn btn-danger mt-3">Delete Selected</button> -->
                                                  </div>
                                             </div>
                                        </div>
                                        </form>
                                   </div>
                              </div>
                         </div>
                    </div>
               </div>
          </div>
     </section>
</main>

<!-- Modal for month selection -->
<div id="monthModal" style="display:none; position:fixed; top:30%; left:40%; background:#fff; padding:20px; border:1px solid #ccc; z-index:1000;">
    <h3>Select a Month</h3>
    <input type="month" id="selectedMonth" />
    <br><br>
    <button class="btn btn-primary" onclick="filterByMonth()">Filter</button>
    <button class="btn btn-danger" onclick="closeModal()">Cancel</button>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const initDataTable = (selector) => {
        new DataTable(selector, {
            order: [[1, 'asc']],
            layout: {
                topStart: {
                    buttons: [
                        { extend: 'print' },
                        { extend: 'excelHtml5', autoFilter: true, sheetName: 'Exported data' },
                        { extend: 'pdfHtml5' },
                        { extend: 'copyHtml5' },
                        { extend: 'pageLength' },
                        {
                            text: 'Monthly Statistics',
                            action: function () {
                                document.getElementById('monthModal').style.display = 'block';
                            }
                        }
                    ]
                }
            },
            language: {
                buttons: {
                    copyTitle: 'Added to clipboard',
                    copyKeys: 'Press <i>ctrl</i> or <i>⌘</i> + <i>C</i> to copy the table data. <br><br>To cancel, press Esc.',
                    copySuccess: {
                        _: '%d rows copied',
                        1: '1 row copied'
                    }
                }
            }
        });
    };

    function closeModal() {
        document.getElementById('monthModal').style.display = 'none';
    }

    function filterByMonth() {
        const month = document.getElementById('selectedMonth').value;
        if (month) {
            const url = `monthly_statistics.php?month=${encodeURIComponent(month)}`;
            window.open(url, '_blank');
            closeModal();
        } else {
            Swal.fire('Please select a month.');
        }
    }

    document.getElementById('select-all').addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('input[name="delete_ids[]"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    initDataTable('#example3');
</script>

<?php 
include('./includes/footer.php');
include('./includes/script.php');
include('../message.php');
?>
