<?php
session_start();
include '../connect.php';

// Redirect if admin is not logged in
if (!isset($_SESSION['adminUserName']) || empty($_SESSION['adminUserName'])) {
    header("Location: LogInToAdmin.php");
    exit();
}

// Include GetClient.php to retrieve ClientID
include '../GetClient.php';

// Initialize variables for form values
$client_name = $phone = $email = $facebook = $icon = $background_img1 = $background_img2 = $background_img3 = $description = $title_page2 = $title_page3 = "";

// Fetch existing data if ClientID is provided
$sql = "SELECT * FROM general_elements WHERE ClientID = $clientID";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $client_name = $row['client_name'];
    $phone = $row['phone'];
    $email = $row['email'];
    $facebook = $row['facebook'];
    $icon = $row['icon'];
    $background_img1 = $row['background_img1'];
    $background_img2 = $row['background_img2'];
    $background_img3 = $row['background_img3'];
    $description = $row['description'];
    $title_page2 = $row['title_page2']; // Fetch title_page2
    $title_page3 = $row['title_page3']; // Fetch title_page3
}

// Function to handle file uploads
function uploadFile($fileInputName, $existingFile, $timestamp) {
    if ($_FILES[$fileInputName]['size'] > 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif']; // Allowed file types
        $fileType = $_FILES[$fileInputName]['type'];
        
        if (in_array($fileType, $allowedTypes)) {
            $filename = $timestamp . "_" . basename($_FILES[$fileInputName]['name']);
            move_uploaded_file($_FILES[$fileInputName]["tmp_name"], '../church/uploads/' . $filename);
            return $filename;
        } else {
            echo "Invalid file type for $fileInputName.";
            return $existingFile; // Return old file if invalid
        }
    }
    return $existingFile; // No file uploaded, return old file
}

// Process form submission for insert or update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form values and escape them
    $client_name = $conn->real_escape_string($_POST['client_name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);
    $facebook = $conn->real_escape_string($_POST['facebook']);
    $description = $conn->real_escape_string($_POST['description']);
    $title_page2 = $conn->real_escape_string($_POST['title_page2']); // New field
    $title_page3 = $conn->real_escape_string($_POST['title_page3']); // New field
    
    // Get the current timestamp for unique filenames
    $timestamp = date("YmdHis");

    // Handle file uploads
    $icon_new = uploadFile('icon', $icon, $timestamp);
    $background_img1_new = uploadFile('background_img1', $background_img1, $timestamp);
    $background_img2_new = uploadFile('background_img2', $background_img2, $timestamp);
    $background_img3_new = uploadFile('background_img3', $background_img3, $timestamp);

    // Check if record exists to determine insert or update
    if ($result->num_rows > 0) {
        // Update the database
        $sql = "UPDATE general_elements SET client_name = '$client_name', phone = '$phone', email = '$email', facebook = '$facebook', 
                icon = '$icon_new', background_img1 = '$background_img1_new', 
                background_img2 = '$background_img2_new', background_img3 = '$background_img3_new', 
                description = '$description', title_page2 = '$title_page2', title_page3 = '$title_page3' 
                WHERE ClientID = $clientID";
        $action = "updated";
    } else {
        // Insert a new record
        $sql = "INSERT INTO general_elements (ClientID, client_name, phone, email, facebook, icon, 
                background_img1, background_img2, background_img3, description, title_page2, title_page3) 
                VALUES ($clientID, '$client_name', '$phone', '$email', '$facebook', '$icon_new', 
                '$background_img1_new', '$background_img2_new', '$background_img3_new', '$description', 
                '$title_page2', '$title_page3')";
        $action = "inserted";
    }

    if ($conn->query($sql) === TRUE) {
        echo "Record successfully $action!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>General Settings - Church Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .form-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid #e1e5e9;
        }

        .form-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 25px 30px;
            text-align: center;
        }

        .form-header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .form-header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .form-body {
            padding: 30px;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f1f2f6;
        }

        .section-title i {
            color: #667eea;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
            font-size: 0.9rem;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="tel"],
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="email"]:focus,
        .form-group input[type="tel"]:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        /* Special styling for email input */
        .form-group input[type="email"] {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-left: 4px solid #28a745;
        }

        .form-group input[type="email"]:focus {
            border-left-color: #28a745;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
        }

        /* Special styling for phone input */
        .form-group input[type="tel"] {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-left: 4px solid #007bff;
        }

        .form-group input[type="tel"]:focus {
            border-left-color: #007bff;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        /* Input wrapper styling */
        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            color: #6c757d;
            z-index: 2;
            font-size: 1rem;
            transition: color 0.3s ease;
        }

        .input-wrapper input {
            padding-left: 45px !important;
        }

        .input-wrapper input:focus + .input-icon,
        .input-wrapper:focus-within .input-icon {
            color: #667eea;
        }

        /* Special icon colors for email and phone */
        .input-wrapper input[type="email"]:focus + .input-icon,
        .input-wrapper:focus-within input[type="email"] + .input-icon {
            color: #28a745;
        }

        .input-wrapper input[type="tel"]:focus + .input-icon,
        .input-wrapper:focus-within input[type="tel"] + .input-icon {
            color: #007bff;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .file-upload-group {
            position: relative;
        }

        .file-upload {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-upload input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-upload-label {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            border: 2px dashed #e1e5e9;
            border-radius: 8px;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            justify-content: center;
        }

        .file-upload-label:hover {
            border-color: #667eea;
            background: #f0f2ff;
        }

        .file-upload-label i {
            font-size: 1.2rem;
            color: #667eea;
        }

        .current-file {
            margin-top: 10px;
            padding: 10px;
            background: #e8f5e8;
            border-radius: 6px;
            font-size: 0.9rem;
            color: #2d5a2d;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .current-file i {
            color: #28a745;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e1e5e9;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: #fff;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .success-message i {
            color: #28a745;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .error-message i {
            color: #dc3545;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .form-container {
                margin: 0 15px;
            }
            
            .form-body {
                padding: 20px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include('../manger/nav.php'); ?>

    <div class="form-container">
        <div class="form-card">
            <div class="form-header">
                <h1><i class="fas fa-cog"></i> General Settings</h1>
                <p>Configure your church website's general information and appearance</p>
            </div>
            
            <div class="form-body">
                <?php if (isset($action) && $action): ?>
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i>
                        Record successfully <?php echo $action; ?>!
                    </div>
                <?php endif; ?>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-info-circle"></i>
                            Basic Information
                        </h3>
                        
                        <div class="form-group">
                            <label for="client_name">Church Name *</label>
                            <input type="text" name="client_name" value="<?php echo htmlspecialchars($client_name); ?>" required placeholder="Enter your church name">
                        </div>

                        <div class="form-group">
                            <label for="phone">
                                <i class="fas fa-phone me-2"></i>Phone Number
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-phone input-icon"></i>
                                <input type="tel" name="phone" value="<?php echo htmlspecialchars($phone); ?>" placeholder="Enter phone number">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">
                                <i class="fas fa-envelope me-2"></i>Email Address
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Enter email address">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="facebook">Facebook Page URL</label>
                            <input type="text" name="facebook" value="<?php echo htmlspecialchars($facebook); ?>" placeholder="https://facebook.com/yourchurch">
                        </div>

                        <div class="form-group">
                            <label for="description">Church Description</label>
                            <textarea name="description" placeholder="Tell visitors about your church..."><?php echo htmlspecialchars($description); ?></textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-image"></i>
                            Images & Media
                        </h3>
                        
                        <div class="form-group">
                            <label>Icon/Logo</label>
                            <div class="file-upload-group">
                                <div class="file-upload">
                                    <input type="file" name="icon" accept="image/*">
                                    <label for="icon" class="file-upload-label">
                                        <i class="fas fa-upload"></i>
                                        <span>Choose Icon File</span>
                                    </label>
                                </div>
                                <?php if ($icon): ?>
                                    <div class="current-file">
                                        <i class="fas fa-file-image"></i>
                                        Current: <?php echo htmlspecialchars($icon); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Background Image 1</label>
                                <div class="file-upload-group">
                                    <div class="file-upload">
                                        <input type="file" name="background_img1" accept="image/*">
                                        <label for="background_img1" class="file-upload-label">
                                            <i class="fas fa-upload"></i>
                                            <span>Choose Image</span>
                                        </label>
                                    </div>
                                    <?php if ($background_img1): ?>
                                        <div class="current-file">
                                            <i class="fas fa-file-image"></i>
                                            Current: <?php echo htmlspecialchars($background_img1); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Background Image 2</label>
                                <div class="file-upload-group">
                                    <div class="file-upload">
                                        <input type="file" name="background_img2" accept="image/*">
                                        <label for="background_img2" class="file-upload-label">
                                            <i class="fas fa-upload"></i>
                                            <span>Choose Image</span>
                                        </label>
                                    </div>
                                    <?php if ($background_img2): ?>
                                        <div class="current-file">
                                            <i class="fas fa-file-image"></i>
                                            Current: <?php echo htmlspecialchars($background_img2); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Background Image 3</label>
                            <div class="file-upload-group">
                                <div class="file-upload">
                                    <input type="file" name="background_img3" accept="image/*">
                                    <label for="background_img3" class="file-upload-label">
                                        <i class="fas fa-upload"></i>
                                        <span>Choose Image</span>
                                    </label>
                                </div>
                                <?php if ($background_img3): ?>
                                    <div class="current-file">
                                        <i class="fas fa-file-image"></i>
                                        Current: <?php echo htmlspecialchars($background_img3); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-heading"></i>
                            Page Titles
                        </h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="title_page2">Page 2 Title</label>
                                <input type="text" name="title_page2" value="<?php echo htmlspecialchars($title_page2); ?>" placeholder="Enter page 2 title">
                            </div>

                            <div class="form-group">
                                <label for="title_page3">Page 3 Title</label>
                                <input type="text" name="title_page3" value="<?php echo htmlspecialchars($title_page3); ?>" placeholder="Enter page 3 title">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            <?php echo $result->num_rows > 0 ? 'Update Settings' : 'Save Settings'; ?>
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Back to Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // File upload preview functionality
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                const label = this.nextElementSibling;
                
                if (file) {
                    label.innerHTML = `<i class="fas fa-check-circle"></i><span>${file.name}</span>`;
                    label.style.borderColor = '#28a745';
                    label.style.background = '#e8f5e8';
                }
            });
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#dc3545';
                    isValid = false;
                } else {
                    field.style.borderColor = '#e1e5e9';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    </script>
</body>
</html>
