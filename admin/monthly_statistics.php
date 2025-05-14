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
$query = "SELECT course, COUNT(*) as count FROM user_log WHERE date_log BETWEEN '$startDate' AND '$endDate' GROUP BY course";
$result = mysqli_query($con, $query);

$courses = [];
$counts = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $courses[] = $row['course'];
        $counts[] = $row['count'];
    }
} else {
    echo "No data found for selected month.";
    exit;
}

mysqli_close($con);

// JSON encode for Chart.js
$jsonCourses = json_encode($courses);
$jsonCounts = json_encode($counts);
?>

    <h2>Monthly Course Statistics for <?= htmlspecialchars($month) ?></h2>
    <canvas id="barChart" width="600" height="300"></canvas>

    <script>
        const courses = <?= $jsonCourses ?>;
        const counts = <?= $jsonCounts ?>;

        new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: {
                labels: courses,
                datasets: [{
                    label: 'User count by course',
                    data: counts,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)'
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0
                    }
                }
            }
        });
    </script>

<?php 
include('./includes/footer.php');
include('./includes/script.php');
include('../message.php');
?>
