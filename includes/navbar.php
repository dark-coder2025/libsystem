<?php 
// Ensure session cookie expires when the browser closes
ini_set('session.cookie_lifetime', 0);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
session_start(); 
include('admin/config/dbcon.php');
?>
<nav class="navbar navbar-expand-lg" style="background: #0096FF;">
     <?php  $page = substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], "/")+ 1); ?>
     <div class="container-fluid mx-5">
     <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
               data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
               aria-label="Toggle navigation">
               <span class="navbar-toggler-icon"></span>
          </button>
          <img src="./images/mcc-lrc.png" alt="logo" class=" mx-2" height="40px" width="40px" />
          <a class="navbar-brand text-white fw-bold fs-5" href="#">MCC-LRC</a>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
               <ul class="navbar-nav ms-auto mb-2 mb-lg-0 nav nav-pills nav-justified">
                    <?php if(isset($_SESSION['auth_stud'])) :?>
                    <li class="nav-item">
                         <a class="nav-link text-white <?=$page == 'index.php' || $page == 'book_details' ? 'active': '' ?> fw-semibold"
                              href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                         <a class="nav-link text-white <?=$page == 'ebook.php' ? 'active': '' ?> fw-semibold"
                              href="ebook.php">Ebooks</a>
                    </li>

                    <li class="nav-item">
                         <a class="nav-link text-white <?=$page == 'notification.php' ? 'active': '' ?> fw-semibold"
                              href="notification.php">Notification</a>
                    </li>




                    <?php else :?>
                    <li class="nav-item">
                         <a class="nav-link  text-white <?=$page == '.' ? 'active': '' ?> fw-semibold"
                              href=".">Home</a>
                    </li>
                    <li class="nav-item">
                         <a class="nav-link  text-white <?=$page == 'web-opac.php' ? 'active': '' ?> fw-semibold"
                              href="web-opac.php">OPAC</a>
                    </li>
                    <li class="nav-item">
                         <a class="nav-link  text-white <?=$page == 'about.php' ? 'active': '' ?> fw-semibold"
                              href="about.php">About</a>
                    </li>
                    <?php endif; ?>
                    <?php if(isset($_SESSION['auth_stud'])) :?>
                    <li class="nav-item dropdown">
                         <a class="nav-link  dropdown-toggle text-white fw-semibold" href="#" role="button"
                              data-bs-toggle="dropdown" aria-expanded="false">
                              <span><?= $_SESSION['auth_stud']['stud_name']; ?></span>
                         </a>
                         <ul class="dropdown-menu">
                              <li><a class="dropdown-item" href="myprofile">
                                        <i class="bi bi-person"></i> My Profile</a></li>
                              <!-- <li><a class="dropdown-item" href="#"><i class="bi bi-gear"></i> Settings</a></li> -->
                              <li>
                                   <hr class="dropdown-divider">
                              </li>
                              <li><a class="dropdown-item" href="hold">
                                        <i class="bi bi-book"></i> Hold Books</a></li>
                              <li>
                                   <hr class="dropdown-divider">
                              </li>
                              <li>
                                   <form action="allcode.php" method="POST">
                                        <button type="submit" name="logout_btn" class="dropdown-item"><i
                                                  class="bi bi-box-arrow-right"></i> Logout</button>
                                   </form>
                              </li>
                         </ul>
                    </li>
                    <?php else :?>
                    <li class="nav-item">
                         <a href="login.php" class="nav-link text-white fw-semibold">Login</a>
                    </li>
                    <li class="nav-item">
                         <a href="ms_verify.php"
                              class="nav-link text-white bg-info px-3 fw-semibold rounded-pill">Signup</a>
                    </li>
                    <?php endif; ?>
               </ul>
          </div>
     </div>
</nav>