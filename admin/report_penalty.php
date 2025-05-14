<?php 
include('authentication.php');
include('includes/header.php'); 
include('includes/sidebar.php'); 
?>

<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.dataTables.css">

<main id="main" class="main">
    <?php 
    $page = basename($_SERVER['SCRIPT_NAME']); 
    ?>
    <div class="pagetitle">
        <h1>Report</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href=".">Home</a></li>
                <li class="breadcrumb-item active">Report</li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-pills">
                            <li class="nav-item border border-info border-end-0 rounded-start">
                                <a class="nav-link <?= $page == 'report.php' ? 'active' : '' ?>" href="report.php">All Transaction</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $page == 'report_penalty.php' ? 'active' : '' ?>" href="report_penalty.php">Penalty Report</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="row d-flex justify-content-end align-items-center mt-2">
                            <form action="" method="POST" class="col-12 col-md-5 d-flex">
                                <?php date_default_timezone_set('Asia/Manila'); ?>
                                <div class="form-group form-group-sm">
                                    <label for="from_date"> <small>From Date</small></label>
                                    <input type="date" name="from_date" id="from_date" class="form-control form-control-sm" required>
                                </div>
                                <div class="form-group form-group-sm mx-1">
                                    <label for="to_date"> <small>To Date</small></label>
                                    <input type="date" name="to_date" id="to_date" class="form-control form-control-sm" required>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label for="filter_attendance"> <small>Click to Filter</small></label>
                                    <button type="submit" name="filter_attendance" id="filter_attendance" class="btn text-white fw-semibold btn-info btn-sm d-block">Filter</button>
                                </div>
                            </form>
                        </div>

                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="data_table">
                                        <?php
                                        // Fetch admin name
                                        $admin_query = mysqli_query($con, "SELECT admin_name FROM report LIMIT 1") or die(mysqli_connect_error());
                                        $admin_row = mysqli_fetch_array($admin_query);
                                        $admin = $admin_row['admin_name'] ?? 'Admin';

                                        // Handle date filtering
                                        if (isset($_POST['from_date']) && isset($_POST['to_date'])) {
                                            $from_date = $_POST['from_date'];
                                            $to_date = $_POST['to_date'];

                                            $return_query = mysqli_query($con, "SELECT * FROM return_book 
                                                LEFT JOIN book ON return_book.book_id = book.book_id 
                                                LEFT JOIN user ON return_book.user_id = user.user_id
                                                WHERE book_penalty > 1 AND date_returned BETWEEN '$from_date' AND '$to_date' 
                                                ORDER BY return_book.return_book_id DESC") or die(mysqli_connect_error());
                                            
                                            $count_penalty_query = mysqli_query($con, "SELECT SUM(book_penalty) as total_penalty FROM return_book WHERE book_penalty > 0 AND date_returned BETWEEN '$from_date' AND '$to_date'") or die(mysqli_connect_error());
                                            $count_penalty_row = mysqli_fetch_array($count_penalty_query);
                                        } else {
                                            $return_query = mysqli_query($con, "SELECT * FROM return_book 
                                                LEFT JOIN book ON return_book.book_id = book.book_id 
                                                LEFT JOIN user ON return_book.user_id = user.user_id
                                                WHERE book_penalty > 0 
                                                ORDER BY return_book.return_book_id DESC") or die(mysqli_connect_error());

                                            $count_penalty_query = mysqli_query($con, "SELECT SUM(book_penalty) as total_penalty FROM return_book WHERE book_penalty > 0") or die(mysqli_connect_error());
                                            $count_penalty_row = mysqli_fetch_array($count_penalty_query);
                                        }
                                        ?>
                                        
                                        <div class="pull-left">
                                                <div class="span">
                                                    <div class="alert alert-info mt-2 p-1">
                                                        <i class="icon-credit-card icon-large"></i>&nbsp;Total Amount of Penalty: Php <?= number_format($count_penalty_row['total_penalty'], 2) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <table id="example" class="display" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>Penalty Amount</th>
                                                    <th>Received from</th>
                                                    <th>Person In Charge</th>
                                                    <th>Due Date</th>
                                                    <th>Date Returned</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($return_row = mysqli_fetch_array($return_query)) { ?>
                                                <tr>
                                                    <td class="<?= ($return_row['book_penalty'] != 'No Penalty') ? 'alert alert-warning' : ''; ?>" style="width:100px;">
                                                        Php <?= number_format($return_row['book_penalty'], 2) ?>
                                                    </td>
                                                    <td style="text-transform: capitalize"><?= htmlspecialchars($return_row['firstname'] . " " . $return_row['lastname']) ?></td>
                                                    <td><?= htmlspecialchars($admin) ?></td>
                                                    <td><?= date("M d, Y", strtotime($return_row['due_date'])) ?></td>
                                                    <td><?= date("M d, Y", strtotime($return_row['date_returned'])) ?></td>
                                                </tr>
                                                <?php } ?>
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
    </section>
</main>

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
            order: [[3, 'asc']],
            layout: {
                topStart: {
                    buttons: [
                        { extend: 'print' },
                        { extend: 'excelHtml5', autoFilter: true, sheetName: 'Exported data' },
                        { extend: 'pdfHtml5' },
                        { extend: 'copyHtml5' },
                        { extend: 'pageLength' } 
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

    initDataTable('#example');
</script>

<?php 
include('includes/footer.php');
include('includes/script.php');
include('../message.php');   
?>
