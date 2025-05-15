<?php 
include('authentication.php');
include('includes/header.php'); 
include('./includes/sidebar.php'); 

// if (isset($_POST['delete_all'])) {
//      $delete_query = "DELETE FROM user_log";
//      if (mysqli_query($con, $delete_query)) {
//          echo "<script>alert('All user logs have been deleted successfully.');</script>";
//      } else {
//          echo "<script>alert('Error deleting user logs: " . mysqli_error($con) . "');</script>";
//      }
//  }
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
                         <div class="row">
                         <div data-aos="fade-down" class="col-12">
                              <div class="card recent-sales overflow-auto  border-3 border-top border-info">

                                   <div class="card-body">
                                        <div class="row d-flex justify-content-end align-items-center mt-2">
                                             <form action="" method="POST" class="col-12 col-md-5 d-flex ">
                                                  <?php date_default_timezone_set('Asia/Manila'); ?>
                                                  <div class="form-group form-group-sm">
                                                       <label for=""> <small>From Date</small></label>
                                                       <input type="date" name="from_date" id="disable_date"
                                                            class="form-control form-control-sm"></input>
                                                  </div>

                                                  <div class="form-group form-group-sm mx-2">
                                                       <label for=""> <small>To Date</small></label>
                                                       <input type="date" name="to_date" id="disable_date2"
                                                            class="form-control form-control-sm"></input>
                                                  </div>
                                                  <div class="form-group form-group-sm">
                                                       <label for=""> <small>Click to Filter</small></label>
                                                       <button type="submit" name="filter_attendance"
                                                            class="btn text-white fw-semibold btn-info btn-sm d-block">Filter</button>
                                                  </div>
                                             </form>

                                             <!-- <form action="" method="POST" class="col-12 col-md-3 d-flex justify-content-center">
                                                            <button type="submit" name="delete_all" class="btn btn-danger btn-sm">Delete All Logs</button>
                                                       </form> -->

                                        </div>

                                        <div class="container">
                                             <div class="row">
                                                  <div class="col-12">

                                                       <div class="data_table">
                                                       <table id="example3" class="display" style="width:100%">
                                                                 <thead>
                                                                      <tr>
                                                                      <th>Date</th>
                                                                                     <th>Time in</th>
                                                                                     <th>Full Name</th>
                                                                                     <th>Program</th>
                                                                                     <th>Time out</th>
                                                                      </tr>
                                                                 </thead>
                                                                 <tbody>
                                                                      <?php
                                 
                                                       
                                 if(isset($_POST['from_date']) && isset($_POST['to_date']))
                                 {
                                      $from_date = $_POST['from_date'];
                                      $to_date = $_POST['to_date'];

                                      $query = "SELECT * FROM user_log WHERE date_log BETWEEN '$from_date' AND '$to_date' ORDER BY date_log DESC, time_log DESC";
                                      $query_run = mysqli_query($con, $query);

                                      if(mysqli_num_rows($query_run) > 0 )
                                      {
                                           foreach($query_run as $row)
                                           {
                                 ?>
                                                          <tr>
                                                               <?php date_default_timezone_set('Asia/Manila'); ?>
                                                               
                                                            <td><?= date("M d, Y", strtotime($row['date_log'])); ?></td>
                                                            <td><?= date("h:i a", strtotime($row['time_log'])); ?></td>
                                                            <td><?= $row['firstname'].' '.$row['middlename'].' '.$row['lastname']; ?></td>
                                                            <td><?=$row['year_level'].' - '.$row['course']; ?></td>
                                                            <td><?= date("h:i a", strtotime($row['time_out'])); ?></td>
                                                          </tr>
                                                          <?php      }
                            }
                            
                       }
                       else
                       {
                       
                            $result= mysqli_query($con,"SELECT * FROM user_log ORDER BY date_log DESC, time_log DESC");
                            while ($row= mysqli_fetch_array ($result) ){
                           
                                                  ?>
                                                                      <tr>
                                                                                     <?php date_default_timezone_set('Asia/Manila'); ?>
                                                                                     <td><?= date("M d, Y", strtotime($row['date_log'])); ?>
                                                                                     </td>
                                                                                     <td><?= date("h:i a", strtotime($row['time_log'])); ?>
                                                                                     </td>
                                                                                     <td><?= $row['firstname'].' '.$row['middlename'].' '.$row['lastname']; ?>
                                                                                     </td>
                                                                                     <td><?=$row['year_level'].' - '.$row['course']; ?></td>
                                                                                     <td><?= date("h:i a", strtotime($row['time_out'])); ?></td>
                                                                                </tr>
                                                                      <?php } 
                                                       }
                                                 
                                                       ?>

                                                                 </tbody>
                                                            </table>
                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
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

<script>
    const initDataTable = (selector) => {
        new DataTable(selector, {
            order: [[0, 1, 'asc']],
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
                    copyKeys: 'Press <i>ctrl</i> or <i>\u2318</i> + <i>C</i> to copy the table data to your clipboard. <br><br>To cancel, click this message or press Esc.',
                    copySuccess: {
                        _: '%d rows copied',
                        1: '1 row copied'
                    }
                }
            }
        });
    };

    // Function to close the modal
    function closeModal() {
        document.getElementById('monthModal').style.display = 'none';
    }

    // Function to filter by month and open new tab
    function filterByMonth() {
        const month = document.getElementById('selectedMonth').value;
        if (month) {
            const url = `monthly_statistics.php?month=${encodeURIComponent(month)}`;
            window.open(url, '_blank');
            closeModal();
        } else {
            alert('Please select a month.');
        }
    }

    initDataTable('#example3');
</script>



<?php 
include('./includes/footer.php');
include('./includes/script.php');
include('../message.php');

    
?>