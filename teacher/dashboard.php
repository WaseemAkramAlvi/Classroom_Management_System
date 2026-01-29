<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireLogin('teacher');

$page_title = 'Teacher Dashboard';

$teacher_id = $_SESSION['user_id'];

// Get statistics
$stats = [];

// Total tests created
$result = $conn->query("SELECT COUNT(*) as count FROM tests WHERE teacher_id = $teacher_id");
$stats['total_tests'] = $result->fetch_assoc()['count'];

// Published tests
$result = $conn->query("SELECT COUNT(*) as count FROM tests WHERE teacher_id = $teacher_id AND status = 'published'");
$stats['published_tests'] = $result->fetch_assoc()['count'];

// Total students who took test
$result = $conn->query("SELECT COUNT(DISTINCT st.student_id) as count 
                        FROM student_tests st 
                        INNER JOIN tests t ON st.test_id = t.test_id 
                        WHERE t.teacher_id = $teacher_id AND st.status = 'completed'");
$stats['students_tested'] = $result->fetch_assoc()['count'];

// Pending approvals
$result = $conn->query("SELECT COUNT(*) as count FROM admissions a
                        INNER JOIN student_tests st ON a.student_test_id = st.student_test_id
                        INNER JOIN tests t ON st.test_id = t.test_id
                        WHERE t.teacher_id = $teacher_id AND a.status = 'pending'");
$stats['pending_approvals'] = $result->fetch_assoc()['count'];

// Recent test results
$recent_results_query = "SELECT s.full_name, s.email, t.test_name, st.score, st.percentage, st.result, st.created_at
                         FROM student_tests st
                         INNER JOIN students s ON st.student_id = s.student_id
                         INNER JOIN tests t ON st.test_id = t.test_id
                         WHERE t.teacher_id = $teacher_id AND st.status = 'completed'
                         ORDER BY st.created_at DESC LIMIT 5";
$recent_results = $conn->query($recent_results_query);

include '../includes/header.php';
?>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Teacher Panel</h2>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item active">
                <span class="icon">📊</span>
                <span>Dashboard</span>
            </a>
            <a href="tests.php" class="nav-item">
                <span class="icon">📝</span>
                <span>Manage Tests</span>
            </a>
            <a href="results.php" class="nav-item">
                <span class="icon">📈</span>
                <span>View Results</span>
            </a>
            <a href="approvals.php" class="nav-item">
                <span class="icon">✅</span>
                <span>Student Approvals</span>
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
                <div class="stat-icon" style="background: #4CAF50;">📝</div>
                <div class="stat-details">
                    <h3><?php echo $stats['total_tests']; ?></h3>
                    <p>Total Tests Created</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #2196F3;">✅</div>
                <div class="stat-details">
                    <h3><?php echo $stats['published_tests']; ?></h3>
                    <p>Published Tests</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #FF9800;">🎓</div>
                <div class="stat-details">
                    <h3><?php echo $stats['students_tested']; ?></h3>
                    <p>Students Tested</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #F44336;">⏳</div>
                <div class="stat-details">
                    <h3><?php echo $stats['pending_approvals']; ?></h3>
                    <p>Pending Approvals</p>
                </div>
            </div>
        </div>
        
        <!-- Recent Results -->
        <div class="section">
            <div class="section-header">
                <h2>Recent Test Results</h2>
                <a href="results.php" class="btn btn-primary">View All</a>
            </div>
            
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Test Name</th>
                            <th>Score</th>
                            <th>Percentage</th>
                            <th>Result</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recent_results->num_rows > 0): ?>
                            <?php while($result = $recent_results->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo escape($result['full_name']); ?></td>
                                <td><?php echo escape($result['email']); ?></td>
                                <td><?php echo escape($result['test_name']); ?></td>
                                <td><?php echo $result['score']; ?></td>
                                <td><?php echo $result['percentage']; ?>%</td>
                                <td>
                                    <?php
                                    $res = $result['result'];
                                    $badge_class = $res == 'passed' ? 'badge-success' : 'badge-danger';
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst($res); ?></span>
                                </td>
                                <td><?php echo formatDateTime($result['created_at']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No test results yet</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
