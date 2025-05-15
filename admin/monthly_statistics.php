<?php
include('authentication.php');
include('includes/header.php'); 
include('./includes/sidebar.php'); 

$month = $_GET['month'] ?? '';
if (!$month) {
    echo "No month selected.";
    exit;
}

// Format: 2025-04 → 2025-04-01 to 2025-04-30
$startDate = $month . '-01';
$endDate = date("Y-m-t", strtotime($startDate)); // gets last day of the month

// Prepare and execute the query
$query = "SELECT course, COUNT(course) as count FROM user_log WHERE date_log BETWEEN '$startDate' AND '$endDate' GROUP BY course";
$result = mysqli_query($con, $query);

foreach ($query_run as $course) {
    $courses[] = $course['course'];
    $total_student_course[] = $course['COUNT(course)'];
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
    <div class="card">
        <div class="card-body">
            <h5 style="text-align: center;margin-top: 10px;">Monthly Course Statistics for <?= htmlspecialchars($month) ?></h5>
            <canvas id="barChart" style="max-height: 400px;"></canvas>

            <script>
                document.addEventListener("DOMContentLoaded", () => {
                    new Chart(document.querySelector('#barChart'), {
                        type: 'bar',
                        data: {
                            labels: <?php echo json_encode($courses)?>,
                            datasets: [{
                                label: 'Program',
                                data: <?php echo json_encode($total_student_course)?>,
                                backgroundColor: [
                                    'rgba(255, 159, 64, 0.2)',
                                    'rgba(255, 205, 86, 0.2)',
                                    'rgba(75, 192, 192, 0.2)',
                                    'rgba(54, 162, 235, 0.2)',
                                    'rgba(153, 102, 255, 0.2)',
                                    'rgba(201, 203, 207, 0.2)'
                                ],
                                borderColor: [
                                    'rgb(255, 159, 64)',
                                    'rgb(255, 205, 86)',
                                    'rgb(75, 192, 192)',
                                    'rgb(54, 162, 235)',
                                    'rgb(153, 102, 255)',
                                    'rgb(201, 203, 207)'
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
            </script>
        </div>
    </div>
</div>
</div>
               </div>
          </div>
     </section>

</main>

<?php 
include('./includes/footer.php');
include('./includes/script.php');
include('../message.php');
?>
