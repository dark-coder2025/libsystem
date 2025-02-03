<?php 
include('authentication.php');
include('./includes/header.php'); 
include('./includes/sidebar.php'); 
?>

<main id="main" class="main">
     <div class="pagetitle">
          <h1>Edit Book</h1>
          <nav>
               <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="books.php">Book Collection</a></li>
                    <li class="breadcrumb-item active">Edit Book</li>
               </ol>
          </nav>
     </div>
     <section class="section">
          <div class="row">
               <div class="col-lg-12">
                    <div class="card">
                         <div class="card-header d-flex justify-content-end">
                         </div>
                         <div class="card-body">
                              <?php
                              if(isset($_GET['title']) || isset($_GET['copyright_date']) || isset($_GET['author']) || isset($_GET['isbn']))
                              {
                                   $book_title = encryptor('decrypt',$_GET['title']);
                                   $copyright_date = encryptor('decrypt',$_GET['copyright_date']);
                                   $author = encryptor('decrypt',$_GET['author']);
                                   $isbn = encryptor('decrypt',$_GET['isbn']);

                                   $query = "SELECT * FROM book LEFT JOIN category ON book.category_id = category.category_id WHERE title='$book_title' AND copyright_date='$copyright_date' AND author='$author' AND isbn='$isbn'"; 
                                   $query_run = mysqli_query($con, $query);

                                   if(mysqli_num_rows($query_run) > 0)
                                   {
                                        $book = mysqli_fetch_array($query_run);
                              ?>
                              <form id="editBookForm" action="books_code.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                                   <div class="row d-flex justify-content-center mt-2">
                                        <div class="col-12 col-md-5">
                                             <div class="mb-2 input-group-sm">
                                                  <label for="">Title</label>
                                                  <input type="text" id="title" name="title" value="<?=$book['title'];?>" class="form-control">
                                             </div>
                                        </div>
                                        <div class="col-12 col-md-5">
                                             <div class="mb-2 input-group-sm">
                                                  <label for="">Author</label>
                                                  <input type="text" id="author" name="author" value="<?=$book['author'];?>" class="form-control">
                                             </div>
                                        </div>
                                   </div>
                                   <div class="row d-flex justify-content-center">
                                   <div class="col-12 col-md-5">
                                        <div class="mb-2 input-group-sm">
                                             <label for="copyright_date">Copyright Year</label>
                                             <input type="text" id="copyright_date"
                                                       name="copyright_date"
                                                       value="<?= $book['copyright_date']; ?>"
                                                       class="form-control"
                                                       autocomplete="off"
                                                       pattern="\d{4}"
                                                       required>
                                        </div>
                                        </div>
                                        <div class="col-12 col-md-5">
                                             <div class="mb-2 input-group-sm">
                                                  <label for="">Publisher</label>
                                                  <input type="text" id="publisher" name="publisher" value="<?=$book['publisher'];?>" class="form-control">
                                             </div>
                                        </div>
                                   </div>
                                   <div class="row d-flex justify-content-center">
                                        <div class="col-12 col-md-5">
                                             <div class="mb-2 input-group-sm">
                                                  <label for="">ISBN</label>
                                                  <input type="text" id="isbn" name="isbn" value="<?=$book['isbn'];?>" class="form-control">
                                             </div>
                                        </div>
                                        <div class="col-12 col-md-5">
                                             <div class="mb-2 input-group-sm">
                                                  <label for="">Place of Publication</label>
                                                  <input type="text" id="place_publication" name="place_publication" value="<?=$book['place_publication'];?>" class="form-control">
                                             </div>
                                        </div>
                                   </div>
                                   <div class="row d-flex justify-content-center">
                                        <input type="hidden" name="old_title" value="<?=$book['title']?>">
                                        <input type="hidden" name="old_copyright_date" value="<?=$book['copyright_date']?>">
                                        <div class="col-12 col-md-5">
                                             <div class="mb-2 input-group-sm">
                                                  <label for="">Call Number</label>
                                                  <input type="text" id="call_number" name="call_number" id="book_call_number_edit" value="<?=$book['call_number'];?>" class="form-control">
                                             </div>
                                        </div>
                                        <div class="col-12 col-md-5">
                                             <div class="mb-2 input-group-sm">
                                                  <label for="">Image</label>
                                                  <input type="hidden" name="old_book_image" value="<?=$book['book_image'];?>">
                                                  <input type="file" id="book_image_input" name="book_image" class="form-control" autocomplete="off" accept=".jpg,.jpeg,.png" onchange="validateImage(this)">
                                             </div>
                                        </div>
                                   </div>
                                   <div class="row d-flex justify-content-center">
                                        <div class="col-12 col-md-5">
                                             <div class="mb-2 input-group-sm">
                                                  <label for="">Page</label>
                                                  <input type="text" id="page" name="page" value="<?=$book['page'];?>" class="form-control">
                                             </div>
                                        </div>
                                        <div class="col-12 col-md-5">
                                             <div class="mb-2 input-group-sm">
                                                  <label for="">Price</label>
                                                  <input type="text" id="price" name="price" value="<?=$book['price'];?>" class="form-control">
                                             </div>
                                        </div>
                                   </div>
                                   <div class="row d-flex justify-content-center">
                                        <div class="col-12 col-md-5">
                                             <div class="mb-2 input-group-sm">
                                                  <label for="subject">Subject/s</label>
                                                  <input type="text" id="subject" name="subject" value="<?=$book['subject'];?>" class="form-control mb-2">
                                                  <input type="text" id="subject1" name="subject1" value="<?=$book['subject1'];?>" class="form-control mb-2">
                                                  <input type="text" id="subject2" name="subject2" value="<?=$book['subject2'];?>" class="form-control">
                                             </div>
                                        </div>
                                   </div>
                                   </div>
                                   <div class="card-footer d-flex justify-content-end">
                                        <div>
                                             <a href="books.php" class="btn btn-secondary">Cancel</a>
                                             <button type="submit" name="update_book" class="btn btn-primary">Update Book</button>
                                        </div>
                                   </div>
                              </form>
                              <?php 
                                   }
                                   else
                                   {
                                        echo "No such book found";
                                   }
                              }
                              ?>
                         </div>
                    </div>
               </div>
          </div>
     </section>
</main>

<!-- Include SweetAlert CSS and JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.0/dist/sweetalert2.all.min.js"></script>

<?php 
include('./includes/footer.php');
include('./includes/script.php');
include('../message.php');
?>

<script>
    $(document).ready(function() {
    // Restrict input to numeric values only
    $('#copyright_date').on('keypress', function(event) {
        var key = String.fromCharCode(event.which);
        if (!/[0-9]/.test(key)) {
            event.preventDefault();
        }
    });

    // Ensure the year is not greater than the current year
    $('#copyright_date').on('change', function() {
        var inputYear = parseInt($(this).val(), 10);
        var currentYear = new Date().getFullYear();
        if (inputYear > currentYear) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Year',
                text: 'Year cannot be greater than the current year.',
                confirmButtonText: 'OK'
            });
            $(this).val(''); // Clear the input
        }
    });

    // Validate book image file type
    $('#book_image_input').on('change', function() {
        const file = this.files[0];
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (file && !allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid File Type',
                text: 'Only JPG, JPEG, and PNG files are allowed.',
                confirmButtonText: 'OK'
            });
            $(this).val(''); // Clear the input
            $('#book_image_name').val(''); // Clear the filename input
        } else {
            $('#book_image_name').val(file ? file.name : '');
        }
    });
});

function validateNameInput(inputField) {
        const value = inputField.value;
        const xssPattern = /<[^>]*>/; // Regex pattern to detect HTML tags

        // Check for XSS tags
        if (xssPattern.test(value)) {
            Swal.fire({
                title: 'Invalid Input!',
                text: 'Field cannot contain HTML tags.',
                icon: 'error',
                confirmButtonText: 'Okay'
            });
            inputField.value = ''; // Clear the input field
            inputField.focus(); // Refocus on the input field
        }
    }

    document.getElementById('title').addEventListener('input', function() {
        validateNameInput(this);
    });
    document.getElementById('author').addEventListener('input', function() {
        validateNameInput(this);
    });
    document.getElementById('publisher').addEventListener('input', function() {
        validateNameInput(this);
    });
    document.getElementById('isbn').addEventListener('input', function() {
        validateNameInput(this);
    });
    document.getElementById('place_publication').addEventListener('input', function() {
        validateNameInput(this);
    });
    document.getElementById('call_number').addEventListener('input', function() {
        validateNameInput(this);
    });
    document.getElementById('page').addEventListener('input', function() {
        validateNameInput(this);
    });
    document.getElementById('price').addEventListener('input', function() {
        validateNameInput(this);
    });
    document.getElementById('subject').addEventListener('input', function() {
        validateNameInput(this);
    });
</script>

<script>
     document.getElementById('title').addEventListener('input', function () {
        var titleInput = this.value.trim();
        
        var dangerousCharsPattern = /[<>\"\']/;
        
        if (titleInput === "") {
            this.setCustomValidity('Title name cannot be empty or just spaces.');
        } else if (this.value !== titleInput) {
            this.setCustomValidity('Title name cannot start with a space.');
        } else if (dangerousCharsPattern.test(titleInput)) {
            this.setCustomValidity('Title name cannot contain HTML special characters like <, >, ", \'.');
        } else {
            this.setCustomValidity('');
        }
        
        var isValid = titleInput !== "" && this.value === titleInput && !dangerousCharsPattern.test(titleInput);
        this.classList.toggle('is-invalid', !isValid);
    });

    document.getElementById('author').addEventListener('input', function () {
        var authorInput = this.value.trim();
        
        // Regex to match any character except '<' and '>'
        var validPattern = /^[^<>]+$/;
        
        if (this.value !== authorInput) {
            this.setCustomValidity('Name cannot start with a space.');
        } else if (validPattern.test(authorInput)) {
            this.setCustomValidity('');
        } else {
            this.setCustomValidity('Please enter a valid name without "<" or ">" characters and no leading/trailing spaces.');
        }
        
        var isValid = validPattern.test(authorInput) && this.value === authorInput;
        this.classList.toggle('is-invalid', !isValid);
    });


    document.getElementById('publisher').addEventListener('input', function () {
        var publisherInput = this.value.trim();
        
        var dangerousCharsPattern = /[<>\"\']/;
        
        if (publisherInput === "") {
            this.setCustomValidity('Publisher name cannot be empty or just spaces.');
        } else if (this.value !== publisherInput) {
            this.setCustomValidity('Publisher name cannot start with a space.');
        } else if (dangerousCharsPattern.test(publisherInput)) {
            this.setCustomValidity('Publisher name cannot contain HTML special characters like <, >, ", \'.');
        } else {
            this.setCustomValidity('');
        }
        
        var isValid = publisherInput !== "" && this.value === publisherInput && !dangerousCharsPattern.test(publisherInput);
        this.classList.toggle('is-invalid', !isValid);
    });

    document.getElementById('isbn').addEventListener('input', function () {
        var isbnInput = this.value.trim(); 
        
        var isbnPattern = /^[0-9-]+$/;
        
        if (this.value !== isbnInput) {
            this.setCustomValidity('ISBN cannot start with a space.');
        } else if (isbnPattern.test(isbnInput)) {
            this.setCustomValidity('');
        } else {
            this.setCustomValidity('Please enter a valid ISBN with only numbers and dashes.');
        }
        
        var isValid = isbnPattern.test(isbnInput) && this.value === isbnInput;
        this.classList.toggle('is-invalid', !isValid);
    });

    document.getElementById('place_publication').addEventListener('input', function () {
        var placepubInput = this.value.trim();
        
        var dangerousCharsPattern = /[<>\"\']/;
        
        if (placepubInput === "") {
            this.setCustomValidity('Place Publication name cannot be empty or just spaces.');
        } else if (this.value !== placepubInput) {
            this.setCustomValidity('Place Publication name cannot start with a space.');
        } else if (dangerousCharsPattern.test(placepubInput)) {
            this.setCustomValidity('Place Publication name cannot contain HTML special characters like <, >, ", \'.');
        } else {
            this.setCustomValidity('');
        }
        
        var isValid = placepubInput !== "" && this.value === placepubInput && !dangerousCharsPattern.test(placepubInput);
        this.classList.toggle('is-invalid', !isValid);
    });

    document.getElementById('subject').addEventListener('input', function () {
        var subjectInput = this.value.trim();
        
        var dangerousCharsPattern = /[<>\"\']/;
        
        if (this.value !== subjectInput) {
            this.setCustomValidity('Subject name cannot start with a space.');
        } else if (dangerousCharsPattern.test(subjectInput)) {
            this.setCustomValidity('Subject name cannot contain HTML special characters like <, >, ", \'.');
        } else {
            this.setCustomValidity('');
        }
        
        var isValid = subjectInput !== "" && this.value === subjectInput && !dangerousCharsPattern.test(subjectInput);
        this.classList.toggle('is-invalid', !isValid);
    });

    document.getElementById('subject1').addEventListener('input', function () {
        var subject1Input = this.value.trim();
        
        var dangerousCharsPattern = /[<>\"\']/;
        
        if (this.value !== subject1Input) {
            this.setCustomValidity('Subject name cannot start with a space.');
        } else if (dangerousCharsPattern.test(subject1Input)) {
            this.setCustomValidity('Subject name cannot contain HTML special characters like <, >, ", \'.');
        } else {
            this.setCustomValidity('');
        }
        
        var isValid = subject1Input !== "" && this.value === subject1Input && !dangerousCharsPattern.test(subject1Input);
        this.classList.toggle('is-invalid', !isValid);
    });

    document.getElementById('subject2').addEventListener('input', function () {
        var subject2Input = this.value.trim();
        
        var dangerousCharsPattern = /[<>\"\']/;
        
        if (this.value !== subject2Input) {
            this.setCustomValidity('Subject name cannot start with a space.');
        } else if (dangerousCharsPattern.test(subject2Input)) {
            this.setCustomValidity('Subject name cannot contain HTML special characters like <, >, ", \'.');
        } else {
            this.setCustomValidity('');
        }
        
        var isValid = subject2Input !== "" && this.value === subject2Input && !dangerousCharsPattern.test(subject2Input);
        this.classList.toggle('is-invalid', !isValid);
    });

    document.getElementById('call_number').addEventListener('input', function () {
        var callnumInput = this.value.trim();
        
        var dangerousCharsPattern = /[<>\"\']/;
        
        if (callnumInput === "") {
            this.setCustomValidity('Call number name cannot be empty or just spaces.');
        } else if (this.value !== callnumInput) {
            this.setCustomValidity('Call number name cannot start with a space.');
        } else if (dangerousCharsPattern.test(callnumInput)) {
            this.setCustomValidity('Call number name cannot contain HTML special characters like <, >, ", \'.');
        } else {
            this.setCustomValidity('');
        }
        
        var isValid = callnumInput !== "" && this.value === callnumInput && !dangerousCharsPattern.test(callnumInput);
        this.classList.toggle('is-invalid', !isValid);
    });

    document.getElementById('copyright_year').addEventListener('input', function () {
        var currentYear = new Date().getFullYear();  // Dynamically get the current year
        var copyrightYearInput = this.value.trim();
        
        var yearPattern = /^\d{4}$/;

        // Check if the input is a valid 4-digit year
        if (!yearPattern.test(copyrightYearInput)) {
            this.setCustomValidity('Please enter a valid 4-digit year.');
        } 
        // Check if the input year is greater than the current year
        else if (parseInt(copyrightYearInput) > currentYear) {
            this.setCustomValidity('Copyright year cannot be in the future.');
        } 
        // Valid case: years up to the current year
        else {
            this.setCustomValidity('');
        }

        var isValid = yearPattern.test(copyrightYearInput) && parseInt(copyrightYearInput) <= currentYear;
        this.classList.toggle('is-invalid', !isValid);
    });

    document.getElementById('book_image_input').addEventListener('change', function () {
        var file = this.files[0];
        var isValid = true;

        if (file) {
            var allowedExtensions = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!allowedExtensions.includes(file.type)) {
                isValid = false;
                this.setCustomValidity('Please upload a valid image file (JPEG, JPG, PNG).');
            } else {
                this.setCustomValidity('');
            }
        }

        this.classList.toggle('is-invalid', !isValid);
    });
</script>
