<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireLogin('admin');

$page_title = 'Manage Teachers';

// Handle actions
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $teacher_id = (int)$_POST['teacher_id'];
    
    if ($action == 'activate') {
        $conn->query("UPDATE teachers SET status = 'active' WHERE teacher_id = $teacher_id");
        setFlashMessage('success', 'Teacher activated successfully');
    } elseif ($action == 'deactivate') {
        $conn->query("UPDATE teachers SET status = 'inactive' WHERE teacher_id = $teacher_id");
        setFlashMessage('success', 'Teacher deactivated successfully');
    } elseif ($action == 'delete') {
        $conn->query("DELETE FROM teachers WHERE teacher_id = $teacher_id");
        setFlashMessage('success', 'Teacher deleted successfully');
    }
    
    redirect('teachers.php');
}

// Handle adding new teacher
if (isset($_POST['add_teacher'])) {
    $full_name = sanitize($_POST['full_name']);
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $subject = sanitize($_POST['subject']);
    $phone = sanitize($_POST['phone']);
    
    $errors = [];
    
    if (usernameExists($conn, $username, 'teachers')) {
        $errors[] = 'Username already exists';
    }
    
    if (emailExists($conn, $email, 'teachers')) {
        $errors[] = 'Email already exists';
    }
    
    if (empty($errors)) {
        $hashed_password = hashPassword($password);
        $full_name = $conn->real_escape_string($full_name);
        $username = $conn->real_escape_string($username);
        $email = $conn->real_escape_string($email);
        $subject = $conn->real_escape_string($subject);
        $phone = $conn->real_escape_string($phone);
        
        $query = "INSERT INTO teachers (username, email, password, full_name, subject, phone, status) 
                  VALUES ('$username', '$email', '$hashed_password', '$full_name', '$subject', '$phone', 'active')";
        
        if ($conn->query($query)) {
            setFlashMessage('success', 'Teacher added successfully');
            redirect('teachers.php');
        } else {
            setFlashMessage('error', 'Failed to add teacher');
        }
    } else {
        setFlashMessage('error', implode(', ', $errors));
    }
}

// Get all teachers
$teachers_query = "SELECT * FROM teachers ORDER BY created_at DESC";
$teachers = $conn->query($teachers_query);

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
            <a href="teachers.php" class="nav-item active">
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
            <h1>Manage Teachers</h1>
            <button class="btn btn-primary" onclick="openModal('addTeacherModal')">Add New Teacher</button>
        </div>
        
        <div class="section">
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($teachers->num_rows > 0): ?>
                            <?php while($teacher = $teachers->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $teacher['teacher_id']; ?></td>
                                <td><?php echo escape($teacher['full_name']); ?></td>
                                <td><?php echo escape($teacher['username']); ?></td>
                                <td><?php echo escape($teacher['email']); ?></td>
                                <td><?php echo escape($teacher['phone']); ?></td>
                                <td><?php echo escape($teacher['subject']); ?></td>
                                <td>
                                    <?php if ($teacher['status'] == 'active'): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo formatDate($teacher['created_at']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if ($teacher['status'] == 'active'): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="deactivate">
                                                <input type="hidden" name="teacher_id" value="<?php echo $teacher['teacher_id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-warning" 
                                                        onclick="return confirm('Deactivate this teacher?')">Deactivate</button>
                                            </form>
                                        <?php else: ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="activate">
                                                <input type="hidden" name="teacher_id" value="<?php echo $teacher['teacher_id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-success">Activate</button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="teacher_id" value="<?php echo $teacher['teacher_id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Delete this teacher? This action cannot be undone.')">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">No teachers found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Add Teacher Modal -->
<div id="addTeacherModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add New Teacher</h2>
            <span class="close" onclick="closeModal('addTeacherModal')">&times;</span>
        </div>
        <form method="POST" action="">
            <div class="form-group">
                <label for="full_name">Full Name *</label>
                <input type="text" name="full_name" id="full_name" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="username">Username *</label>
                    <input type="text" name="username" id="username" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" name="email" id="email" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" name="password" id="password" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" name="phone" id="phone">
                </div>
            </div>
            
            <div class="form-group">
                <label for="subject">Subject *</label>
                <input type="text" name="subject" id="subject" required>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addTeacherModal')">Cancel</button>
                <button type="submit" name="add_teacher" class="btn btn-primary">Add Teacher</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
