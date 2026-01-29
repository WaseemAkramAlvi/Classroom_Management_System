<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireLogin('admin');

$page_title = 'Manage Students';

// Get all students
$students_query = "SELECT s.*, st.score, st.percentage, st.result 
                   FROM students s
                   LEFT JOIN student_tests st ON s.student_id = st.student_id
                   ORDER BY s.created_at DESC";
$students = $conn->query($students_query);

include '../includes/header.php';
?>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Admin Panel</h2>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item">
                <span class="icon">📊</span>
                <span>Dashboard</span>
            </a>
            <a href="teachers.php" class="nav-item">
                <span class="icon">👨‍🏫</span>
                <span>Manage Teachers</span>
            </a>
            <a href="students.php" class="nav-item active">
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
            <h1>Manage Students</h1>
        </div>
        
        <div class="section">
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Date of Birth</th>
                            <th>Test Status</th>
                            <th>Score</th>
                            <th>Result</th>
                            <th>Admission Status</th>
                            <th>Registered At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($students->num_rows > 0): ?>
                            <?php while($student = $students->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $student['student_id']; ?></td>
                                <td><?php echo escape($student['full_name']); ?></td>
                                <td><?php echo escape($student['email']); ?></td>
                                <td><?php echo escape($student['phone']); ?></td>
                                <td><?php echo formatDate($student['date_of_birth']); ?></td>
                                <td>
                                    <?php if ($student['test_taken']): ?>
                                        <span class="badge badge-success">Completed</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Not Taken</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    if ($student['test_taken'] && $student['score'] !== null) {
                                        echo $student['score'] . ' / ' . $student['percentage'] . '%';
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    if ($student['test_taken'] && $student['result']) {
                                        $result = $student['result'];
                                        $badge_class = '';
                                        switch($result) {
                                            case 'passed':
                                                $badge_class = 'badge-success';
                                                break;
                                            case 'failed':
                                                $badge_class = 'badge-danger';
                                                break;
                                            default:
                                                $badge_class = 'badge-warning';
                                        }
                                        echo '<span class="badge ' . $badge_class . '">' . ucfirst($result) . '</span>';
                                    } else {
                                        echo '-';
                                    }
                                    ?>
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
                                <td colspan="10" class="text-center">No students found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
