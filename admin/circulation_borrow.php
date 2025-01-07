edpf kghw pylr zyncedpf kghw pylr zyncedpf kghw pylr zyncedpf kghw pylr zyncyods rsbo slja qgfcyods rsbo slja qgfcyods rsbo slja qgfcyods rsbo slja qgfc<?php 
include('authentication.php');
include('includes/header.php'); 
include('./includes/sidebar.php'); 

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require 'phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
    require 'phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require 'phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';
?>


<main id="main" class="main">
     <div class="pagetitle">
          <h1>Circulation</h1>
          <nav>
               <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href=".">Home</a></li>
                    <li class="breadcrumb-item"><a href="circulation.php">Circulation</a></li>
                    <li class="breadcrumb-item active">Student Borrow Book</li>
               </ol>
          </nav>
     </div>
     <section class="section ">
          <div class="row">
               <div class="col-lg-12">
                    <div class="card">
                         <div class="card-header text-bg-primary">
                              <i class="bi bi-book"></i> Borrow Book
                         </div>
                         <div class="card-body">
                              <div class="row d-flex justify-content-center">
                                   <div class="col-12 col-md-4 mt-4">
                                        <form action="" method="GET">
                                             <div class="input-group mb-3 input-group-sm">

                                                  <!-- <span class="input-group-text bg-primary text-white"
                                                  id="basic-addon1">SEARCH ID</span> -->
                                                  <input type="text" name="student_id_no"
                                                       value="<?php if(isset($_GET['student_id_no'])){echo $_GET['student_id_no'];}?>"
                                                       class="form-control" placeholder="Enter Student ID"
                                                       aria-label="Username" aria-describedby="basic-addon1" autofocus
                                                       required id="student_id_no" oninput="formatStudentId()">
                                                  <button class="input-group-text bg-primary text-white"
                                                       id="basic-addon1">Search</button>
                                             </div>

                                             <!-- <div class="col-md-3 mt-3">
                                             <button type="submit" name="submit_borrower"
                                                  class="btn btn-primary">Submit</button>
                                        </div> -->
                                        </form>
                                   </div>

                                   <?php
                                  if(isset($_GET['student_id_no']))
                                  {
                                   $student_id_no = $_GET['student_id_no'];

                                   $query = "SELECT * FROM user WHERE student_id_no='$student_id_no'";
                                   $query_run = mysqli_query($con, $query);

                                   if(mysqli_num_rows($query_run) > 0)
                                   {
                                        foreach($query_run as $row)
                                        {
                                             // echo $row['student_id_no'];
                                             $student_id = encryptor('encrypt', $_GET['student_id_no']);
                                                  echo ('<script> location.href="circulation_borrowing.php?a='.$student_id.'";</script');
                                             
                                        }
                                   }
                                   else
                                   {
                                        $_SESSION['message_error'] = 'No ID Found';
                                        // echo ('<script> location.href="circulation_borrow.php";</script');
                                        
                                        
                                        
                                   }
                                  }



                                       
                                   ?>



                              </div>
                         </div>
                         <div class="card-footer">


                         </div>
                    </div>
                    <div class="card">
                         <div class="card-header text-dark fw-semibold ">
                              Recent Borrowed Books
                         </div>
                         <div class="card-body">
                              <div class="table-responsive mt-3">
                              <table id="example" class="display nowrap" style="width:100%">
                                        <thead>
                                             <tr>
                                                  <th>Acc #</th>
                                                  <th>Image</th>
                                                  <th>Borrower Name</th>
                                                  <th>Title</th>
                                                  <th>Date Borrowed</th>
                                                  <th>Due Date</th>
                                                  <th>Date Returned</th>
                                                  <th>Status</th>
                                             </tr>
                                        </thead>
                                        <tbody>
                                             <?php
                                             $borrow_query = mysqli_query($con, "SELECT * FROM borrow_book
                                                  LEFT JOIN book ON borrow_book.book_id = book.book_id 
                                                  LEFT JOIN user ON borrow_book.user_id = user.user_id 
                                                  WHERE borrowed_status = 'borrowed'
                                                  ORDER BY borrow_book.borrow_book_id DESC");
                                             
                                             $borrow_count = mysqli_num_rows($borrow_query);
                                             
                                             while ($borrow_row = mysqli_fetch_array($borrow_query)) {
                                                  $id = $borrow_row['borrow_book_id'];
                                                  $book_id = $borrow_row['book_id'];
                                                  $user_id = $borrow_row['user_id'];

                                                  $due_date = strtotime($borrow_row['due_date']);
                                                  $current_date = time();
                                                  $one_day_seconds = 86400; // Number of seconds in a day

                                                  // Check for overdue notifications
                                                  if ($current_date > $due_date && !$borrow_row['date_returned']) {
                                                       $mail = new PHPMailer(true);
                                                       try {
                                                            $mail->isSMTP();
                                                            $mail->Host = 'smtp.gmail.com';
                                                            $mail->SMTPAuth = true;
                                                            $mail->Username = 'mcclearningresourcecenter2.0@gmail.com';
                                                            $mail->Password = 'edpf kghw pylr zync';
                                                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                                                            $mail->Port = 587;

                                                            $mail->setFrom('mcclearningresourcecenter2.0@gmail.com', 'MCC Learning Resource Center');
                                                            $mail->addAddress($borrow_row['email']);

                                                            $mail->isHTML(true);
                                                            $mail->Subject = 'Overdue Book Notification';
                                                            $mail->Body = "
                                                            <html>
                                                                 <head>
                                                                 <style>
                                                                      body {
                                                                           font-family: Arial, sans-serif;
                                                                           background-color: #f4f4f4;
                                                                           margin: 0;
                                                                           padding: 0;
                                                                      }
                                                                      .container {
                                                                           width: 80%;
                                                                           margin: 20px auto;
                                                                           padding: 20px;
                                                                           background-color: #fff;
                                                                           border-radius: 8px;
                                                                           box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                                                                      }
                                                                      .header {
                                                                           text-align: center;
                                                                           padding-bottom: 20px;
                                                                           border-bottom: 1px solid #ddd;
                                                                      }
                                                                      .content {
                                                                           padding: 20px 0;
                                                                      }
                                                                 </style>
                                                                 </head>
                                                                 <body>
                                                                 <div class='container'>
                                                                      <div class='header'>
                                                                           <img src='https://mcc-lrc.com/images/mcc-lrc.png' alt='Logo'>
                                                                      </div>
                                                                      <div class='content'>
                                                                           <p>Dear " . $borrow_row['firstname'] . " " . $borrow_row['lastname'] . ",</p>
                                                                           <p>Your borrowed book " . $borrow_row['title'] . " is overdue. Please return it as soon as possible.</p>
                                                                           <p>Thank you!</p>
                                                                      </div>
                                                                 </div>
                                                                 </body>
                                                            </html>
                                                            ";
                                                            $mail->send();
                                                       } catch (Exception $e) {
                                                            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
                                                       }
                                                  }

                                                  // Check for due date notifications
                                                  if ($due_date - $current_date <= $one_day_seconds && !$borrow_row['date_returned']) {
                                                       $mail = new PHPMailer(true);
                                                       try {
                                                            $mail->isSMTP();
                                                            $mail->Host = 'smtp.gmail.com';
                                                            $mail->SMTPAuth = true;
                                                            $mail->Username = 'mcclearningresourcecenter2.0@gmail.com';
                                                            $mail->Password = 'edpf kghw pylr zync';
                                                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                                                            $mail->Port = 587;

                                                            $mail->setFrom('mcclearningresourcecenter2.0@gmail.com', 'MCC Learning Resource Center');
                                                            $mail->addAddress($borrow_row['email']);

                                                            $mail->isHTML(true);
                                                            $mail->Subject = 'Book Due Date Reminder';
                                                            $mail->Body = "
                                                            <html>
                                                                 <head>
                                                                 <style>
                                                                      body {
                                                                           font-family: Arial, sans-serif;
                                                                           background-color: #f4f4f4;
                                                                           margin: 0;
                                                                           padding: 0;
                                                                      }
                                                                      .container {
                                                                           width: 80%;
                                                                           margin: 20px auto;
                                                                           padding: 20px;
                                                                           background-color: #fff;
                                                                           border-radius: 8px;
                                                                           box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                                                                      }
                                                                      .header {
                                                                           text-align: center;
                                                                           padding-bottom: 20px;
                                                                           border-bottom: 1px solid #ddd;
                                                                      }
                                                                      .content {
                                                                           padding: 20px 0;
                                                                      }
                                                                 </style>
                                                                 </head>
                                                                 <body>
                                                                 <div class='container'>
                                                                      <div class='header'>
                                                                           <img src='https://mcc-lrc.com/images/mcc-lrc.png' alt='Logo'>
                                                                      </div>
                                                                      <div class='content'>
                                                                           <p>Dear " . $borrow_row['firstname'] . " " . $borrow_row['lastname'] . ",</p>
                                                                           <p>This is a reminder that your borrowed book " . $borrow_row['title'] . " is due tomorrow. Please return it on time.</p>
                                                                           <p>Thank you!</p>
                                                                      </div>
                                                                 </div>
                                                                 </body>
                                                            </html>
                                                            ";
                                                            $mail->send();
                                                       } catch (Exception $e) {
                                                            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
                                                       }
                                                  }
                                             ?>

                                             <tr>
                                                  <td style="text-align: center;">
                                                       <?php echo $borrow_row['accession_number']; ?>
                                                  </td>
                                                  <td>
                                                       <center>
                                                            <?php if($borrow_row['book_image'] != ""): ?>
                                                                 <img src="../uploads/books_img/<?php echo $borrow_row['book_image']; ?>" alt="" width="80px" height="80px">
                                                            <?php else: ?>
                                                                 <img src="../uploads/books_img/book_image.jpg" alt="" width="80px" height="80px">
                                                            <?php endif; ?>
                                                       </center>
                                                  </td>
                                                  <td style="text-transform: capitalize">
                                                       <?php echo $borrow_row['firstname']." ".$borrow_row['lastname']; ?>
                                                  </td>
                                                  <td style="text-transform: capitalize">
                                                       <?php echo $borrow_row['title']; ?>
                                                  </td>
                                                  <td><?php echo date("M d, Y", strtotime($borrow_row['date_borrowed'])); ?></td>
                                                  <td><?php echo date("M d, Y", strtotime($borrow_row['due_date'])); ?></td>
                                                  <td><?php echo ($borrow_row['date_returned'] == "0000-00-00 00:00:00") ? "Pending" : date("M d, Y h:i:s a", strtotime($borrow_row['date_returned'])); ?></td>
                                                  <td class="<?php echo $borrow_row['borrowed_status'] != 'borrowed' ? 'alert alert-success' : 'alert alert-danger'; ?>" style="text-transform: capitalize">
                                                       <?php echo $borrow_row['borrowed_status']; ?>
                                                  </td>
                                             </tr>
                                             <?php } // End of while loop 

                                             if ($borrow_count <= 0) {
                                                  echo '
                                                       <table style="float:right;">
                                                            <tr>
                                                                 <td style="padding:10px;" class="alert alert-danger">No Books Borrowed at this Moment</td>
                                                            </tr>
                                                       </table>
                                                  ';
                                             } ?>
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
<script>
function formatStudentId() {
    var input = document.getElementById('student_id_no');
    var value = input.value;

    // Remove all non-numeric characters except the hyphen
    value = value.replace(/[^0-9-]/g, '');

    // Ensure only one hyphen exists, and it's placed correctly
    var hyphenIndex = value.indexOf('-');
    if (hyphenIndex !== -1) {
        // If hyphen exists, ensure it's after the 4th character
        if (hyphenIndex > 4) {
            value = value.slice(0, 4) + '-' + value.slice(5);
        }
    }

    // Allow only one hyphen, and the hyphen must be placed between 4th and 5th digit
    if (value.length > 9) {
        value = value.slice(0, 9); // Limit to a max of 9 characters (e.g., 1234-1234)
    }

    input.value = value;
}
</script>
<?php 
include('./includes/footer.php');
include('./includes/script.php');
include('../message.php');   
?>

<script>
var select_box_element = document.querySelector('#select_box');

dselect(select_box_element, {
     search: true
});
</script>
