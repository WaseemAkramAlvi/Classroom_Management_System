<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hero {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            padding: 20px;
        }
        
        .hero-content {
            max-width: 800px;
        }
        
        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .hero p {
            font-size: 20px;
            margin-bottom: 40px;
            opacity: 0.9;
        }
        
        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .hero-btn {
            padding: 15px 40px;
            font-size: 18px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-block;
        }
        
        .hero-btn-primary {
            background-color: white;
            color: #667eea;
        }
        
        .hero-btn-secondary {
            background-color: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .hero-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        
        .features {
            padding: 80px 20px;
            background-color: white;
        }
        
        .features-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .features h2 {
            text-align: center;
            font-size: 36px;
            margin-bottom: 50px;
            color: #333;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }
        
        .feature-card {
            text-align: center;
            padding: 30px;
            border-radius: 10px;
            background-color: #f8f9fa;
            transition: transform 0.3s;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .feature-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        
        .feature-card h3 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #333;
        }
        
        .feature-card p {
            color: #666;
            line-height: 1.6;
        }
        
        .footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 30px 20px;
        }
        
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 32px;
            }
            
            .hero p {
                font-size: 16px;
            }
            
            .hero-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Class Management System</h1>
            <p>A comprehensive platform for managing student admissions, conducting online MCQ tests, and streamlining educational administration.</p>
            <div class="hero-buttons">
                <a href="auth/login.php" class="hero-btn hero-btn-primary">Login</a>
                <a href="auth/register.php" class="hero-btn hero-btn-secondary">Register as Student</a>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="features">
        <div class="features-container">
            <h2>Key Features</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🛡️</div>
                    <h3>Admin Control</h3>
                    <p>Comprehensive admin dashboard to manage teachers, students, and monitor system activities.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">👨‍🏫</div>
                    <h3>Teacher Panel</h3>
                    <p>Create and manage MCQ tests, review student performance, and approve admissions.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🎓</div>
                    <h3>Student Portal</h3>
                    <p>Take online admission tests, view results, and track admission status in real-time.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">📝</div>
                    <h3>MCQ Tests</h3>
                    <p>Online multiple-choice question tests with automatic grading and result calculation.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Analytics</h3>
                    <p>Detailed statistics and reports on student performance and system activities.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <h3>Secure</h3>
                    <p>Role-based authentication, password hashing, and protection against SQL injection.</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 Class Management System. All rights reserved.</p>
        <p>Designed and Developed with ❤️</p>
    </footer>
</body>
</html>
