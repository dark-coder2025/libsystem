<?php
include('authentication.php');

// Include the barcode generator
// require_once('barcode/vendor/autoload.php'); 

// Update Status
// if (isset($_POST['update_status'])) {
//     $accession_number = mysqli_real_escape_string($con, $_POST['accession_number']);
//     $status = mysqli_real_escape_string($con, $_POST['status']);

//     // Update query
//     $update_query = "UPDATE book SET status = '$status' WHERE accession_number = '$accession_number'";
//     $update_query_run = mysqli_query($con, $update_query);

//     if ($update_query_run) {
//         echo json_encode(['success' => true]);
//     } else {
//         echo json_encode(['success' => false]);
//     }
//     exit();
// }

// Existing code for handling delete, update, and add operations

// Delete Book
if (isset($_POST['delete_book'])) {
    $accession_number = $_POST['accession_number']; // Get input

    // Prepare the select query to find the book
    $select_query = "SELECT book_id, title, copyright_date, author, isbn FROM book WHERE accession_number = ?";
    $stmt = mysqli_prepare($con, $select_query);
    mysqli_stmt_bind_param($stmt, "s", $accession_number);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $book_id = $row['book_id'];
        $title = $row['title'];
        $copyright_date = $row['copyright_date'];
        $author = $row['author'];
        $isbn = $row['isbn'];

        // Begin transaction
        mysqli_begin_transaction($con);

        // Delete related records in borrow_book
        $delete_borrow_query = "DELETE FROM borrow_book WHERE book_id = ?";
        $stmt_borrow = mysqli_prepare($con, $delete_borrow_query);
        mysqli_stmt_bind_param($stmt_borrow, "i", $book_id);
        $borrow_result = mysqli_stmt_execute($stmt_borrow);

        if (!$borrow_result) {
            mysqli_rollback($con);
            $_SESSION['status'] = "Failed to delete related borrow records.";
            $_SESSION['status_code'] = "error";
            header("Location: book_views.php?tab=copies");
            exit(0);
        }

        // Delete related records in return_book
        $delete_return_query = "DELETE FROM return_book WHERE book_id = ?";
        $stmt_return = mysqli_prepare($con, $delete_return_query);
        mysqli_stmt_bind_param($stmt_return, "i", $book_id);
        $return_result = mysqli_stmt_execute($stmt_return);

        if (!$return_result) {
            mysqli_rollback($con);
            $_SESSION['status'] = "Failed to delete related return records.";
            $_SESSION['status_code'] = "error";
            header("Location: book_views.php?tab=copies");
            exit(0);
        }

        // Delete the book
        $delete_book_query = "DELETE FROM book WHERE accession_number = ?";
        $stmt_book = mysqli_prepare($con, $delete_book_query);
        mysqli_stmt_bind_param($stmt_book, "s", $accession_number);
        $book_result = mysqli_stmt_execute($stmt_book);

        if (!$book_result || mysqli_stmt_affected_rows($stmt_book) <= 0) {
            mysqli_rollback($con);
            $_SESSION['status'] = "Failed to delete the book.";
            $_SESSION['status_code'] = "error";
            header("Location: book_views.php?tab=copies");
            exit(0);
        }

        // Commit transaction
        mysqli_commit($con);
        $_SESSION['status'] = "Book accession number '$accession_number' deleted successfully.";
        $_SESSION['status_code'] = "success";
        header("Location: book_views.php?title=" . urlencode(encryptor('encrypt', $title)) . 
               "&copyright_date=" . urlencode(encryptor('encrypt', $copyright_date)) . 
               "&author=" . urlencode(encryptor('encrypt', $author)) . 
               "&isbn=" . urlencode(encryptor('encrypt', $isbn)) . "&tab=copies");
        exit(0);
    } else {
        // No book found
        $_SESSION['status'] = "No book found with accession number '$accession_number'.";
        $_SESSION['status_code'] = "warning";
        header("Location: book_views.php?tab=copies");
        exit(0);
    }
}

// Update Book
if (isset($_POST['update_book'])) {
    $book_title = mysqli_real_escape_string($con, $_POST['title']);
    $old_book_title = mysqli_real_escape_string($con, $_POST['old_title']); // Assuming you have an input field for old title
    $old_copyright_date = mysqli_real_escape_string($con, $_POST['old_copyright_date']);

    $title = mysqli_real_escape_string($con, $_POST['title']);
    $author = mysqli_real_escape_string($con, $_POST['author']);
    $copyright_date = mysqli_real_escape_string($con, $_POST['copyright_date']);
    $publisher = mysqli_real_escape_string($con, $_POST['publisher']);
    $isbn = mysqli_real_escape_string($con, $_POST['isbn']);
    $place_publication = mysqli_real_escape_string($con, $_POST['place_publication']);
    $call_number = mysqli_real_escape_string($con, $_POST['call_number']);
    $subject = mysqli_real_escape_string($con, $_POST['subject']);
    $subject1 = mysqli_real_escape_string($con, $_POST['subject1']);
    $subject2 = mysqli_real_escape_string($con, $_POST['subject2']);

    $old_book_filename = $_POST['old_book_image'];
    $book_image = $_FILES['book_image']['name'];
    $update_book_filename = "";

    if ($book_image != NULL) {
        // Rename the Image
        $book_extension = pathinfo($book_image, PATHINFO_EXTENSION);
        $book_filename = time() . '.' . $book_extension;
        $update_book_filename = $book_filename;
    } else {
        $update_book_filename = $old_book_filename;
    }

    // Update query
    $query = "UPDATE book SET title='$title', author='$author', copyright_date='$copyright_date', 
              publisher='$publisher', isbn='$isbn', place_publication='$place_publication', 
              call_number='$call_number', book_image='$update_book_filename',
              subject='$subject', subject1='$subject1', subject2='$subject2'
              WHERE title = '$old_book_title' AND copyright_date='$old_copyright_date'"; // Update based on old title

    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        // If image is updated, delete old image and upload new one
        if ($book_image != NULL) {
            if (file_exists('../uploads/books_img/' . $old_book_filename)) {
                unlink("../uploads/books_img/" . $old_book_filename);
            }
            move_uploaded_file($_FILES['book_image']['tmp_name'], '../uploads/books_img/' . $book_filename);
        }

        $_SESSION['status'] = 'Book Updated successfully';
        $_SESSION['status_code'] = "success";
        header("Location: books.php");
        exit(0);
    } else {
        $_SESSION['status'] = 'Book not Updated';
        $_SESSION['status_code'] = "error";
        header("Location: books.php");
        exit(0);
    }
}

// Update Book Accession Number
if (isset($_POST['update_accession_number'])) {
    $old_accession_number = mysqli_real_escape_string($con, $_POST['old_accession_number']);
    $new_accession_number = mysqli_real_escape_string($con, $_POST['accession_number']);
    $category_id = mysqli_real_escape_string($con, $_POST['category_id']);
    $remarks = mysqli_real_escape_string($con, $_POST['remarks']);

    $select_query = "SELECT title,copyright_date FROM book WHERE accession_number = '$old_accession_number'";
    $select_query_run = mysqli_query($con, $select_query);

    if (mysqli_num_rows($select_query_run) > 0) {
        $row = mysqli_fetch_assoc($select_query_run);
        $title = $row['title'];
        $copyright_date = $row['copyright_date'];

        // Generate the barcode
        $barcode = 'MCC-LRC' . $new_accession_number;

        // Check if the new accession number already exists
        $check_query = "SELECT * FROM book WHERE accession_number = '$new_accession_number' AND accession_number != '$old_accession_number'";
        $check_result = mysqli_query($con, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $_SESSION['status'] = 'This accession number already exists.';
            $_SESSION['status_code'] = 'error';
        } else {
            // Update the book details, including the barcode
            $update_query = "UPDATE book SET accession_number = '$new_accession_number', category_id = '$category_id', barcode = '$barcode', status = '$remarks' WHERE accession_number = '$old_accession_number'";
            if (mysqli_query($con, $update_query)) {
                $_SESSION['status'] = 'Book updated successfully.';
                $_SESSION['status_code'] = 'success';
            } else {
                $_SESSION['status'] = 'Failed to update book.';
                $_SESSION['status_code'] = 'error';
            }
        }

        header("Location: book_views.php?title=" . urlencode(encryptor('encrypt',$title)) . "&copyright_date=" . urlencode(encryptor('encrypt',$copyright_date)) . "&author=" . urlencode(encryptor('encrypt',$author)) . "&isbn=" . urlencode(encryptor('encrypt',$isbn)) . "&tab=copies");
        exit();
    }
}

// Add Book
if (isset($_POST['add_book'])) {
    // Collect form data
    $title = $_POST['title'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];
    $publisher = $_POST['publisher'];
    $copyright_date = intval($_POST['copyright_date']); // Ensure it's an integer
    $place_publication = $_POST['place_publication'];
    $call_number = $_POST['call_number'];
    $category_id = $_POST['lrc_location']; // Updated to category_id
    $existing_image = $_POST['existing_image'];
    $copy = intval($_POST['copy']); // Number of copies to add
    $subject = $_POST['subject'];
    $subject1 = $_POST['subject1'];
    $subject2 = $_POST['subject2'];

    // Validate the copyright_date
    $currentYear = date('Y');
    if ($copyright_date > $currentYear) {
        $_SESSION['status'] = "Year cannot be greater than the current year.";
        $_SESSION['status_code'] = "warning";
        header("Location: book_add.php");
        exit(0);
    }

    // Handle the uploaded image
    $book_image = '';
    if (isset($_FILES['book_image']) && $_FILES['book_image']['error'] === UPLOAD_ERR_OK) {
        $image_tmp_name = $_FILES['book_image']['tmp_name'];
        $image_name = $_FILES['book_image']['name'];
        $image_size = $_FILES['book_image']['size'];
        $image_error = $_FILES['book_image']['error'];
        $image_type = $_FILES['book_image']['type'];

        $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($image_ext, $allowed_ext)) {
            if ($image_error === 0) {
                if ($image_size < 5000000) { // Limit to 5MB
                    $new_image_name = uniqid('', true) . "." . $image_ext;
                    $image_upload_path = '../uploads/books_img/' . $new_image_name;
                    move_uploaded_file($image_tmp_name, $image_upload_path);
                    $book_image = $new_image_name;
                } else {
                    $_SESSION['status'] = "Your file is too large.";
                    $_SESSION['status_code'] = "warning";
                    header("Location: book_add.php");
                    exit(0);
                }
            } else {
                $_SESSION['status'] = "There was an error uploading your file.";
                $_SESSION['status_code'] = "error";
                header("Location: book_add.php");
                exit(0);
            }
        } else {
            $_SESSION['status'] = "You cannot upload files of this type.";
            $_SESSION['status_code'] = "warning";
            header("Location: book_add.php");
            exit(0);
        }
    } else {
        // Use existing image if no new image is uploaded
        $book_image = $existing_image;
    }

    // Prepare the SQL query to check for existing accession numbers
    $check_query = "SELECT COUNT(*) FROM book WHERE accession_number = ?";
    $check_stmt = mysqli_prepare($con, $check_query);

    if ($check_stmt) {
        $pre = "MCC"; // Prefix for the barcode
        $suf = "LRC"; // Suffix for the barcode

        for ($i = 1; $i <= $copy; $i++) {
            $accession_number = $_POST['accession_number_' . $i];
            
            // Bind the accession number parameter and execute the statement
            mysqli_stmt_bind_param($check_stmt, "s", $accession_number);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_bind_result($check_stmt, $count);
            mysqli_stmt_fetch($check_stmt);

            if ($count > 0) {
                $_SESSION['status'] = "Accession number " . $accession_number . " already exists.";
                $_SESSION['status_code'] = "error";
                header("Location: book_add.php");
                exit(0);
            }
        }

        mysqli_stmt_close($check_stmt);
    } else {
        $_SESSION['status'] = "Error preparing the statement: " . mysqli_error($con);
        $_SESSION['status_code'] = "error";
        header("Location: book_add.php");
        exit(0);
    }

    // Prepare the SQL query to insert new books
    $insert_query = "INSERT INTO book (title, author, isbn, publisher, copyright_date, place_publication, call_number, category_id, book_image, accession_number, barcode, subject, subject1, subject2, date_added, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'Available')";
    $insert_stmt = mysqli_prepare($con, $insert_query);

    if ($insert_stmt) {
        for ($i = 1; $i <= $copy; $i++) {
            $accession_number = $_POST['accession_number_' . $i];
            $barcode = $pre . '-' . $suf . $accession_number;
            // $barcodeImagePath = '../uploads/barcodes'.$barcode.".png";
            // $barcodeImage = $barcode.".png";

            // $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
            // file_put_contents($barcodeImagePath, $generator->getBarcode($barcode, $generator::TYPE_CODE_128));
            
            // Bind parameters and execute the statement
            mysqli_stmt_bind_param($insert_stmt, "ssssssssssssss", $title, $author, $isbn, $publisher, $copyright_date, $place_publication, $call_number, $category_id, $book_image, $accession_number, $barcode, $subject, $subject1, $subject2);
            mysqli_stmt_execute($insert_stmt);
        }

        mysqli_stmt_close($insert_stmt);

        $_SESSION['status'] = "Book(s) added successfully.";
        $_SESSION['status_code'] = "success";
        header("Location: books.php");
        exit(0);
    } else {
        $_SESSION['status'] = "Error preparing the statement: " . mysqli_error($con);
        $_SESSION['status_code'] = "error";
        header("Location: book_add.php");
        exit(0);
    }
}
?>
