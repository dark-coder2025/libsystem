<?php 
include('authentication.php');
include('includes/header.php'); 
include('./includes/sidebar.php'); 

// ---- Summary Counts ----
$total_errors_query = $con->query("SELECT COUNT(*) AS total FROM error_logs");
$total_errors = $total_errors_query ? $total_errors_query->fetch_assoc()['total'] : 0;

$today_errors_query = $con->query("SELECT COUNT(*) AS total FROM error_logs WHERE DATE(created_at) = CURDATE()");
$today_errors = $today_errors_query ? $today_errors_query->fetch_assoc()['total'] : 0;

$week_errors_query = $con->query("SELECT COUNT(*) AS total FROM error_logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$week_errors = $week_errors_query ? $week_errors_query->fetch_assoc()['total'] : 0;

$critical_query = $con->query("SELECT COUNT(*) AS total FROM error_logs WHERE error_type IN ('Fatal Error', 'Parse Error', 'Core Error') AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$critical_errors = $critical_query ? $critical_query->fetch_assoc()['total'] : 0;

// ---- Fetch logs with optional filters ----
$where_clauses = [];
$params = [];
$types = '';

if (isset($_GET['filter_type']) && $_GET['filter_type'] !== '') {
    $where_clauses[] = "error_type = ?";
    $params[] = $_GET['filter_type'];
    $types .= 's';
}
if (isset($_GET['date_from']) && $_GET['date_from'] !== '') {
    $where_clauses[] = "DATE(created_at) >= ?";
    $params[] = $_GET['date_from'];
    $types .= 's';
}
if (isset($_GET['date_to']) && $_GET['date_to'] !== '') {
    $where_clauses[] = "DATE(created_at) <= ?";
    $params[] = $_GET['date_to'];
    $types .= 's';
}

$sql = "SELECT * FROM error_logs";
if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}
$sql .= " ORDER BY created_at DESC LIMIT 500";

$stmt = $con->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get distinct error types for filter dropdown
$types_result = $con->query("SELECT DISTINCT error_type FROM error_logs ORDER BY error_type");
$available_types = [];
while ($row = $types_result->fetch_assoc()) {
    $available_types[] = $row['error_type'];
}
?>

<style>
.error-badge { padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; }
.badge-fatal { background: #f8d7da; color: #842029; }
.badge-warning { background: #fff3cd; color: #664d03; }
.badge-notice { background: #cff4fc; color: #055160; }
.badge-deprecated { background: #e2e3e5; color: #41464b; }
.badge-default { background: #d1e7dd; color: #0f5132; }
.msg-cell { max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; cursor: pointer; }
.msg-cell:hover { white-space: normal; overflow: visible; }
.summary-card { border-left: 4px solid; border-radius: 8px; }
.summary-card .card-body { padding: 15px 20px; }
.summary-card h2 { font-size: 28px; font-weight: 700; margin: 0; }
.summary-card p { margin: 0; font-size: 13px; }
</style>

<main id="main" class="main">
    <div class="pagetitle" data-aos="fade-down">
        <h1>Error Logs</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href=".">Home</a></li>
                <li class="breadcrumb-item active">Error Logs</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <!-- Summary Cards -->
        <div class="row mb-3" data-aos="fade-down">
            <div class="col-xl-3 col-md-6 mb-2">
                <div class="card summary-card border-primary">
                    <div class="card-body">
                        <p class="text-primary">Total Errors</p>
                        <h2><?= number_format($total_errors); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-2">
                <div class="card summary-card border-info">
                    <div class="card-body">
                        <p class="text-info">Today</p>
                        <h2><?= number_format($today_errors); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-2">
                <div class="card summary-card border-warning">
                    <div class="card-body">
                        <p class="text-warning">Last 7 Days</p>
                        <h2><?= number_format($week_errors); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-2">
                <div class="card summary-card border-danger">
                    <div class="card-body">
                        <p class="text-danger">Critical (7 Days)</p>
                        <h2><?= number_format($critical_errors); ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="card mb-3" data-aos="fade-down">
            <div class="card-body py-2">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Error Type</label>
                        <select name="filter_type" class="form-select form-select-sm">
                            <option value="">All Types</option>
                            <?php foreach ($available_types as $t): ?>
                                <option value="<?= htmlspecialchars($t); ?>" 
                                    <?= (isset($_GET['filter_type']) && $_GET['filter_type'] === $t) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($t); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">From</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" 
                               value="<?= htmlspecialchars($_GET['date_from'] ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">To</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" 
                               value="<?= htmlspecialchars($_GET['date_to'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-sm btn-primary me-1">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <a href="error_logs.php" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                    <div class="col-md-2 text-end">
                        <form action="error_logs_code.php" method="POST" class="d-inline" 
                              onsubmit="return confirm('Clear ALL error logs? This cannot be undone.');">
                            <button type="submit" name="clear_all_btn" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash3"></i> Clear All
                            </button>
                        </form>
                    </div>
                </form>
            </div>
        </div>

        <!-- Error Logs Table -->
        <div class="card" data-aos="fade-down">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="errorLogsTable" class="table table-sm table-striped table-hover" style="width:100%">
                        <thead class="table-dark">
                            <tr>
                                <th style="width:40px">#</th>
                                <th style="width:110px">Type</th>
                                <th>Message</th>
                                <th style="width:200px">File</th>
                                <th style="width:50px">Line</th>
                                <th style="width:90px">User IP</th>
                                <th style="width:60px">Role</th>
                                <th style="width:140px">Date/Time</th>
                                <th style="width:80px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $row_num = 1;
                            while ($log = $result->fetch_assoc()): 
                                $type = $log['error_type'];
                                $badge_class = 'badge-default';
                                if (strpos($type, 'Fatal') !== false || strpos($type, 'Parse') !== false || strpos($type, 'Core') !== false) {
                                    $badge_class = 'badge-fatal';
                                } elseif (strpos($type, 'Warning') !== false) {
                                    $badge_class = 'badge-warning';
                                } elseif (strpos($type, 'Notice') !== false) {
                                    $badge_class = 'badge-notice';
                                } elseif (strpos($type, 'Deprecated') !== false) {
                                    $badge_class = 'badge-deprecated';
                                }
                            ?>
                            <tr>
                                <td><?= $row_num++; ?></td>
                                <td><span class="error-badge <?= $badge_class; ?>"><?= htmlspecialchars($type); ?></span></td>
                                <td class="msg-cell" title="<?= htmlspecialchars($log['error_message']); ?>">
                                    <?= htmlspecialchars($log['error_message']); ?>
                                </td>
                                <td class="msg-cell" title="<?= htmlspecialchars($log['error_file'] ?? ''); ?>">
                                    <?= htmlspecialchars(basename($log['error_file'] ?? 'N/A')); ?>
                                </td>
                                <td><?= $log['error_line'] ?? '-'; ?></td>
                                <td><small><?= htmlspecialchars($log['user_ip'] ?? '-'); ?></small></td>
                                <td><small><?= htmlspecialchars($log['user_role'] ?? '-'); ?></small></td>
                                <td><small><?= date('M d, Y h:i A', strtotime($log['created_at'])); ?></small></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary py-0 px-1 view-detail-btn" 
                                            data-bs-toggle="modal" data-bs-target="#detailModal"
                                            data-id="<?= $log['id']; ?>"
                                            data-type="<?= htmlspecialchars($log['error_type']); ?>"
                                            data-message="<?= htmlspecialchars($log['error_message'], ENT_QUOTES); ?>"
                                            data-file="<?= htmlspecialchars($log['error_file'] ?? ''); ?>"
                                            data-line="<?= $log['error_line'] ?? ''; ?>"
                                            data-ip="<?= htmlspecialchars($log['user_ip'] ?? ''); ?>"
                                            data-uri="<?= htmlspecialchars($log['request_uri'] ?? ''); ?>"
                                            data-agent="<?= htmlspecialchars($log['user_agent'] ?? ''); ?>"
                                            data-method="<?= htmlspecialchars($log['request_method'] ?? ''); ?>"
                                            data-userid="<?= $log['user_id'] ?? ''; ?>"
                                            data-role="<?= htmlspecialchars($log['user_role'] ?? ''); ?>"
                                            data-date="<?= htmlspecialchars($log['created_at']); ?>"
                                            title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <form action="error_logs_code.php" method="POST" class="d-inline">
                                        <input type="hidden" name="error_id" value="<?= $log['id']; ?>">
                                        <button type="submit" name="delete_btn" class="btn btn-sm btn-outline-danger py-0 px-1"
                                                onclick="return confirm('Delete this log entry?');" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="bi bi-bug"></i> Error Log Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-sm">
                    <tr><th style="width:120px">Error ID</th><td id="modal-id"></td></tr>
                    <tr><th>Type</th><td><span id="modal-type" class="error-badge"></span></td></tr>
                    <tr><th>Message</th><td id="modal-message" style="word-break:break-all;"></td></tr>
                    <tr><th>File</th><td id="modal-file" style="word-break:break-all;"></td></tr>
                    <tr><th>Line</th><td id="modal-line"></td></tr>
                    <tr><th>User IP</th><td id="modal-ip"></td></tr>
                    <tr><th>Request URL</th><td id="modal-uri" style="word-break:break-all;"></td></tr>
                    <tr><th>Method</th><td id="modal-method"></td></tr>
                    <tr><th>User Agent</th><td id="modal-agent" style="word-break:break-all;"></td></tr>
                    <tr><th>User ID</th><td id="modal-userid"></td></tr>
                    <tr><th>Role</th><td id="modal-role"></td></tr>
                    <tr><th>Timestamp</th><td id="modal-date"></td></tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php 
include('./includes/footer.php');
include('./includes/script.php');
include('../message.php');
?>

<script>
// Initialize DataTable
$(document).ready(function() {
    $('#errorLogsTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        responsive: true,
        language: {
            emptyTable: "No error logs found",
            zeroRecords: "No matching error logs found"
        }
    });
});

// Populate detail modal
document.querySelectorAll('.view-detail-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.getElementById('modal-id').textContent = this.dataset.id;
        document.getElementById('modal-type').textContent = this.dataset.type;
        document.getElementById('modal-type').className = 'error-badge ' + getBadgeClass(this.dataset.type);
        document.getElementById('modal-message').textContent = this.dataset.message;
        document.getElementById('modal-file').textContent = this.dataset.file || 'N/A';
        document.getElementById('modal-line').textContent = this.dataset.line || '-';
        document.getElementById('modal-ip').textContent = this.dataset.ip || '-';
        document.getElementById('modal-uri').textContent = this.dataset.uri || '-';
        document.getElementById('modal-agent').textContent = this.dataset.agent || '-';
        document.getElementById('modal-method').textContent = this.dataset.method || '-';
        document.getElementById('modal-userid').textContent = this.dataset.userid || '-';
        document.getElementById('modal-role').textContent = this.dataset.role || '-';
        document.getElementById('modal-date').textContent = this.dataset.date || '-';
    });
});

function getBadgeClass(type) {
    if (type.includes('Fatal') || type.includes('Parse') || type.includes('Core')) return 'badge-fatal';
    if (type.includes('Warning')) return 'badge-warning';
    if (type.includes('Notice')) return 'badge-notice';
    if (type.includes('Deprecated')) return 'badge-deprecated';
    return 'badge-default';
}
</script>
