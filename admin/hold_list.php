<?php
ob_start(); // Start output buffering
include('authentication.php');
include('includes/header.php');
include('./includes/sidebar.php'); 
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Hold Books</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href=".">Home</a></li>
                <li class="breadcrumb-item active">Hold Books</li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header text-bg-primary">
                        <i class="bi bi-book"></i> Hold Books
                    </div>
                    <div class="card-body">
                        <div class="row d-flex justify-content-center">
                            <div class="col-12 col-md-4 mt-4">
                                <form action="" method="GET">
                                    <div class="input-group mb-3 input-group-sm">
                                        <input type="text" name="id_no"
                                               value="<?php echo isset($_GET['id_no']) ? htmlspecialchars($_GET['id_no']) : ''; ?>"
                                               class="form-control" placeholder="Enter Student ID or Faculty Name"
                                               aria-label="ID" aria-describedby="basic-addon1" autofocus required>
                                        <button class="input-group-text bg-primary text-white"
                                                id="basic-addon1">Search</button>
                                    </div>
                                </form>
                            </div>

                            <?php
                            if (isset($_GET['id_no'])) {
                                $id_no = mysqli_real_escape_string($con, $_GET['id_no']);
                                
                                $query_user = "SELECT * FROM user WHERE student_id_no='$id_no'";
                                $query_faculty = "SELECT * FROM faculty WHERE username = '$id_no'";
                                
                                $query_run_user = mysqli_query($con, $query_user);
                                $query_run_faculty = mysqli_query($con, $query_faculty);

                                if (mysqli_num_rows($query_run_user) > 0) {
                                    $row = mysqli_fetch_assoc($query_run_user);
                                    $user_id = $row['user_id'];
                                    echo '<script> location.href="hold_view.php?id='.$user_id.'&type=user";</script>';
                                    exit(0); // Make sure script execution stops here
                                } elseif (mysqli_num_rows($query_run_faculty) > 0) {
                                    $row = mysqli_fetch_assoc($query_run_faculty);
                                    $faculty_id = $row['faculty_id'];
                                    echo '<script> location.href="hold_view.php?id='.$faculty_id.'&type=faculty";</script>';
                                    exit(0); // Make sure script execution stops here
                                } else {
                                    $_SESSION['status'] = 'No ID or Username Found';
                                    $_SESSION['status_code'] = "error";
                                    header("Location: hold_list.php");
                                    exit(0);
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header text-dark fw-semibold">
                        Recent Hold Books
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mt-3">
                        <table id="example" class="display nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Borrower Name</th>
                                        <th>Hold Books</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $borrow_query = mysqli_query($con, "SELECT 
                                    u.user_id, u.firstname AS user_firstname, u.lastname AS user_lastname, 
                                    f.faculty_id, f.firstname AS faculty_firstname, f.lastname AS faculty_lastname,
                                    h.hold_id,
                                    COUNT(h.hold_id) AS num_hold_books
                                    FROM holds h
                                    LEFT JOIN user u ON u.user_id = h.user_id AND h.hold_status = 'Hold'
                                    LEFT JOIN faculty f ON f.faculty_id = h.faculty_id AND h.hold_status = 'Hold'
                                    WHERE h.hold_status = 'Hold'
                                    GROUP BY u.user_id, f.faculty_id
                                    ORDER BY h.hold_id DESC");

                                    $borrow_count = mysqli_num_rows($borrow_query);
                                    while($holdlist = mysqli_fetch_array($borrow_query)) {
                                        $name = $holdlist['user_id'] ? $holdlist['user_firstname'].' '.$holdlist['user_lastname'] : $holdlist['faculty_firstname'].' '.$holdlist['faculty_lastname'];
                                        $id = $holdlist['user_id'] ? $holdlist['user_id'] : $holdlist['faculty_id'];
                                    ?>
                                    <tr>
                                        <td style="text-transform: capitalize">
                                            <?php echo htmlspecialchars($name); ?>
                                        </td>
                                        <td><?= htmlspecialchars($holdlist['num_hold_books']); ?></td>
                                        <td class="justify-content-center">
                                            <div class="btn-group" style="background: #DFF6FF;">
                                                <!-- View Hold Books Action -->
                                                <a href="hold_view.php?id=<?= htmlspecialchars($id); ?>&type=<?= htmlspecialchars($holdlist['user_id'] ? 'user' : 'faculty'); ?>"
                                                   class="viewBookBtn btn btn-sm border text-primary"
                                                   data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                   title="View Hold Books">
                                                   View
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } 
                                    if ($borrow_count <= 0) {
                                        echo '
                                            <table style="float:right;">
                                                <tr>
                                                    <td style="padding:10px;" class="alert alert-danger">No Hold Books at this Moment</td>
                                                </tr>
                                            </table>
                                        ';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer"></div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    new DataTable('#example', {
    responsive: true,
    rowReorder: {
        selector: 'td:nth-child(2)'
    }
});
});
</script>

<?php 
include('./includes/footer.php');
include('./includes/script.php');
include('../message.php');   
ob_end_flush(); // End output buffering and flush output
?>

<script>
var select_box_element = document.querySelector('#select_box');
dselect(select_box_element, {
    search: true
});
</script>
