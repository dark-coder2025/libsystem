<?php 
include('includes/header.php');
include('includes/navbar.php');
?>

<div class="jumbotron h-50" style="background-color: #1A3D61; background-image: linear-gradient(to right, #1A3D61, #0D4C92);">
    <div id="carouselExampleDark" class="carousel carousel-dark slide" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="10000">
                <img src="assets/img/mccfront.jpg" class="d-block w-100 h-100 rounded-3 shadow-lg" alt="Library Entrance">
            </div>
            <div class="carousel-item">
                <img src="assets/img/slide2.jpg" class="d-block w-100 h-100 rounded-3 shadow-lg" alt="Library Interior">
            </div>
            <div class="carousel-item">
                <img src="assets/img/slide3.jpg" class="d-block w-100 h-100 rounded-3 shadow-lg" alt="Bookshelf View">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>

<!-- Library Services Section -->
<div class="container bg-light mt-4 rounded-3 shadow-lg">
    <h3 class="fs-3 text-center text-md-start text-dark font-italic">
        <a href="services.php" class="text-decoration-none">Library Services</a>
    </h3>
    <h4 class="text-center fs-4 fs-md-2 mb-4 text-muted">Operating Hours at MCCLRC</h4>
    <div class="row align-items-center">
        <!-- Text Column -->
        <div class="col-12 col-md-6 p-3 p-md-5 text-center text-md-start">
            <h4 class="fs-5 fs-md-3 text-primary">Monday - Saturday:</h4>
            <h4 class="fs-6 fs-md-4 text-secondary">8:00 AM - 5:00 PM (No Noon Break)</h4>
        </div>
        <!-- Image Column -->
        <div class="col-12 col-md-6 text-center">
            <img src="assets/img/A.gif" class="img-fluid book-style shadow-lg" alt="Library GIF">
        </div>
    </div>
</div>

<!-- Footer -->
<div class="jumbotron mt-5" style="background-color: #1A3D61;">
    <footer class="text-center text-lg-start text-white">
        <div class="container p-4">
            <section class="d-flex justify-content-center">
                <!-- Social Media Links -->
                <a href="https://www.facebook.com/MCCLRC" class="btn btn-outline-light btn-floating m-1" role="button"><i class="bi bi-facebook"></i></a>
                <a href="https://www.youtube.com/watch?v=bIzChSbj0OU" class="btn btn-outline-light btn-floating m-1" role="button"><i class="bi bi-youtube"></i></a>
            </section>

            <div class="row mt-3">
                <div class="col-md-12 text-center">
                    <p class="font-weight-bold text-white mb-0">Madridejos Community College 2.0</p>
                </div>
            </div>
        </div>
    </footer>
</div>

<!-- Chat Icon -->
<a href="https://www.facebook.com/MCCLRC" class="chat-icon" target="_blank" title="Chat with us on Facebook">
    <i class="bi bi-chat-dots-fill"></i>
</a>

<style>
    /* Chat Icon Style */
    .chat-icon {
        position: fixed;
        bottom: 20px;
        right: 20px;
        color: white; /* Icon color */
        font-size: 45px; /* Icon size */
        text-shadow: 0px 0px 10px rgb(6, 106, 221); /* Glowing text shadow */
        z-index: 1000; /* Keeps it on top */
        transition: transform 0.3s ease, text-shadow 0.3s ease;
        text-decoration: none; /* Remove underline */
    }

    .chat-icon:hover {
        transform: scale(1.2); /* Slightly enlarge on hover */
        text-shadow: 0px 0px 15px rgba(13, 76, 146, 0.8); /* Stronger glow on hover */
    }

    /* Text & Heading Styling */
    h3, h4 {
        font-family: 'Georgia', serif;
    }

    /* Carousel Image Styling */
    .carousel-item img {
        object-fit: cover;
        border-radius: 15px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    /* Container Styling */
    .container.bg-light {
        border-radius: 15px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    /* Book-style Image */
    .book-style {
        width: 100%; /* Full width of its container */
        max-width: 300px; /* Adjust this to control the max width of the 'book' */
        height: auto;
        border: 10px solid #5A3D36; /* Book edge color, brown to resemble a book cover */
        border-radius: 10px; /* Slightly rounded corners */
        box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.2); /* Shadow for depth */
        position: relative;
        transition: transform 0.3s ease-in-out; /* Smooth transition for hover effect */
    }

    .book-style:hover {
        transform: rotateY(10deg); /* Slight 3D rotation effect on hover */
        box-shadow: 10px 10px 30px rgba(0, 0, 0, 0.3); /* Stronger shadow on hover */
    }

    /* Footer Styling */
    footer {
        background-color: #1A3D61;
    }

    footer .btn {
        background-color: #0D4C92;
    }

    footer p {
        font-size: 1rem;
    }
</style>

<?php 
include('includes/script.php');
include('message.php'); 
?>
