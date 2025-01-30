<?php // include('authentication.php'); ?>
<header id="header" class="header fixed-top d-flex align-items-center">
    <!-- Logo -->
    <div class="d-flex align-items-center">
        <a href="." class="logo d-flex align-items-center">
            <img src="./images/mcc-lrc.png" alt="logo" class="mx-2" />
            <span class="d-none d-lg-block mx-2">MCC <span class="text-info d-block fs-6">Learning Resource Center</span></span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>

    <?php 
    if (isset($_SESSION['auth_admin']['admin_id'])) {
        $id_session = $_SESSION['auth_admin']['admin_id'];
    }
    ?>

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">
            <li class="nav-item dropdown me-3">
                <a class="nav-link nav-icon fs-4" href="#" data-bs-toggle="dropdown">
                    <i class="bi bi-bell"></i>
                    <?php
                    $query = "SELECT COUNT(DISTINCT CONCAT(h.user_id, '-', h.faculty_id)) AS total_borrowers
                            FROM holds h
                            WHERE h.hold_status = 'Hold'";
                    $query_run = mysqli_query($con, $query);
                    $total_borrowers = $query_run ? mysqli_fetch_assoc($query_run)['total_borrowers'] : 0;

                    // Get the pending count
                    $user_sql = "SELECT COUNT(*) AS pending_count FROM user WHERE status = 'pending'";
                    $faculty_sql = "SELECT COUNT(*) AS pending_count FROM faculty WHERE status = 'pending'";

                    $user_result = mysqli_query($con, $user_sql);
                    $faculty_result = mysqli_query($con, $faculty_sql);

                    $userCount = $user_result ? mysqli_fetch_assoc($user_result)['pending_count'] : 0;
                    $facultyCount = $faculty_result ? mysqli_fetch_assoc($faculty_result)['pending_count'] : 0;

                    $pendingCount = $userCount + $facultyCount;

                    echo '<span class="badge bg-danger badge-number">'.($total_borrowers + $pendingCount).'</span>';
                    ?>
                </a>

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications scrollable-dropdown">
                    <li class="dropdown-header">
                        You have <?= ($total_borrowers + $pendingCount) ?> notifications
                    </li>

                    <?php 
                    // Notifications for holds
                    $query_notif = "SELECT 
                                        h.user_id, 
                                        u.firstname AS user_firstname, 
                                        u.lastname AS user_lastname, 
                                        h.faculty_id, 
                                        f.firstname AS faculty_firstname, 
                                        f.lastname AS faculty_lastname,
                                        COUNT(h.hold_id) AS num_hold_books
                                    FROM holds h
                                    LEFT JOIN user u ON u.user_id = h.user_id
                                    LEFT JOIN faculty f ON f.faculty_id = h.faculty_id
                                    WHERE h.hold_status = 'Hold'
                                    GROUP BY h.user_id, h.faculty_id
                                    ORDER BY h.hold_id DESC LIMIT 3";

                    $query_run = mysqli_query($con, $query_notif);

                    if (mysqli_num_rows($query_run) > 0) {
                        while ($holdlist = mysqli_fetch_assoc($query_run)) {
                            $name = !empty($holdlist['user_id']) ? $holdlist['user_firstname'].' '.$holdlist['user_lastname'] : $holdlist['faculty_firstname'].' '.$holdlist['faculty_lastname'];
                            ?>
                            <li>
                                <hr class="dropdown-divider" />
                            </li>
                            <li class="notification-item">
                                <a href="hold_list.php" style="text-decoration:none;">
                                    <div>
                                        <h4><?= htmlspecialchars($name); ?></h4>
                                        <p>hold <span><?= htmlspecialchars($holdlist['num_hold_books']); ?></span> book(s).</p>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider" />
                            </li>
                            <?php
                        }
                    }

                    // Notifications for pending users and faculty
                    if ($userCount > 0) {
                    ?>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li class="notification-item">
                        <a href="user_student_approval.php" style="text-decoration:none;font-size:13px;margin-left:10px;">
                            <div>
                                <h4>Pending Approvals</h4>
                                <p>You have <span><?= htmlspecialchars($userCount); ?></span> pending student approval(s).</p>
                            </div>
                        </a>
                    </li>
                    <?php
                    }

                    if ($facultyCount > 0) {
                    ?>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li class="notification-item">
                        <a href="user_faculty_approval.php" style="text-decoration:none;font-size:13px;margin-left:10px;">
                            <div>
                                <h4>Pending Faculty Approvals</h4>
                                <p>You have <span><?= htmlspecialchars($facultyCount); ?></span> pending faculty approval(s).</p>
                            </div>
                        </a>
                    </li>
                    <?php
                    }
                    ?>
                </ul>
            </li>

            <li class="nav-item dropdown pe-3">
                <?php
                $query = "SELECT * FROM admin WHERE admin_id = '$id_session'";
                $query_run = mysqli_query($con, $query);
                $row = mysqli_fetch_array($query_run);
                ?>
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="javascript:;" data-bs-toggle="dropdown">
                    <img src="<?= $row['admin_image'] ? '../uploads/admin_profile/'.$row['admin_image'] : 'assets/img/admin.png'; ?>" alt="" width="30px" height="30px" class="rounded-circle">
                    <span class="d-none d-md-block dropdown-toggle ps-2"><?= $_SESSION['auth_admin']['admin_name']; ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li class="dropdown-header">
                        <h6><?= $_SESSION['auth_admin']['admin_name'];?></h6>
                    </li>
                    <li><hr class="dropdown-divider" /></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="admin_profile.php">
                            <i class="bi bi-person"></i> <span>My Profile</span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider" /></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="account_settings.php">
                            <i class="bi bi-gear"></i> <span>Account Settings</span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider" /></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="admin.php">
                            <i class="bi bi-person-workspace"></i><span>Admin</span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider" /></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="circulation_settings.php">
                            <i class="bi bi-journal-album"></i><span>Circulation Settings</span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider" /></li>
                    <li>
                        <form action="allcode.php" method="POST">
                            <button class="dropdown-item d-flex align-items-center" name="logout_btn" type="submit">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Log Out</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</header>

<style>
.scrollable-dropdown {
    max-height: 300px; /* Set your desired max-height */
    overflow-y: auto;
}
</style>
