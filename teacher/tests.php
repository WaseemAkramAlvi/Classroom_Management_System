<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireLogin('teacher');

$page_title = 'Manage Tests';

$teacher_id = $_SESSION['user_id'];

// Handle creating new test
if (isset($_POST['create_test'])) {
    $test_name = sanitize($_POST['test_name']);
    $test_description = sanitize($_POST['test_description']);
    $duration_minutes = (int)$_POST['duration_minutes'];
    $passing_score = (int)$_POST['passing_score'];
    
    $test_name = $conn->real_escape_string($test_name);
    $test_description = $conn->real_escape_string($test_description);
    
    $query = "INSERT INTO tests (teacher_id, test_name, test_description, duration_minutes, passing_score) 
              VALUES ($teacher_id, '$test_name', '$test_description', $duration_minutes, $passing_score)";
    
    if ($conn->query($query)) {
        $test_id = $conn->insert_id;
        setFlashMessage('success', 'Test created successfully. Now add questions.');
        redirect('add_questions.php?test_id=' . $test_id);
    } else {
        setFlashMessage('error', 'Failed to create test');
    }
}

// Handle delete test
if (isset($_POST['delete_test'])) {
    $test_id = (int)$_POST['test_id'];
    $conn->query("DELETE FROM tests WHERE test_id = $test_id AND teacher_id = $teacher_id");
    setFlashMessage('success', 'Test deleted successfully');
    redirect('tests.php');
}

// Handle publish test
if (isset($_POST['publish_test'])) {
    $test_id = (int)$_POST['test_id'];
    $conn->query("UPDATE tests SET status = 'published' WHERE test_id = $test_id AND teacher_id = $teacher_id");
    setFlashMessage('success', 'Test published successfully');
    redirect('tests.php');
}

// Get all tests
$tests_query = "SELECT t.*, COUNT(q.question_id) as question_count 
                FROM tests t 
                LEFT JOIN questions q ON t.test_id = q.test_id 
                WHERE t.teacher_id = $teacher_id 
                GROUP BY t.test_id 
                ORDER BY t.created_at DESC";
$tests = $conn->query($tests_query);

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
            <a href="tests.php" class="nav-item active">
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
            <h1>Manage Tests</h1>
            <button class="btn btn-primary" onclick="openModal('createTestModal')">Create New Test</button>
        </div>
        
        <div class="section">
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Test ID</th>
                            <th>Test Name</th>
                            <th>Duration</th>
                            <th>Passing Score</th>
                            <th>Questions</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($tests->num_rows > 0): ?>
                            <?php while($test = $tests->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $test['test_id']; ?></td>
                                <td><?php echo escape($test['test_name']); ?></td>
                                <td><?php echo $test['duration_minutes']; ?> mins</td>
                                <td><?php echo $test['passing_score']; ?>%</td>
                                <td><?php echo $test['question_count']; ?></td>
                                <td>
                                    <?php
                                    $status = $test['status'];
                                    $badge_class = '';
                                    switch($status) {
                                        case 'published':
                                            $badge_class = 'badge-success';
                                            break;
                                        case 'closed':
                                            $badge_class = 'badge-danger';
                                            break;
                                        default:
                                            $badge_class = 'badge-warning';
                                    }
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst($status); ?></span>
                                </td>
                                <td><?php echo formatDate($test['created_at']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="add_questions.php?test_id=<?php echo $test['test_id']; ?>" 
                                           class="btn btn-sm btn-primary">Add Questions</a>
                                        
                                        <?php if ($test['status'] == 'draft' && $test['question_count'] > 0): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="test_id" value="<?php echo $test['test_id']; ?>">
                                                <button type="submit" name="publish_test" class="btn btn-sm btn-success">Publish</button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="test_id" value="<?php echo $test['test_id']; ?>">
                                            <button type="submit" name="delete_test" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Delete this test?')">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No tests created yet</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Create Test Modal -->
<div id="createTestModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Create New Test</h2>
            <span class="close" onclick="closeModal('createTestModal')">&times;</span>
        </div>
        <form method="POST" action="">
            <div class="form-group">
                <label for="test_name">Test Name *</label>
                <input type="text" name="test_name" id="test_name" required>
            </div>
            
            <div class="form-group">
                <label for="test_description">Test Description</label>
                <textarea name="test_description" id="test_description" rows="3"></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="duration_minutes">Duration (minutes) *</label>
                    <input type="number" name="duration_minutes" id="duration_minutes" value="30" min="5" required>
                </div>
                
                <div class="form-group">
                    <label for="passing_score">Passing Score (%) *</label>
                    <input type="number" name="passing_score" id="passing_score" value="50" min="0" max="100" required>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('createTestModal')">Cancel</button>
                <button type="submit" name="create_test" class="btn btn-primary">Create Test</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
