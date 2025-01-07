<?php 
include('authentication.php');
include('includes/header.php'); 
include('./includes/sidebar.php');
?>

<style>
     #studbadge{
          position: relative;
          top: -15px;
          left: 20px;
     }
</style>

<main id="main" class="main">
     <div class="pagetitle d-flex justify-content-between">
          <div>
               <h1>Manage Students</h1>
               <nav>
                    <ol class="breadcrumb">
                         <li class="breadcrumb-item"><a href="users.php">Users</a></li>
                         <li class="breadcrumb-item active">Students</li>
                    </ol>
               </nav>
          </div>

     </div>
     <section class="section">
          <div class="row">
               <div class="col-lg-12">
                    <div class="card">
                         <div class="card-header d-flex justify-content-between align-items-center">
                              <a href="user_student_approval.php" class="btn btn-primary position-relative">
                                   <i class="bi bi-people-fill"></i>
                                   Student Approval
                                   <?php
                                   // Example query to count pending student approvals
                                   $sql = "SELECT COUNT(*) AS pending_student_count FROM user WHERE role_as = 'student' AND status = 'pending'";
                                   $result = mysqli_query($con, $sql);
                                   $row = mysqli_fetch_assoc($result);
                                   $pendingStudentCount = $row['pending_student_count'];

                                   if ($pendingStudentCount > 0) {
                                        echo '<span class="badge bg-danger" id="studbadge">' . $pendingStudentCount . '</span>';
                                   }
                                   ?>
                              </a>
                              <a href="users.php" class="btn btn-primary position-relative">Back</a>
                         </div>
                         <div class="card-body">
                              <div class="table-responsive mt-3">
                              <table id="example" class="display nowrap" style="width:100%">
                                        <thead>
                                             <tr>
                                                  <th><center>Full Name</center></th>
                                                  <th><center>Student Profile</center></th>
                                                  <th><center>Student No</center></th>
                                                  <th><center>Gender</center></th>
                                                  <th><center>Course</center></th>
                                                  <th><center>Year Level</center></th>
                                                  <th><center>Action</center></th>
                                             </tr>
                                        </thead>
                                        <tbody>
                                             <?php
                                             $query = "SELECT * FROM user WHERE status IN ('approved', 'blocked') AND role_as = 'student' ORDER BY user_id ASC";
                                             $query_run = mysqli_query($con, $query);

                                             if(mysqli_num_rows($query_run)) {
                                                  foreach($query_run as $user) {
                                                       ?>
                                                       <tr>
                                                            <td style="text-transform:capitalize;"><center><?=$user['lastname'].',  '.$user['firstname'].' '.$user['middlename'];?></center></td>
                                                            <td>
                                                                 <center>
                                                                      <?php if($user['profile_image'] != ""): ?>
                                                                      <img src="../uploads/profile_images/<?php echo $user['profile_image']; ?>"
                                                                           alt="image" width="120px" height="100px">
                                                                      <?php else: ?>
                                                                      <img src="uploads/books_img/book_image.jpg" alt=""
                                                                           width="120px" height="100px">
                                                                      <?php endif; ?>
                                                                 </center>
                                                            </td>
                                                            <td><center><?=$user['student_id_no'];?></center></td>
                                                            <td><center><?=$user['gender'];?></center></td>
                                                            <td><center><?=$user['course'];?></center></td>
                                                            <td><center><?=$user['year_level'];?></center></td>
                                                            <td class="justify-content-center">
                                                                 <center>
                                                                 <div class="btn-group" style="background: #DFF6FF;">
                                                                      <button type="button" class="btn btn-sm border dropdown-toggle text-primary" data-bs-toggle="dropdown" aria-expanded="false">
                                                                           <i class="bi bi-gear-fill"></i>
                                                                      </button>
                                                                      <ul class="dropdown-menu">
                                                                           <!-- View Student Action -->
                                                                           <li><a href="user_student_view.php?b=<?=encryptor('encrypt',$user['user_id']);?>" class="dropdown-item text-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" title="View Student">
                                                                                <i class="bi bi-eye-fill"></i> View
                                                                           </a></li>
                                                                           <!-- Edit Student Action -->
                                                                           <li><a href="#" class="dropdown-item text-success" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit Student" onclick="loadStudentData('<?=$user['user_id'];?>')">
                                                                                <i class="bi bi-pencil-fill"></i> Edit
                                                                           </a></li>
                                                                           <!-- Block/Unblock Student Action -->
                                                                           <?php if($user['status'] == 'approved'): ?>
                                                                                <li><a href="#" class="dropdown-item text-warning" onclick="confirmBlock('<?=$user['user_id'];?>')" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Block Student">
                                                                                     <i class="bi bi-lock-fill"></i> Block
                                                                                </a></li>
                                                                           <?php else: ?>
                                                                                <li><a href="#" class="dropdown-item text-success" onclick="confirmUnblock('<?=$user['user_id'];?>')" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Unblock Student">
                                                                                     <i class="bi bi-unlock-fill"></i> Unblock
                                                                                </a></li>
                                                                           <?php endif; ?>
                                                                           <!-- Delete Student Action -->
                                                                           <li><a href="#" class="dropdown-item text-danger" onclick="confirmDelete('<?=$user['user_id'];?>')" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete Student">
                                                                                <i class="bi bi-trash-fill"></i> Delete
                                                                           </a></li>
                                                                           <!-- Generate ID Card Action -->
                                                                           <li><a href="user_student_id.php?a=<?= encryptor('encrypt',$user['user_id']); ?>" target="_blank" class="dropdown-item text-info" data-bs-toggle="tooltip"  data-bs-placement="bottom" title="Generate Library ID">
                                                                                <i class="bi bi-card-heading"></i> Generate Library ID
                                                                           </a></li>
                                                                      </ul>
                                                                 </div>
                                                                 </center>
                                                            </td>
                                                       </tr>
                                                       <?php
                                                  }
                                             } else {
                                                  echo "No records found";
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
<!-- Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editStudentForm" method="POST" action="user_student_code.php">
          <input type="hidden" name="edit_student_id" id="editStudentId">
          <div class="mb-3">
            <label for="editLName" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="editLName" name="edit_last_name" style="text-transform:capitalize;" required>
          </div>
          <div class="mb-3">
            <label for="editFName" class="form-label">First Name</label>
            <input type="text" class="form-control" id="editFName" name="edit_first_name" style="text-transform:capitalize;" required>
          </div>
          <div class="mb-3">
            <label for="editMName" class="form-label">Middle Name</label>
            <input type="text" class="form-control" id="editMName" name="edit_middle_name" style="text-transform:capitalize;">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Delete Student Modal -->
<div class="modal fade" id="deleteStudentModal" tabindex="-1" aria-labelledby="deleteStudentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteStudentModalLabel">Delete Student</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="deleteStudentForm" method="POST" action="user_student_code.php">
          <input type="hidden" value="<?= $user['user_id']; ?>" name="delete_student_id" id="deleteStudentId">
          <div class="mb-3">
          <label for="deleteReason" class="form-label">Reason for Delete</label>
          <textarea class="form-control" id="deleteReason" name="delete_reason" rows="4" required></textarea>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php 
include('./includes/footer.php');
include('./includes/script.php');
include('../message.php');   
?>

<script>
function confirmDelete(userId) {
     fetch('user_student_code.php?id=' + userId)
        .then(response => response.json())
        .then(data => {
            document.getElementById('deleteStudentId').value = data.user_id;

            var myModal = new bootstrap.Modal(document.getElementById('deleteStudentModal'));
            myModal.show();
        })
        .catch(error => {
            console.error('Error fetching student data:', error);
        });
}

function loadStudentData(userId) {
    fetch('user_student_code.php?id=' + userId)
        .then(response => response.json())
        .then(data => {
            // Sanitize data to remove any potential XSS tags
            document.getElementById('editStudentId').value = sanitizeInput(data.user_id);
            document.getElementById('editLName').value = sanitizeInput(data.lastname);
            document.getElementById('editFName').value = sanitizeInput(data.firstname);
            document.getElementById('editMName').value = sanitizeInput(data.middlename);

            var myModal = new bootstrap.Modal(document.getElementById('editStudentModal'));
            myModal.show();
        })
        .catch(error => {
            console.error('Error fetching student data:', error);
        });
}

document.getElementById('editLName').addEventListener('input', function () {
     var editLName = this.value.trim(); // Remove any leading or trailing spaces
          
     var alphabetPattern = /^[A-Za-zñÑ.\s]+$/; // Pattern for alphabet and spaces only
          
     // Check if input starts with a space
     if (this.value !== editLName) {
          this.setCustomValidity('Name cannot start with a space.');
     } else if (alphabetPattern.test(editLName)) {
          this.setCustomValidity(''); // If valid, clear any previous error message
     } else {
          this.setCustomValidity('Please enter a valid name with only letters and no leading/trailing spaces.');
     }
          
     // Check validity and toggle the invalid class
     var isValid = alphabetPattern.test(editLName) && this.value === editLName; // Ensure no leading spaces
     this.classList.toggle('is-invalid', !isValid);
});

document.getElementById('editFName').addEventListener('input', function () {
     var editFName = this.value.trim(); // Remove any leading or trailing spaces
          
     var alphabetPattern = /^[A-Za-zñÑ.\s]+$/; // Pattern for alphabet and spaces only
          
     // Check if input starts with a space
     if (this.value !== editFName) {
          this.setCustomValidity('Name cannot start with a space.');
     } else if (alphabetPattern.test(editFName)) {
          this.setCustomValidity(''); // If valid, clear any previous error message
     } else {
          this.setCustomValidity('Please enter a valid name with only letters and no leading/trailing spaces.');
     }
          
     // Check validity and toggle the invalid class
     var isValid = alphabetPattern.test(editFName) && this.value === editFName; // Ensure no leading spaces
     this.classList.toggle('is-invalid', !isValid);
});

document.getElementById('editMName').addEventListener('input', function () {
     var editMName = this.value.trim(); // Remove any leading or trailing spaces
          
     var alphabetPattern = /^[A-Za-zñÑ.\s]+$/; // Pattern for alphabet and spaces only
          
     // Check if input starts with a space
     if (this.value !== editMName) {
          this.setCustomValidity('Name cannot start with a space.');
     } else if (alphabetPattern.test(editMName)) {
          this.setCustomValidity(''); // If valid, clear any previous error message
     } else {
          this.setCustomValidity('Please enter a valid name with only letters and no leading/trailing spaces.');
     }
          
     // Check validity and toggle the invalid class
     var isValid = alphabetPattern.test(editMName) && this.value === editMName; // Ensure no leading spaces
     this.classList.toggle('is-invalid', !isValid);
});

document.getElementById('deleteReason').addEventListener('input', function () {
        var deleteReason = this.value.trim();
        
        var dangerousCharsPattern = /[<>\"\']/;
        
        if (deleteReason === "") {
            this.setCustomValidity('Reason cannot be empty or just spaces.');
        } else if (this.value !== deleteReason) {
            this.setCustomValidity('Reason cannot start with a space.');
        } else if (dangerousCharsPattern.test(deleteReason)) {
            this.setCustomValidity('Reason cannot contain HTML special characters like <, >, ", \'.');
        } else {
            this.setCustomValidity('');
        }
        
        var isValid = deleteReason !== "" && this.value === deleteReason && !dangerousCharsPattern.test(deleteReason);
        this.classList.toggle('is-invalid', !isValid);
    });
    
// Sanitize input: remove any HTML tags
function sanitizeInput(input) {
    return input.replace(/<\/?[^>]+(>|$)/g, "");
}

function confirmBlock(userId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You are about to block this student!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.isConfirmed) {
            // Proceed with the blocking
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'user_student_code.php';

            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'block_student';
            input.value = userId;

            form.appendChild(input);

            document.body.appendChild(form);
            form.submit();
        }
    });
}

function confirmUnblock(userId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You are about to unblock this student!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.isConfirmed) {
            // Proceed with the unblocking
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'user_student_code.php';

            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'unblock_student';
            input.value = userId;

            form.appendChild(input);

            document.body.appendChild(form);
            form.submit();
        }
    });
}

// function confirmDelete(userId) {
//     Swal.fire({
//         title: 'Are you sure to delete this?',
//         icon: 'warning',
//         showCancelButton: true,
//         confirmButtonColor: '#3085d6',
//         cancelButtonColor: '#d33',
//         confirmButtonText: 'Yes'
//     }).then((result) => {
//         if (result.isConfirmed) {
//             // Proceed with the deletion
//             var form = document.createElement('form');
//             form.method = 'POST';
//             form.action = 'user_student_code.php';

//             var input = document.createElement('input');
//             input.type = 'hidden';
//             input.name = 'delete_student';
//             input.value = userId;

//             form.appendChild(input);

//             document.body.appendChild(form);
//             form.submit();
//         }
//     });
// }

document.addEventListener('DOMContentLoaded', function () {
     // Check if the DataTable is already initialized
    if (!$.fn.DataTable.isDataTable('#example')) {
        $('#example').DataTable({
            responsive: true,
            rowReorder: {
                selector: 'td:nth-child(2)'
            }
        });
    }
});
</script>
