<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireLogin('admin');

$page_title = 'Admin Dashboard';

// Get statistics
$stats = [];

// Total teachers
$result = $conn->query("SELECT COUNT(*) as count FROM teachers");
$stats['total_teachers'] = $result->fetch_assoc()['count'];

// Active teachers
$result = $conn->query("SELECT COUNT(*) as count FROM teachers WHERE status = 'active'");
$stats['active_teachers'] = $result->fetch_assoc()['count'];

// Total students
$result = $conn->query("SELECT COUNT(*) as count FROM students");
$stats['total_students'] = $result->fetch_assoc()['count'];

// Pending admissions
$result = $conn->query("SELECT COUNT(*) as count FROM students WHERE admission_status = 'pending' AND test_taken = 1");
$stats['pending_admissions'] = $result->fetch_assoc()['count'];

// Approved students
$result = $conn->query("SELECT COUNT(*) as count FROM students WHERE admission_status = 'approved'");
$stats['approved_students'] = $result->fetch_assoc()['count'];

// Rejected students
$result = $conn->query("SELECT COUNT(*) as count FROM students WHERE admission_status = 'rejected'");
$stats['rejected_students'] = $result->fetch_assoc()['count'];

// Recent students
$recent_students_query = "SELECT * FROM students ORDER BY created_at DESC LIMIT 5";
$recent_students = $conn->query($recent_students_query);

include '../includes/header.php';
?>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Admin Panel</h2>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item active">
                <span class="icon">📊</span>
                <span>Dashboard</span>
            </a>
            <a href="teachers.php" class="nav-item">
                <span class="icon">👨‍🏫</span>
                <span>Manage Teachers</span>
            </a>
            <a href="students.php" class="nav-item">
                <span class="icon">🎓</span>
                <span>Manage Students</span>
            </a>
            <a href="../auth/logout.php" class="nav-item">
                <span class="icon">🚪</span>
                <span>Logout</span>
            </a>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="content-header">
            <h1>Dashboard</h1>
            <div class="user-info">
                <span>Welcome, <?php echo escape($_SESSION['full_name']); ?></span>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #4CAF50;">👨‍🏫</div>
                <div class="stat-details">
                    <h3><?php echo $stats['total_teachers']; ?></h3>
                    <p>Total Teachers</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #2196F3;">✅</div>
                <div class="stat-details">
                    <h3><?php echo $stats['active_teachers']; ?></h3>
                    <p>Active Teachers</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #FF9800;">🎓</div>
                <div class="stat-details">
                    <h3><?php echo $stats['total_students']; ?></h3>
                    <p>Total Students</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #F44336;">⏳</div>
                <div class="stat-details">
                    <h3><?php echo $stats['pending_admissions']; ?></h3>
                    <p>Pending Admissions</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #9C27B0;">✔️</div>
                <div class="stat-details">
                    <h3><?php echo $stats['approved_students']; ?></h3>
                    <p>Approved Students</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #607D8B;">❌</div>
                <div class="stat-details">
                    <h3><?php echo $stats['rejected_students']; ?></h3>
                    <p>Rejected Students</p>
                </div>
            </div>
        </div>
        
        <!-- Recent Students -->
        <div class="section">
            <div class="section-header">
                <h2>Recent Student Registrations</h2>
                <a href="students.php" class="btn btn-primary">View All</a>
            </div>
            
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Test Status</th>
                            <th>Admission Status</th>
                            <th>Registered At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recent_students->num_rows > 0): ?>
                            <?php while($student = $recent_students->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $student['student_id']; ?></td>
                                <td><?php echo escape($student['full_name']); ?></td>
                                <td><?php echo escape($student['email']); ?></td>
                                <td><?php echo escape($student['phone']); ?></td>
                                <td>
                                    <?php if ($student['test_taken']): ?>
                                        <span class="badge badge-success">Completed</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Not Taken</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $status = $student['admission_status'];
                                    $badge_class = '';
                                    switch($status) {
                                        case 'approved':
                                            $badge_class = 'badge-success';
                                            break;
                                        case 'rejected':
                                            $badge_class = 'badge-danger';
                                            break;
                                        default:
                                            $badge_class = 'badge-warning';
                                    }
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst($status); ?></span>
                                </td>
                                <td><?php echo formatDateTime($student['created_at']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No students registered yet</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
