<?php
include('authentication.php');
include('includes/header.php');
include('./includes/sidebar.php');

$month = $_GET['month'] ?? '';

if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month)) {
    echo "Invalid or no month selected.";
    exit;
}

$dateObj = DateTime::createFromFormat('Y-m', $month);
$monthDisplay = $dateObj ? $dateObj->format('F Y') : 'Invalid month';

$courses = [];
$total_student_course = [];
$labels = [];

$startDate = $month . '-01';
$endDate = date('Y-m-t', strtotime($startDate));

// Query for course-wise count
$query = "SELECT course, COUNT(course) as count FROM user_log 
          WHERE date_log BETWEEN '$startDate' AND '$endDate' 
          GROUP BY course";

$query_run = mysqli_query($con, $query);

if ($query_run && mysqli_num_rows($query_run) > 0) {
    foreach ($query_run as $course) {
        $courseName = $course['course'];
        $studentCount = $course['count'];
        
        $labels[] = "{$courseName} : {$studentCount}";
        $courses[] = $courseName;
        $total_student_course[] = $studentCount;
    }
}

// Query for total logs in the month
$totalLogsQuery = "SELECT COUNT(*) as total_logs FROM user_log 
                   WHERE date_log BETWEEN '$startDate' AND '$endDate'";
$totalLogsResult = mysqli_query($con, $totalLogsQuery);
$totalLogs = 0;

if ($totalLogsResult && mysqli_num_rows($totalLogsResult) > 0) {
    $row = mysqli_fetch_assoc($totalLogsResult);
    $totalLogs = $row['total_logs'];
}
?>

<main id="main" class="main">
    <div class="pagetitle" data-aos="fade-down">
        <h1>Monthly Statistics</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">Monthly Statistics</li>
            </ol>
        </nav>
    </div>

    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div data-aos="fade-down" class="col-lg-12">
                        <div class="card" id="printArea">
                            <div class="card-body">
                                <h5 style="text-align: center; margin-top: 10px;">
                                    Monthly Statistics for <?= htmlspecialchars($monthDisplay) ?>
                                </h5>

                                <button onclick="printMonthlyStatistics()" class="btn btn-primary mb-3 float-end no-print">
                                    Print Monthly Statistics
                                </button>

                                <?php if (!empty($courses)): ?>
                                    <canvas id="barChart" style="max-height: 400px;max-width: 100%;"></canvas>
                                    <script>
                                        document.addEventListener("DOMContentLoaded", () => {
                                            new Chart(document.querySelector('#barChart'), {
                                                type: 'bar',
                                                data: {
                                                    labels: <?= json_encode($labels) ?>,
                                                    datasets: [{
                                                        label: 'Program',
                                                        data: <?= json_encode($total_student_course) ?>,
                                                        backgroundColor: [
                                                            'rgba(255, 242, 64, 0.2)',
                                                            'rgba(182, 255, 86, 0.2)',
                                                            'rgba(75, 135, 192, 0.2)',
                                                            'rgba(235, 54, 54, 0.2)',
                                                            'rgba(43, 40, 49, 0.2)',
                                                            'rgba(201, 203, 207, 0.2)',
                                                            'rgba(255, 99, 132, 0.2)'
                                                        ],
                                                        borderColor: [
                                                            'rgb(250, 229, 37)',
                                                            'rgb(84, 233, 39)',
                                                            'rgb(56, 155, 221)',
                                                            'rgb(235, 54, 54)',
                                                            'rgb(0, 0, 0)',
                                                            'rgb(201, 203, 207)',
                                                            'rgb(255, 99, 132)'
                                                        ],
                                                        borderWidth: 1
                                                    }]
                                                },
                                                options: {
                                                    scales: {
                                                        y: {
                                                            beginAtZero: true,
                                                            ticks: {
                                                                stepSize: 1
                                                            }
                                                        }
                                                    }
                                                }
                                            });
                                        });

                                        function printMonthlyStatistics() {
                                            window.print();
                                        }
                                    </script>

                                    <p style="text-align: left; margin-bottom: 20px; margin-top: 20px;">
                                        <h5>Total User Logs for <?= htmlspecialchars($monthDisplay) ?>:</h5> <strong><?= $totalLogs ?></strong>
                                    </p>
                                <?php else: ?>
                                    <p style="text-align: center; margin-top: 20px;">
                                        No data found for <?= htmlspecialchars($monthDisplay) ?>.
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Hide everything but printArea during print -->
<style>
@media print {
    body * {
        visibility: hidden;
    }

    #printArea, #printArea * {
        visibility: visible;
    }

    #printArea {
        position: absolute;
        left: 0;
        top: 25%;
        width: 100%;
    }

    .no-print {
        display: none;
    }
}
</style>

<?php 
include('./includes/footer.php');
include('./includes/script.php');
include('../message.php');
?>
