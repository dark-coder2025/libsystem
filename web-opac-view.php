<?php
ob_start(); // Start output buffering
include('includes/header.php');
include('includes/navbar.php');
include('admin/config/dbcon.php');
include('includes/url.php');
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card mt-4">
                <div class="card-header">
                    <a href="web-opac" class="btn btn-primary">Back</a>
                </div>
                <div class="card-body">
                    <section class="section profile">
                        <div class="row">
                            <?php
                            if (isset($_GET['a']) || isset($_GET['b']) || isset($_GET['c']) || isset($_GET['d']) || isset($_GET['e'])) {
                                $book_id = filter_var(encryptor('decrypt', $_GET['a']), FILTER_VALIDATE_INT);
                                $book_title = encryptor('decrypt', $_GET['b']);
                                $author = encryptor('decrypt', $_GET['c']);
                                $copyright_date = encryptor('decrypt', $_GET['d']);
                                $isbn = encryptor('decrypt', $_GET['e']);

                                $query = $con->prepare("SELECT 
                                                           book.*, 
                                                           COUNT(book.accession_number) AS copy_count, 
                                                           SUM(CASE WHEN book.status = 'available' THEN 1 ELSE 0 END) AS available_count
                                                      FROM book 
                                                      WHERE title = ? AND author = ? AND copyright_date = ? AND isbn = ?
                                                      GROUP BY title, author, copyright_date, isbn
                                                      ORDER BY title, author, copyright_date, isbn DESC");
                                $query->bind_param('ssss', $book_title, $author, $copyright_date, $isbn);
                                $query->execute();
                                $result = $query->get_result();

                                if ($result->num_rows > 0) {
                                    $book = $result->fetch_assoc();
                                    $unavailable_count = $book['copy_count'] - $book['available_count'];
                                    ?>
                                    <div class="col-xl-4">
                                        <div class="card">
                                            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                                                <img src="uploads/books_img/<?php echo htmlspecialchars($book['book_image'] ? $book['book_image'] : 'book_image.jpg'); ?>" height="300px" alt="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-8">
                                        <div class="card">
                                            <div class="card-body pt-3">
                                                <ul class="nav nav-tabs nav-tabs-bordered border-info">
                                                    <li class="nav-item">
                                                        <button class="nav-link active text-info border-info fw-semibold" data-bs-toggle="tab" data-bs-target="#profile-overview">Book Details</button>
                                                    </li>
                                                </ul>
                                                <div class="tab-content pt-2">
                                                    <div class="tab-pane fade show active profile-overview" id="profile-overview">
                                                        <div class="row mt-3">
                                                            <div class="col-lg-3 col-md-4 label fw-semibold">Title</div>
                                                            <div class="col-lg-9 col-md-8"><?= htmlspecialchars($book['title']) ?></div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-lg-3 col-md-4 label fw-semibold">Author</div>
                                                            <div class="col-lg-9 col-md-8"><?= htmlspecialchars($book['author']) ?></div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-lg-3 col-md-4 label fw-semibold">Copyright Date</div>
                                                            <div class="col-lg-9 col-md-8"><?= htmlspecialchars($book['copyright_date']) ?></div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-lg-3 col-md-4 label fw-semibold">Publisher</div>
                                                            <div class="col-lg-9 col-md-8"><?= htmlspecialchars($book['publisher']) ?></div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-lg-3 col-md-4 label fw-semibold">Place of Publication</div>
                                                            <div class="col-lg-9 col-md-8"><?= htmlspecialchars($book['place_publication']) ?></div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-lg-3 col-md-4 label fw-semibold">ISBN</div>
                                                            <div class="col-lg-9 col-md-8"><?= htmlspecialchars($book['isbn']) ?></div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-lg-3 col-md-4 label fw-semibold">Call Number</div>
                                                            <div class="col-lg-9 col-md-8"><?= htmlspecialchars($book['call_number']) ?></div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-lg-3 col-md-4 label fw-semibold">Page</div>
                                                            <div class="col-lg-9 col-md-8"><?= htmlspecialchars($book['page']) ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card mt-2">
                                            <div class="card-body">
                                                <div class="row mt-2">
                                                    <div class="col-lg-3 col-md-4 label fw-semibold">Books Available</div>
                                                    <div class="col-lg-6 col-md-4">
                                                        <?= htmlspecialchars($book['available_count']) ?> of <?= htmlspecialchars($book['copy_count']) ?> available
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    echo "No Book details found";
                                }
                            }
                            ?>
                        </div>
                    </section>
                </div>
                <div id="searchresult" class="text-center"></div>
            </div>
        </div>
    </div>
</div>

<?php
include('includes/script.php');
include('message.php'); 
?>

<?php
ob_end_flush(); // End output buffering and flush output
?>
