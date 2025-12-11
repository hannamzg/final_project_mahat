<?php require('components/head.inc.php'); ?>
<?php include('components/navbar.inc.php'); ?>

<?php
// Get page data from pages table (page = 4 for Contact)
$page_query = "SELECT * FROM pages WHERE id = 4";
$page_result = $conn->query($page_query);
$page_data = $page_result->fetch_assoc();

// Get content data for contact page
$content_query = "SELECT * FROM content WHERE pageID = 4 ORDER BY id ASC";
$content_result = $conn->query($content_query);
$content_items = [];
while($row = $content_result->fetch_assoc()) {
    $content_items[] = $row;
}

// Handle form submission
$message_sent = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get raw values 
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Basic validation
    if ($name === '' || $email === '' || $message === '') {
        $error_message = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        // Save to database
        $stmt = $conn->prepare("
            INSERT INTO contact_messages (name, email, phone, subject, message)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        if ($stmt) {
            $stmt->bind_param(
                "sssss",
                $name,
                $email,
                $phone,
                $subject,
                $message
            );
            
            if ($stmt->execute()) {
                $message_sent = true;
                // Optional: clear form values after success
                $name = $email = $phone = $subject = $message = '';
            } else {
                $error_message = 'There was a problem sending your message. Please try again later.';
            }
            
            $stmt->close();
        } else {
            $error_message = 'There was a problem with the server. Please try again later.';
        }
    }
}

?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1><?php echo isset($page_data['pageName']) ? htmlspecialchars($page_data['pageName']) : 'Contact Us'; ?></h1>
        <p>We'd love to hear from you. Get in touch with us today!</p>
    </div>
</section>

<!-- Contact Content Section -->
<?php if(!empty($content_items)): ?>
    <section class="content-section">
        <div class="container">
            <?php foreach($content_items as $item): ?>
                <div class="row align-items-center mb-5">
                    <div class="col-lg-6">
                        <div class="content-text">
                            <h2 class="mb-4"><?php echo htmlspecialchars($item['title']); ?></h2>
                            <div class="content-body">
                                <?php echo nl2br(htmlspecialchars($item['content'])); ?>
                            </div>
                            <?php if(!empty($item['link']) && !empty($item['linkText'])): ?>
                                <a href="<?php echo htmlspecialchars($item['link']); ?>" class="btn btn-primary mt-3">
                                    <?php echo htmlspecialchars($item['linkText']); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <?php if(!empty($item['img'])): ?>
                            <div class="content-image">
                                <img src="church/uploads/<?php echo htmlspecialchars($item['img']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="img-fluid rounded-3 shadow">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<!-- Contact Form and Info Section -->
<section class="content-section bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="section-title">Get In Touch</h2>
                <p class="section-subtitle">We're here to help and answer any questions you might have</p>
            </div>
        </div>
        
        <div class="row g-5">
            <!-- Contact Form -->
            <div class="col-lg-8">
                <div class="contact-form-wrapper">
                    <?php if($message_sent): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            Thank you for your message! We'll get back to you soon.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($error_message): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo $error_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="contact-form">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                            </div>
                            <div class="col-md-6">
                                <label for="subject" class="form-label">Subject</label>
                                <select class="form-select" id="subject" name="subject">
                                    <option value="">Select a subject</option>
                                    <option value="General Inquiry">General Inquiry</option>
                                    <option value="Prayer Request">Prayer Request</option>
                                    <option value="Event Information">Event Information</option>
                                    <option value="Volunteer Opportunities">Volunteer Opportunities</option>
                                    <option value="Pastoral Care">Pastoral Care</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="message" class="form-label">Message *</label>
                                <textarea class="form-control" id="message" name="message" rows="6" required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="col-lg-4">
                <div class="contact-info-card">
                    <h4 class="mb-4">Contact Information</h4>
                    
                    <div class="contact-item mb-4">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h6>Address</h6>
                            <p>123 Church Street<br>City, State 12345</p>
                        </div>
                    </div>
                    
                    <div class="contact-item mb-4">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-details">
                            <h6>Phone</h6>
                            <p>
                                <?php if(isset($general_data['phone']) && !empty($general_data['phone'])): ?>
                                    <a href="tel:<?php echo htmlspecialchars($general_data['phone']); ?>">
                                        <?php echo htmlspecialchars($general_data['phone']); ?>
                                    </a>
                                <?php else: ?>
                                    <a href="tel:+1234567890">(123) 456-7890</a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="contact-item mb-4">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h6>Email</h6>
                            <p>
                                <?php if(isset($general_data['email']) && !empty($general_data['email'])): ?>
                                    <a href="mailto:<?php echo htmlspecialchars($general_data['email']); ?>">
                                        <?php echo htmlspecialchars($general_data['email']); ?>
                                    </a>
                                <?php else: ?>
                                    <a href="mailto:info@church.com">info@church.com</a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="contact-item mb-4">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-details">
                            <h6>Office Hours</h6>
                            <p>
                                Monday - Friday: 9:00 AM - 5:00 PM<br>
                                Saturday: 10:00 AM - 2:00 PM<br>
                                Sunday: 8:00 AM - 1:00 PM
                            </p>
                        </div>
                    </div>
                    
                    <!-- Social Media Links -->
                    <div class="social-links mt-4">
                        <h6 class="mb-3">Follow Us</h6>
                        <div class="social-buttons">
                            <?php if(isset($general_data['facebook']) && !empty($general_data['facebook'])): ?>
                                <a href="<?php echo htmlspecialchars($general_data['facebook']); ?>" target="_blank" class="btn btn-outline-primary me-2 mb-2">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            <?php endif; ?>
                            <a href="#" class="btn btn-outline-primary me-2 mb-2">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="btn btn-outline-primary me-2 mb-2">
                                <i class="fab fa-youtube"></i>
                            </a>
                            <a href="#" class="btn btn-outline-primary me-2 mb-2">
                                <i class="fab fa-twitter"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="content-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="section-title">Find Us</h2>
                <p class="section-subtitle">Visit us at our church location</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="map-wrapper">
                    <div class="map-placeholder">
                        <i class="fas fa-map-marked-alt fa-3x text-primary mb-3"></i>
                        <h5>Interactive Map</h5>
                        <p>123 Church Street, City, State 12345</p>
                        <a href="https://maps.google.com" target="_blank" class="btn btn-primary">
                            <i class="fas fa-directions me-2"></i>Get Directions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Custom Contact Styles -->
<style>
    .contact-form-wrapper {
        background: white;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .contact-info-card {
        background: white;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        height: fit-content;
    }
    
    .contact-item {
        display: flex;
        align-items: flex-start;
    }
    
    .contact-icon {
        width: 50px;
        height: 50px;
        background: var(--primary-color);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        flex-shrink: 0;
    }
    
    .contact-details h6 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .contact-details p {
        margin: 0;
        color: var(--text-dark);
    }
    
    .contact-details a {
        color: var(--secondary-color);
        text-decoration: none;
    }
    
    .contact-details a:hover {
        color: var(--primary-color);
    }
    
    .social-buttons .btn {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .map-wrapper {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 60px 40px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .map-placeholder {
        color: var(--text-light);
    }
    
    .form-control, .form-select {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        padding: 12px 15px;
        transition: border-color 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }
    
    .form-label {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 8px;
    }
    
    @media (max-width: 768px) {
        .contact-form-wrapper,
        .contact-info-card {
            padding: 30px 20px;
        }
        
        .map-wrapper {
            padding: 40px 20px;
        }
    }
</style>

<?php require('components/footer.inc.php'); ?>
