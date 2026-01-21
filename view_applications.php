<?php
// view_applications.php - English only
require_once 'config.php';

// Simple admin check (add proper authentication in production)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_logged_in'])) {
    // Redirect to login or show access denied
    echo '<!DOCTYPE html>
    <html>
    <head><title>Access Denied</title><link rel="stylesheet" href="styles.css"></head>
    <body>
        <div class="container">
            <h2>Access Denied</h2>
            <p>Please <a href="admin_login.html">login</a> to access the admin panel.</p>
        </div>
    </body>
    </html>';
    exit;
}

// Get all applications
$stmt = $pdo->query("SELECT * FROM applications ORDER BY application_date DESC");
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Visa Applications</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header-flags">
            <img src="https://flagcdn.com/w40/et.png" alt="Ethiopia Flag" class="flag">
            <h1><i class="fas fa-cogs"></i> Admin Panel</h1>
            <img src="https://flagcdn.com/w40/ca.png" alt="Canada Flag" class="flag">
        </div>

        <div class="form-container">
            <div class="form-header">
                <h2><i class="fas fa-chart-bar"></i> Visa Applications Dashboard</h2>
                <p class="subtitle">Manage and review visa applications</p>
                <div class="admin-actions">
                    <a href="logout.php" class="btn btn-outline">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
            <!-- Statistics -->
            <div class="stats-grid">
                <?php
                $total = count($applications);
                $pending = count(array_filter($applications, fn($app) => $app['status'] == 'Pending'));
                $review = count(array_filter($applications, fn($app) => $app['status'] == 'Under Review'));
                $approved = count(array_filter($applications, fn($app) => $app['status'] == 'Approved'));
                $rejected = count(array_filter($applications, fn($app) => $app['status'] == 'Rejected'));
                ?>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total; ?></div>
                    <div class="stat-label">Total Applications</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: #ffc107;"><?php echo $pending; ?></div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: #17a2b8;"><?php echo $review; ?></div>
                    <div class="stat-label">Under Review</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: #28a745;"><?php echo $approved; ?></div>
                    <div class="stat-label">Approved</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: #dc3545;"><?php echo $rejected; ?></div>
                    <div class="stat-label">Rejected</div>
                </div>
            </div>

            <!-- Applications Table -->
            <div class="table-container">
                <table class="applications-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> Application ID</th>
                            <th><i class="fas fa-user"></i> Full Name</th>
                            <th><i class="fas fa-phone"></i> Phone</th>
                            <th><i class="fas fa-globe"></i> Country</th>
                            <th><i class="fas fa-calendar"></i> Submitted</th>
                            <th><i class="fas fa-info-circle"></i> Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                        <tr>
                            <td>ET-CA-<?php echo str_pad($app['id'], 6, "0", STR_PAD_LEFT); ?></td>
                            <td><?php echo htmlspecialchars($app['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($app['phone_number']); ?></td>
                            <td><?php echo htmlspecialchars($app['country']); ?></td>
                            <td><?php echo date('M j, Y', strtotime($app['application_date'])); ?></td>
                            <td>
                                <?php
                                $statusClass = strtolower(str_replace(' ', '-', $app['status']));
                                echo '<span class="status-badge status-' . $statusClass . '">' . $app['status'] . '</span>';
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
