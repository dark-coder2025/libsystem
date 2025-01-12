<?php 
include('authentication.php');
include('includes/header.php'); 
include('./includes/sidebar.php'); 
?>

<main id="main" class="main">
    <div class="pagetitle" data-aos="fade-down">
        <h1>MS 365 Account</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href=".">Home</a></li>
                <li class="breadcrumb-item active">MS 365 Account</li>
            </ol>
        </nav>
    </div>

    <section class="section dashboard">
        <div class="row">
            <div class="col-12">
                <div class="card recent-sales overflow-auto border-3 border-top border-info">
                    <div class="card-body">
                        <div class="row gap-3 gap-md-0 d-flex justify-content-between align-items-center mt-4">
                            <div class="col-md-6 text-start">
                                <form action="import.php" method="post" enctype="multipart/form-data">
                                    <div class="input-group">
                                        <input type="file" name="file" class="form-control" required>
                                        <button type="submit" name="save_excel_data" class="btn btn-primary">
                                            <b>Import</b>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-4 text-end">
                                <button class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                                    <b>Add New Account</b>
                                </button>
                            </div>
                        </div>
                        <br>
                        <div class="table-responsive">
                            <table id="example" class="table table-striped table-bordered nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Firstname</th>
                                        <th>Lastname</th>
                                        <th>Email</th>
                                        <th>Used</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT * FROM ms_account";
                                    if ($stmt = $con->prepare($query)) {
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>
                                                    <td>{$row['firstname']}</td>
                                                    <td>{$row['lastname']}</td>
                                                    <td>{$row['username']}</td>
                                                    <td><center>{$row['used']}</center></td>
                                                    <td><center>
                                                        <button class='btn btn-warning editBtn' data-bs-toggle='modal' data-bs-target='#editAccountModal' 
                                                                data-msid='{$row['ms_id']}' data-firstname='{$row['firstname']}' data-lastname='{$row['lastname']}' data-username='{$row['username']}' data-used='{$row['used']}'>
                                                            Edit
                                                        </button></center>
                                                    </td>
                                                </tr>";
                                        }
                                        $stmt->close();
                                    } else {
                                        echo "<tr><td colspan='6'>Error retrieving data</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Add Account Modal -->
<div class="modal fade" id="addAccountModal" tabindex="-1" aria-labelledby="addAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAccountModalLabel">Add New MS 365 Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="add_account.php" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="firstname" class="form-label">Firstname</label>
                        <input type="text" class="form-control" id="firstname" name="firstname" required>
                    </div>
                    <div class="mb-3">
                        <label for="lastname" class="form-label">Lastname</label>
                        <input type="text" class="form-control" id="lastname" name="lastname" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Email</label>
                        <input type="email" class="form-control" id="username" name="username" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add_account" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Account Modal -->
<div class="modal fade" id="editAccountModal" tabindex="-1" aria-labelledby="editAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAccountModalLabel">Edit MS 365 Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="update_account.php" method="post">
                <div class="modal-body">
                    <!-- Hidden input for ms_id -->
                    <input type="hidden" id="ms_id" name="ms_id">
                    <div class="mb-3">
                        <label for="fname" class="form-label">Firstname</label>
                        <input type="text" class="form-control" id="fname" name="fname" required>
                    </div>
                    <div class="mb-3">
                        <label for="lname" class="form-label">Lastname</label>
                        <input type="text" class="form-control" id="lname" name="lname" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="used" class="form-label">Used</label>
                        <input type="text" class="form-control" id="used" name="used" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="update_account" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
include('./includes/footer.php');
include('./includes/script.php');
include('../message.php');
?>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialize DataTable (if necessary)
    new DataTable('#example', {
        responsive: true,
        rowReorder: {
            selector: 'td:nth-child(2)'
        }
    });

    // Event listener for the Edit button
    const editButtons = document.querySelectorAll('.editBtn');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Get the data attributes from the button
            const msid = this.getAttribute('data-msid');
            const firstname = this.getAttribute('data-firstname');
            const lastname = this.getAttribute('data-lastname');
            const username = this.getAttribute('data-username');
            const used = this.getAttribute('data-used');
            
            // Set the values into the modal's input fields
            document.getElementById('ms_id').value = msid;
            document.getElementById('fname').value = firstname;
            document.getElementById('lname').value = lastname;
            document.getElementById('email').value = username;
            document.getElementById('used').value = used;
        });
    });

    // Apply event listener to edit buttons after DataTable initializes
    applyEditButtonListener();

    // Reapply event listener after DataTable pagination (if needed)
    dataTable.on('draw', function() {
        applyEditButtonListener();
    });
});
</script>
