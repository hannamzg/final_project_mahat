<?php
session_start();
if (!isset($_SESSION['adminUserName']) || empty($_SESSION['adminUserName'])) {
    header("Location: LogInToAdmin.php");
    exit();
}

include '../connect.php';
include '../GetClient.php';

// Get statistics
$stats = [];

// Count total content
$content_count = $conn->query("SELECT COUNT(*) as count FROM content WHERE client_id = $clientID")->fetch_assoc()['count'];
$stats['content'] = $content_count;

// Count total classes
$class_count = $conn->query("SELECT COUNT(*) as count FROM class WHERE client_id = $clientID")->fetch_assoc()['count'];
$stats['classes'] = $class_count;

// Count total slider images
$slider_count = $conn->query("SELECT COUNT(*) as count FROM mainsilderimg WHERE client_id = $clientID")->fetch_assoc()['count'];
$stats['slider_images'] = $slider_count;

// Count total class content
$class_content_count = $conn->query("SELECT COUNT(*) as count FROM classpage WHERE client_id = $clientID")->fetch_assoc()['count'];
$stats['class_content'] = $class_content_count;

// Count total products
$product_count = $conn->query("SELECT COUNT(*) as count FROM products WHERE client_id = $clientID")->fetch_assoc()['count'];
$stats['products'] = $product_count;

// Get recent activities (last 5 content items)
$recent_content = $conn->query("SELECT title, created_at FROM content WHERE client_id = $clientID ORDER BY created_at DESC LIMIT 5");
$recent_activities = [];
while ($row = $recent_content->fetch_assoc()) {
    $recent_activities[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <title>Dashboard - Church Management</title>
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e1e5e9;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-card.primary::before {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-card.success::before {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
        }

        .stat-card.warning::before {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stat-card.info::before {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #fff;
        }

        .stat-icon.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-icon.success {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
        }

        .stat-icon.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stat-icon.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
        }

        .stat-label {
            color: #7f8c8d;
            font-size: 0.9rem;
            font-weight: 500;
            margin: 0;
        }

        .quick-actions {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e1e5e9;
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: #667eea;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 20px;
            background: #f8f9fa;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            text-decoration: none;
            color: #2c3e50;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            background: #667eea;
            color: #fff;
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .action-btn i {
            font-size: 1.2rem;
        }

        .recent-activities {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e1e5e9;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #f1f2f6;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 0.9rem;
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .activity-time {
            font-size: 0.8rem;
            color: #7f8c8d;
        }

        .welcome-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
        }

        .welcome-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .welcome-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .actions-grid {
                grid-template-columns: 1fr;
            }
            
            .welcome-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include('../manger/nav.php'); ?>

    <div class="dashboard-container">
        <div class="welcome-section">
            <h1 class="welcome-title">Welcome to Admin Dashboard</h1>
            <p class="welcome-subtitle">Manage your church content and settings efficiently</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-header">
                    <div class="stat-icon primary">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
                <h3 class="stat-number"><?php echo $stats['content']; ?></h3>
                <p class="stat-label">Total Content Items</p>
            </div>

            <div class="stat-card success">
                <div class="stat-header">
                    <div class="stat-icon success">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                </div>
                <h3 class="stat-number"><?php echo $stats['classes']; ?></h3>
                <p class="stat-label">Active Classes</p>
            </div>

            <div class="stat-card warning">
                <div class="stat-header">
                    <div class="stat-icon warning">
                        <i class="fas fa-images"></i>
                    </div>
                </div>
                <h3 class="stat-number"><?php echo $stats['slider_images']; ?></h3>
                <p class="stat-label">Slider Images</p>
            </div>

            <div class="stat-card info">
                <div class="stat-header">
                    <div class="stat-icon info">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
                <h3 class="stat-number"><?php echo $stats['class_content']; ?></h3>
                <p class="stat-label">Class Content Items</p>
            </div>

            <div class="stat-card success">
                <div class="stat-header">
                    <div class="stat-icon success">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                </div>
                <h3 class="stat-number"><?php echo $stats['products']; ?></h3>
                <p class="stat-label">Products</p>
            </div>
        </div>

        <div class="quick-actions">
            <h2 class="section-title">
                <i class="fas fa-bolt"></i>
                Quick Actions
            </h2>
            <div class="actions-grid">
                <a href="AddToMainImgSilder.php" class="action-btn">
                    <i class="fas fa-images"></i>
                    <span>Add Slider Image</span>
                </a>
                <a href="mangeText.php" class="action-btn">
                    <i class="fas fa-edit"></i>
                    <span>Manage Content</span>
                </a>
                <a href="classMange.php" class="action-btn">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Class Management</span>
                </a>
                <a href="AddToQuestions.php" class="action-btn">
                    <i class="fas fa-question-circle"></i>
                    <span>Add Questions</span>
                </a>
                <a href="calendarMange.php" class="action-btn">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Calendar Events</span>
                </a>
                <a href="productManage.php" class="action-btn">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Product Management</span>
                </a>
                <a href="SetGeneral.php" class="action-btn">
                    <i class="fas fa-cog"></i>
                    <span>General Settings</span>
                </a>
            </div>
        </div>

        <div class="recent-activities">
            <h2 class="section-title">
                <i class="fas fa-clock"></i>
                Recent Activities
            </h2>
            <?php if (!empty($recent_activities)): ?>
                <?php foreach ($recent_activities as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title"><?php echo htmlspecialchars($activity['title']); ?></div>
                            <div class="activity-time">
                                <?php 
                                if (isset($activity['created_at'])) {
                                    echo date('M j, Y g:i A', strtotime($activity['created_at']));
                                } else {
                                    echo 'Recently added';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">No recent activities</div>
                        <div class="activity-time">Start by adding some content</div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Add some interactive animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate stat cards on load
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'all 0.6s ease';
                    
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 100);
                }, index * 200);
            });
        });
    </script>
</body>
</html>
