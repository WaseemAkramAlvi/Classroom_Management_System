<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireLogin('teacher');

$page_title = 'Test Results';

$teacher_id = $_SESSION['user_id'];

// Get all test results
$results_query = "SELECT s.full_name, s.email, t.test_name, 
                         st.score, st.total_marks, st.percentage, st.result, st.created_at
                  FROM student_tests st
                  INNER JOIN students s ON st.student_id = s.student_id
                  INNER JOIN tests t ON st.test_id = t.test_id
                  WHERE t.teacher_id = $teacher_id AND st.status = 'completed'
                  ORDER BY st.created_at DESC";

$results = $conn->query($results_query);

include '../includes/header.php';
?>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Teacher Panel</h2>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item">
                <span class="icon">📊</span>
                <span>Dashboard</span>
            </a>
            <a href="tests.php" class="nav-item">
                <span class="icon">📝</span>
                <span>Manage Tests</span>
            </a>
            <a href="results.php" class="nav-item active">
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
            <h1>Test Results</h1>
        </div>
        
        <div class="section">
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Test Name</th>
                            <th>Score</th>
                            <th>Total Marks</th>
                            <th>Percentage</th>
                            <th>Result</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($results->num_rows > 0): ?>
                            <?php while($result = $results->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo escape($result['full_name']); ?></td>
                                <td><?php echo escape($result['email']); ?></td>
                                <td><?php echo escape($result['test_name']); ?></td>
                                <td><?php echo $result['score']; ?></td>
                                <td><?php echo $result['total_marks']; ?></td>
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
                                <td colspan="8" class="text-center">No test results yet</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
