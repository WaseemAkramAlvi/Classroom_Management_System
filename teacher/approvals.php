<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireLogin('teacher');

$page_title = 'Student Approvals';

$teacher_id = $_SESSION['user_id'];

// Handle approve/reject
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $student_id = (int)$_POST['student_id'];
    $student_test_id = (int)$_POST['student_test_id'];
    $review_notes = sanitize($_POST['review_notes']);
    
    $status = $action == 'approve' ? 'approved' : 'rejected';
    $review_notes = $conn->real_escape_string($review_notes);
    
    // Update admission status
    $conn->query("UPDATE admissions SET status = '$status', reviewed_by = $teacher_id, 
                  review_notes = '$review_notes', reviewed_at = NOW() 
                  WHERE student_id = $student_id AND student_test_id = $student_test_id");
    
    // Update student admission status
    $conn->query("UPDATE students SET admission_status = '$status' WHERE student_id = $student_id");
    
    setFlashMessage('success', 'Student ' . $status . ' successfully');
    redirect('approvals.php');
}

// Get students pending approval
$query = "SELECT s.student_id, s.full_name, s.email, s.phone, s.date_of_birth,
                 t.test_name, st.student_test_id, st.score, st.total_marks, st.percentage, st.result, st.created_at as test_date
          FROM students s
          INNER JOIN student_tests st ON s.student_id = st.student_id
          INNER JOIN tests t ON st.test_id = t.test_id
          LEFT JOIN admissions a ON s.student_id = a.student_id AND st.student_test_id = a.student_test_id
          WHERE t.teacher_id = $teacher_id 
          AND st.status = 'completed' 
          AND (a.status = 'pending' OR a.status IS NULL)
          AND s.admission_status = 'pending'
          ORDER BY st.created_at DESC";

$pending_students = $conn->query($query);

// Get approved/rejected students
$history_query = "SELECT s.student_id, s.full_name, s.email, t.test_name, 
                         st.score, st.percentage, a.status, a.review_notes, a.reviewed_at
                  FROM admissions a
                  INNER JOIN students s ON a.student_id = s.student_id
                  INNER JOIN student_tests st ON a.student_test_id = st.student_test_id
                  INNER JOIN tests t ON st.test_id = t.test_id
                  WHERE a.reviewed_by = $teacher_id AND a.status != 'pending'
                  ORDER BY a.reviewed_at DESC LIMIT 10";

$history = $conn->query($history_query);

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
            <a href="results.php" class="nav-item">
                <span class="icon">📈</span>
                <span>View Results</span>
            </a>
            <a href="approvals.php" class="nav-item active">
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
            <h1>Student Approvals</h1>
        </div>
        
        <!-- Pending Approvals -->
        <div class="section">
            <h2>Pending Approvals</h2>
            
            <?php if ($pending_students->num_rows > 0): ?>
                <?php while($student = $pending_students->fetch_assoc()): ?>
                <div class="approval-card">
                    <div class="student-info">
                        <h3><?php echo escape($student['full_name']); ?></h3>
                        <p><strong>Email:</strong> <?php echo escape($student['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo escape($student['phone']); ?></p>
                        <p><strong>DOB:</strong> <?php echo formatDate($student['date_of_birth']); ?></p>
                    </div>
                    
                    <div class="test-info">
                        <h4>Test Performance</h4>
                        <p><strong>Test:</strong> <?php echo escape($student['test_name']); ?></p>
                        <p><strong>Score:</strong> <?php echo $student['score']; ?> / <?php echo $student['total_marks']; ?></p>
                        <p><strong>Percentage:</strong> <?php echo $student['percentage']; ?>%</p>
                        <p><strong>Result:</strong> 
                            <span class="badge badge-<?php echo $student['result'] == 'passed' ? 'success' : 'danger'; ?>">
                                <?php echo ucfirst($student['result']); ?>
                            </span>
                        </p>
                        <p><strong>Test Date:</strong> <?php echo formatDateTime($student['test_date']); ?></p>
                    </div>
                    
                    <div class="approval-actions">
                        <form method="POST" action="" class="approval-form">
                            <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
                            <input type="hidden" name="student_test_id" value="<?php echo $student['student_test_id']; ?>">
                            
                            <div class="form-group">
                                <label for="review_notes_<?php echo $student['student_id']; ?>">Review Notes</label>
                                <textarea name="review_notes" id="review_notes_<?php echo $student['student_id']; ?>" 
                                          rows="2" placeholder="Add your comments..."></textarea>
                            </div>
                            
                            <div class="button-group">
                                <button type="submit" name="action" value="approve" class="btn btn-success">
                                    ✓ Approve
                                </button>
                                <button type="submit" name="action" value="reject" class="btn btn-danger" 
                                        onclick="return confirm('Are you sure you want to reject this student?')">
                                    ✗ Reject
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <p>No pending approvals</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Approval History -->
        <div class="section">
            <h2>Recent Decisions</h2>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Test Name</th>
                            <th>Score</th>
                            <th>Decision</th>
                            <th>Notes</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($history->num_rows > 0): ?>
                            <?php while($record = $history->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo escape($record['full_name']); ?></td>
                                <td><?php echo escape($record['email']); ?></td>
                                <td><?php echo escape($record['test_name']); ?></td>
                                <td><?php echo $record['score']; ?> (<?php echo $record['percentage']; ?>%)</td>
                                <td>
                                    <span class="badge badge-<?php echo $record['status'] == 'approved' ? 'success' : 'danger'; ?>">
                                        <?php echo ucfirst($record['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo escape($record['review_notes']); ?></td>
                                <td><?php echo formatDateTime($record['reviewed_at']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No decisions made yet</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
