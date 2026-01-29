<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireLogin('student');

$page_title = 'Student Dashboard';

$student_id = $_SESSION['user_id'];

// Get student details
$student = getUserById($conn, $student_id, 'student');

// Get test status
$test_query = "SELECT st.*, t.test_name, t.passing_score 
               FROM student_tests st 
               INNER JOIN tests t ON st.test_id = t.test_id 
               WHERE st.student_id = $student_id";
$test_result = $conn->query($test_query);
$test_info = $test_result->num_rows > 0 ? $test_result->fetch_assoc() : null;

// Get available published tests
$available_tests_query = "SELECT t.* FROM tests t 
                          WHERE t.status = 'published' 
                          AND NOT EXISTS (
                              SELECT 1 FROM student_tests st 
                              WHERE st.test_id = t.test_id AND st.student_id = $student_id
                          )
                          ORDER BY t.created_at DESC";
$available_tests = $conn->query($available_tests_query);

include '../includes/header.php';
?>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Student Panel</h2>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item active">
                <span class="icon">📊</span>
                <span>Dashboard</span>
            </a>
            <a href="take_test.php" class="nav-item">
                <span class="icon">📝</span>
                <span>Take Test</span>
            </a>
            <a href="results.php" class="nav-item">
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
            <h1>Dashboard</h1>
            <div class="user-info">
                <span>Welcome, <?php echo escape($_SESSION['full_name']); ?></span>
            </div>
        </div>
        
        <!-- Status Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #4CAF50;">🎓</div>
                <div class="stat-details">
                    <h3><?php echo ucfirst($student['admission_status']); ?></h3>
                    <p>Admission Status</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #2196F3;">📝</div>
                <div class="stat-details">
                    <h3><?php echo $student['test_taken'] ? 'Completed' : 'Pending'; ?></h3>
                    <p>Test Status</p>
                </div>
            </div>
            
            <?php if ($test_info): ?>
            <div class="stat-card">
                <div class="stat-icon" style="background: #FF9800;">📊</div>
                <div class="stat-details">
                    <h3><?php echo $test_info['percentage']; ?>%</h3>
                    <p>Your Score</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: <?php echo $test_info['result'] == 'passed' ? '#4CAF50' : '#F44336'; ?>;">
                    <?php echo $test_info['result'] == 'passed' ? '✓' : '✗'; ?>
                </div>
                <div class="stat-details">
                    <h3><?php echo ucfirst($test_info['result']); ?></h3>
                    <p>Test Result</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Test Result Section -->
        <?php if ($test_info): ?>
        <div class="section">
            <div class="section-header">
                <h2>Your Test Result</h2>
            </div>
            
            <div class="result-card">
                <h3><?php echo escape($test_info['test_name']); ?></h3>
                <div class="result-details">
                    <div class="result-item">
                        <span class="label">Score:</span>
                        <span class="value"><?php echo $test_info['score']; ?> / <?php echo $test_info['total_marks']; ?></span>
                    </div>
                    <div class="result-item">
                        <span class="label">Percentage:</span>
                        <span class="value"><?php echo $test_info['percentage']; ?>%</span>
                    </div>
                    <div class="result-item">
                        <span class="label">Passing Score:</span>
                        <span class="value"><?php echo $test_info['passing_score']; ?>%</span>
                    </div>
                    <div class="result-item">
                        <span class="label">Result:</span>
                        <span class="badge badge-<?php echo $test_info['result'] == 'passed' ? 'success' : 'danger'; ?>">
                            <?php echo ucfirst($test_info['result']); ?>
                        </span>
                    </div>
                    <div class="result-item">
                        <span class="label">Completed:</span>
                        <span class="value"><?php echo formatDateTime($test_info['end_time']); ?></span>
                    </div>
                </div>
                
                <?php if ($student['admission_status'] == 'pending'): ?>
                <div class="info-box">
                    <p>Your test result is under review. Please wait for approval from the teacher.</p>
                </div>
                <?php elseif ($student['admission_status'] == 'approved'): ?>
                <div class="success-box">
                    <p>Congratulations! Your admission has been approved.</p>
                </div>
                <?php elseif ($student['admission_status'] == 'rejected'): ?>
                <div class="error-box">
                    <p>Sorry, your admission has been rejected.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        <!-- Available Tests Section -->
        <div class="section">
            <div class="section-header">
                <h2>Available Tests</h2>
            </div>
            
            <?php if ($available_tests->num_rows > 0): ?>
                <div class="tests-grid">
                    <?php while($test = $available_tests->fetch_assoc()): ?>
                    <div class="test-card">
                        <h3><?php echo escape($test['test_name']); ?></h3>
                        <p><?php echo escape($test['test_description']); ?></p>
                        <div class="test-meta">
                            <span>⏱️ Duration: <?php echo $test['duration_minutes']; ?> minutes</span>
                            <span>✓ Passing: <?php echo $test['passing_score']; ?>%</span>
                            <span>📊 Marks: <?php echo $test['total_marks']; ?></span>
                        </div>
                        <a href="take_test.php?test_id=<?php echo $test['test_id']; ?>" class="btn btn-primary btn-block">
                            Start Test
                        </a>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p>No tests available at the moment. Please check back later.</p>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
