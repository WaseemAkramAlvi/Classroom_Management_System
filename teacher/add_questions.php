<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireLogin('teacher');

$page_title = 'Add Questions';

$teacher_id = $_SESSION['user_id'];

// Get test_id from URL
if (!isset($_GET['test_id'])) {
    setFlashMessage('error', 'Test ID is required');
    redirect('tests.php');
}

$test_id = (int)$_GET['test_id'];

// Verify test belongs to teacher
$test_query = "SELECT * FROM tests WHERE test_id = $test_id AND teacher_id = $teacher_id";
$test_result = $conn->query($test_query);

if ($test_result->num_rows == 0) {
    setFlashMessage('error', 'Test not found');
    redirect('tests.php');
}

$test = $test_result->fetch_assoc();

// Handle adding question
if (isset($_POST['add_question'])) {
    $question_text = sanitize($_POST['question_text']);
    $option_a = sanitize($_POST['option_a']);
    $option_b = sanitize($_POST['option_b']);
    $option_c = sanitize($_POST['option_c']);
    $option_d = sanitize($_POST['option_d']);
    $correct_answer = sanitize($_POST['correct_answer']);
    $marks = (int)$_POST['marks'];
    
    $question_text = $conn->real_escape_string($question_text);
    $option_a = $conn->real_escape_string($option_a);
    $option_b = $conn->real_escape_string($option_b);
    $option_c = $conn->real_escape_string($option_c);
    $option_d = $conn->real_escape_string($option_d);
    
    $query = "INSERT INTO questions (test_id, question_text, option_a, option_b, option_c, option_d, correct_answer, marks) 
              VALUES ($test_id, '$question_text', '$option_a', '$option_b', '$option_c', '$option_d', '$correct_answer', $marks)";
    
    if ($conn->query($query)) {
        // Update total marks of test
        $conn->query("UPDATE tests SET total_marks = (SELECT SUM(marks) FROM questions WHERE test_id = $test_id) WHERE test_id = $test_id");
        setFlashMessage('success', 'Question added successfully');
        redirect('add_questions.php?test_id=' . $test_id);
    } else {
        setFlashMessage('error', 'Failed to add question');
    }
}

// Handle delete question
if (isset($_POST['delete_question'])) {
    $question_id = (int)$_POST['question_id'];
    $conn->query("DELETE FROM questions WHERE question_id = $question_id");
    $conn->query("UPDATE tests SET total_marks = (SELECT SUM(marks) FROM questions WHERE test_id = $test_id) WHERE test_id = $test_id");
    setFlashMessage('success', 'Question deleted successfully');
    redirect('add_questions.php?test_id=' . $test_id);
}

// Get all questions for this test
$questions_query = "SELECT * FROM questions WHERE test_id = $test_id ORDER BY question_id";
$questions = $conn->query($questions_query);

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
            <h1>Add Questions - <?php echo escape($test['test_name']); ?></h1>
            <div>
                <button class="btn btn-primary" onclick="openModal('addQuestionModal')">Add New Question</button>
                <a href="tests.php" class="btn btn-secondary">Back to Tests</a>
            </div>
        </div>
        
        <div class="section">
            <div class="info-box">
                <p><strong>Test Name:</strong> <?php echo escape($test['test_name']); ?></p>
                <p><strong>Duration:</strong> <?php echo $test['duration_minutes']; ?> minutes</p>
                <p><strong>Total Marks:</strong> <?php echo $test['total_marks']; ?></p>
                <p><strong>Status:</strong> <span class="badge badge-<?php echo $test['status'] == 'published' ? 'success' : 'warning'; ?>"><?php echo ucfirst($test['status']); ?></span></p>
            </div>
        </div>
        
        <div class="section">
            <h2>Questions (<?php echo $questions->num_rows; ?>)</h2>
            
            <?php if ($questions->num_rows > 0): ?>
                <?php $qno = 1; ?>
                <?php while($question = $questions->fetch_assoc()): ?>
                <div class="question-card">
                    <div class="question-header">
                        <h3>Question <?php echo $qno++; ?> (<?php echo $question['marks']; ?> mark<?php echo $question['marks'] > 1 ? 's' : ''; ?>)</h3>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="question_id" value="<?php echo $question['question_id']; ?>">
                            <button type="submit" name="delete_question" class="btn btn-sm btn-danger" 
                                    onclick="return confirm('Delete this question?')">Delete</button>
                        </form>
                    </div>
                    <p class="question-text"><?php echo escape($question['question_text']); ?></p>
                    <div class="options">
                        <div class="option <?php echo $question['correct_answer'] == 'A' ? 'correct' : ''; ?>">
                            <strong>A)</strong> <?php echo escape($question['option_a']); ?>
                            <?php if($question['correct_answer'] == 'A') echo ' ✓'; ?>
                        </div>
                        <div class="option <?php echo $question['correct_answer'] == 'B' ? 'correct' : ''; ?>">
                            <strong>B)</strong> <?php echo escape($question['option_b']); ?>
                            <?php if($question['correct_answer'] == 'B') echo ' ✓'; ?>
                        </div>
                        <div class="option <?php echo $question['correct_answer'] == 'C' ? 'correct' : ''; ?>">
                            <strong>C)</strong> <?php echo escape($question['option_c']); ?>
                            <?php if($question['correct_answer'] == 'C') echo ' ✓'; ?>
                        </div>
                        <div class="option <?php echo $question['correct_answer'] == 'D' ? 'correct' : ''; ?>">
                            <strong>D)</strong> <?php echo escape($question['option_d']); ?>
                            <?php if($question['correct_answer'] == 'D') echo ' ✓'; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <p>No questions added yet. Click "Add New Question" to get started.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- Add Question Modal -->
<div id="addQuestionModal" class="modal">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h2>Add New Question</h2>
            <span class="close" onclick="closeModal('addQuestionModal')">&times;</span>
        </div>
        <form method="POST" action="">
            <div class="form-group">
                <label for="question_text">Question *</label>
                <textarea name="question_text" id="question_text" rows="3" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="option_a">Option A *</label>
                <input type="text" name="option_a" id="option_a" required>
            </div>
            
            <div class="form-group">
                <label for="option_b">Option B *</label>
                <input type="text" name="option_b" id="option_b" required>
            </div>
            
            <div class="form-group">
                <label for="option_c">Option C *</label>
                <input type="text" name="option_c" id="option_c" required>
            </div>
            
            <div class="form-group">
                <label for="option_d">Option D *</label>
                <input type="text" name="option_d" id="option_d" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="correct_answer">Correct Answer *</label>
                    <select name="correct_answer" id="correct_answer" required>
                        <option value="">Select correct answer</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="marks">Marks *</label>
                    <input type="number" name="marks" id="marks" value="1" min="1" required>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addQuestionModal')">Cancel</button>
                <button type="submit" name="add_question" class="btn btn-primary">Add Question</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
