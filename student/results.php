<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireLogin('student');

$page_title = 'My Results';

$student_id = $_SESSION['user_id'];

// Get test result
$result_query = "SELECT st.*, t.test_name, t.passing_score, t.test_description
                 FROM student_tests st
                 INNER JOIN tests t ON st.test_id = t.test_id
                 WHERE st.student_id = $student_id AND st.status = 'completed'";
$result_data = $conn->query($result_query);

include '../includes/header.php';
?>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Student Panel</h2>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item">
                <span class="icon">📊</span>
                <span>Dashboard</span>
            </a>
            <a href="take_test.php" class="nav-item">
                <span class="icon">📝</span>
                <span>Take Test</span>
            </a>
            <a href="results.php" class="nav-item active">
                <span class="icon">📈</span>
                <span>My Results</span>
            </a>
            <a href="profile.php" class="nav-item">
                <span class="icon">👤</span>
                <span>Profile</span>
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
            <h1>My Results</h1>
        </div>
        
        <div class="section">
            <?php if ($result_data->num_rows > 0): ?>
                <?php $result = $result_data->fetch_assoc(); ?>
                
                <div class="result-card-detailed">
                    <h2><?php echo escape($result['test_name']); ?></h2>
                    <p class="test-desc"><?php echo escape($result['test_description']); ?></p>
                    
                    <div class="result-summary">
                        <div class="summary-item">
                            <div class="summary-label">Score</div>
                            <div class="summary-value"><?php echo $result['score']; ?> / <?php echo $result['total_marks']; ?></div>
                        </div>
                        
                        <div class="summary-item">
                            <div class="summary-label">Percentage</div>
                            <div class="summary-value"><?php echo $result['percentage']; ?>%</div>
                        </div>
                        
                        <div class="summary-item">
                            <div class="summary-label">Passing Score</div>
                            <div class="summary-value"><?php echo $result['passing_score']; ?>%</div>
                        </div>
                        
                        <div class="summary-item">
                            <div class="summary-label">Result</div>
                            <div class="summary-value">
                                <span class="badge badge-<?php echo $result['result'] == 'passed' ? 'success' : 'danger'; ?> badge-large">
                                    <?php echo ucfirst($result['result']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="result-info">
                        <p><strong>Started:</strong> <?php echo formatDateTime($result['start_time']); ?></p>
                        <p><strong>Completed:</strong> <?php echo formatDateTime($result['end_time']); ?></p>
                    </div>
                    
                    <?php
                    // Get admission status
                    $student = getUserById($conn, $student_id, 'student');
                    ?>
                    
                    <div class="admission-status">
                        <h3>Admission Status</h3>
                        <?php if ($student['admission_status'] == 'pending'): ?>
                            <div class="status-box status-pending">
                                <span class="status-icon">⏳</span>
                                <div>
                                    <strong>Pending Review</strong>
                                    <p>Your test result is under review by the teacher. Please wait for approval.</p>
                                </div>
                            </div>
                        <?php elseif ($student['admission_status'] == 'approved'): ?>
                            <div class="status-box status-approved">
                                <span class="status-icon">✅</span>
                                <div>
                                    <strong>Approved</strong>
                                    <p>Congratulations! Your admission has been approved. Welcome aboard!</p>
                                </div>
                            </div>
                        <?php elseif ($student['admission_status'] == 'rejected'): ?>
                            <div class="status-box status-rejected">
                                <span class="status-icon">❌</span>
                                <div>
                                    <strong>Rejected</strong>
                                    <p>Unfortunately, your admission has been rejected. Please contact the administration for more information.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
            <?php else: ?>
                <div class="empty-state">
                    <span style="font-size: 64px;">📝</span>
                    <h2>No Test Results</h2>
                    <p>You haven't taken any test yet.</p>
                    <a href="take_test.php" class="btn btn-primary">Take Test Now</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
