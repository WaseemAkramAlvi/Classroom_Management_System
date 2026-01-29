<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireLogin('student');

$page_title = 'Take Test';

$student_id = $_SESSION['user_id'];

// Check if student already took a test
$check_query = "SELECT * FROM student_tests WHERE student_id = $student_id";
$check_result = $conn->query($check_query);

if ($check_result->num_rows > 0) {
    setFlashMessage('error', 'You have already taken a test');
    redirect('dashboard.php');
}

// Get test_id from URL or session
if (isset($_GET['test_id'])) {
    $_SESSION['current_test_id'] = (int)$_GET['test_id'];
}

if (!isset($_SESSION['current_test_id'])) {
    setFlashMessage('error', 'No test selected');
    redirect('dashboard.php');
}

$test_id = $_SESSION['current_test_id'];

// Get test details
$test_query = "SELECT * FROM tests WHERE test_id = $test_id AND status = 'published'";
$test_result = $conn->query($test_query);

if ($test_result->num_rows == 0) {
    setFlashMessage('error', 'Test not found or not available');
    unset($_SESSION['current_test_id']);
    redirect('dashboard.php');
}

$test = $test_result->fetch_assoc();

// Get questions
$questions_query = "SELECT * FROM questions WHERE test_id = $test_id ORDER BY question_id";
$questions = $conn->query($questions_query);

// Check if test is being started
if (isset($_POST['start_test'])) {
    // Create student_test record
    $query = "INSERT INTO student_tests (student_id, test_id, status, start_time, total_marks) 
              VALUES ($student_id, $test_id, 'in_progress', NOW(), {$test['total_marks']})";
    
    if ($conn->query($query)) {
        $_SESSION['student_test_id'] = $conn->insert_id;
        $_SESSION['test_start_time'] = time();
        redirect('take_test.php');
    }
}

// Check if test is in progress
if (!isset($_SESSION['student_test_id'])) {
    // Show test instructions
    include '../includes/header.php';
    ?>
    
    <div class="test-container">
        <div class="test-instructions">
            <h1><?php echo escape($test['test_name']); ?></h1>
            <p class="test-description"><?php echo escape($test['test_description']); ?></p>
            
            <div class="test-info">
                <h2>Test Instructions</h2>
                <ul>
                    <li>Total Questions: <?php echo $questions->num_rows; ?></li>
                    <li>Total Marks: <?php echo $test['total_marks']; ?></li>
                    <li>Duration: <?php echo $test['duration_minutes']; ?> minutes</li>
                    <li>Passing Score: <?php echo $test['passing_score']; ?>%</li>
                </ul>
                
                <h3>Important Guidelines:</h3>
                <ul>
                    <li>Read each question carefully before answering</li>
                    <li>Select only one option for each question</li>
                    <li>You cannot go back once you submit the test</li>
                    <li>Make sure to complete the test within the time limit</li>
                    <li>Click "Submit Test" button when you are done</li>
                </ul>
            </div>
            
            <form method="POST" action="">
                <button type="submit" name="start_test" class="btn btn-primary btn-large">
                    Start Test Now
                </button>
                <a href="dashboard.php" class="btn btn-secondary btn-large">Cancel</a>
            </form>
        </div>
    </div>
    
    <?php
    include '../includes/footer.php';
    exit;
}

// Test is in progress - handle submission
if (isset($_POST['submit_test'])) {
    $student_test_id = $_SESSION['student_test_id'];
    $total_score = 0;
    
    // Process answers
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'question_') === 0) {
            $question_id = (int)str_replace('question_', '', $key);
            $selected_answer = sanitize($value);
            
            // Get correct answer
            $q_query = "SELECT correct_answer, marks FROM questions WHERE question_id = $question_id";
            $q_result = $conn->query($q_query);
            $question = $q_result->fetch_assoc();
            
            $is_correct = ($selected_answer == $question['correct_answer']) ? 1 : 0;
            $marks_obtained = $is_correct ? $question['marks'] : 0;
            $total_score += $marks_obtained;
            
            // Insert answer
            $conn->query("INSERT INTO student_answers (student_test_id, question_id, selected_answer, is_correct, marks_obtained) 
                         VALUES ($student_test_id, $question_id, '$selected_answer', $is_correct, $marks_obtained)");
        }
    }
    
    // Calculate percentage and result
    $percentage = calculatePercentage($total_score, $test['total_marks']);
    $result = ($percentage >= $test['passing_score']) ? 'passed' : 'failed';
    
    // Update student_test
    $conn->query("UPDATE student_tests 
                  SET score = $total_score, percentage = $percentage, result = '$result', 
                      status = 'completed', end_time = NOW() 
                  WHERE student_test_id = $student_test_id");
    
    // Update student record
    $conn->query("UPDATE students SET test_taken = 1 WHERE student_id = $student_id");
    
    // Create admission record
    $conn->query("INSERT INTO admissions (student_id, student_test_id, status) 
                  VALUES ($student_id, $student_test_id, 'pending')");
    
    // Clear session
    unset($_SESSION['student_test_id']);
    unset($_SESSION['current_test_id']);
    unset($_SESSION['test_start_time']);
    
    setFlashMessage('success', 'Test submitted successfully!');
    redirect('results.php');
}

// Calculate remaining time
$elapsed_time = time() - $_SESSION['test_start_time'];
$remaining_time = ($test['duration_minutes'] * 60) - $elapsed_time;

if ($remaining_time <= 0) {
    // Auto submit test
    setFlashMessage('error', 'Time expired! Test auto-submitted.');
    // You can add auto-submit logic here
    unset($_SESSION['student_test_id']);
    unset($_SESSION['current_test_id']);
    unset($_SESSION['test_start_time']);
    redirect('dashboard.php');
}

include '../includes/header.php';
?>

<div class="test-container">
    <div class="test-header">
        <h1><?php echo escape($test['test_name']); ?></h1>
        <div class="timer" id="timer">
            <span id="time-remaining"><?php echo gmdate("i:s", $remaining_time); ?></span>
        </div>
    </div>
    
    <form method="POST" action="" id="testForm">
        <div class="questions-container">
            <?php 
            $qno = 1;
            $questions->data_seek(0); // Reset pointer
            while($question = $questions->fetch_assoc()): 
            ?>
            <div class="question-box">
                <h3>Question <?php echo $qno++; ?> (<?php echo $question['marks']; ?> mark<?php echo $question['marks'] > 1 ? 's' : ''; ?>)</h3>
                <p class="question-text"><?php echo escape($question['question_text']); ?></p>
                
                <div class="options-list">
                    <label class="option-label">
                        <input type="radio" name="question_<?php echo $question['question_id']; ?>" 
                               value="A" required>
                        <span>A) <?php echo escape($question['option_a']); ?></span>
                    </label>
                    
                    <label class="option-label">
                        <input type="radio" name="question_<?php echo $question['question_id']; ?>" 
                               value="B" required>
                        <span>B) <?php echo escape($question['option_b']); ?></span>
                    </label>
                    
                    <label class="option-label">
                        <input type="radio" name="question_<?php echo $question['question_id']; ?>" 
                               value="C" required>
                        <span>C) <?php echo escape($question['option_c']); ?></span>
                    </label>
                    
                    <label class="option-label">
                        <input type="radio" name="question_<?php echo $question['question_id']; ?>" 
                               value="D" required>
                        <span>D) <?php echo escape($question['option_d']); ?></span>
                    </label>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        <div class="test-footer">
            <button type="submit" name="submit_test" class="btn btn-success btn-large" 
                    onclick="return confirm('Are you sure you want to submit the test? You cannot change your answers after submission.')">
                Submit Test
            </button>
        </div>
    </form>
</div>

<script>
// Timer countdown
let remainingSeconds = <?php echo $remaining_time; ?>;
const timerElement = document.getElementById('time-remaining');

const countdown = setInterval(function() {
    remainingSeconds--;
    
    if (remainingSeconds <= 0) {
        clearInterval(countdown);
        alert('Time is up! The test will be auto-submitted.');
        document.getElementById('testForm').submit();
    }
    
    const minutes = Math.floor(remainingSeconds / 60);
    const seconds = remainingSeconds % 60;
    timerElement.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    
    // Change color when time is running out
    if (remainingSeconds <= 60) {
        timerElement.style.color = '#f44336';
    }
}, 1000);
</script>

<?php include '../includes/footer.php'; ?>
