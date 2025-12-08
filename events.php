<?php
include 'components/head.inc.php';

// Fetch the latest 6 events from the database
$events_query = "SELECT * FROM weekly_program ORDER BY date ASC, time ASC LIMIT 6";
$events_result = $conn->query($events_query);
?>

<?php include 'components/navbar.inc.php'; ?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-calendar-alt me-3"></i>Upcoming Events</h1>
        <p>Join us for these special moments of fellowship and worship</p>
    </div>
</section>

<!-- Events Section -->
<section class="content-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="section-title">Our Next 6 Events</h2>
                <p class="section-subtitle">Mark your calendar and join us for these upcoming church events and activities</p>
            </div>
        </div>

        <?php if ($events_result && $events_result->num_rows > 0): ?>
            <div class="row">
                <?php while ($event = $events_result->fetch_assoc()): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <!-- Event Date -->
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                        <div class="text-center">
                                            <div class="fw-bold fs-5"><?php echo date('j', strtotime($event['date'])); ?></div>
                                            <div class="small"><?php echo date('M', strtotime($event['date'])); ?></div>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-muted"><?php echo date('l', strtotime($event['date'])); ?></h6>
                                        <small class="text-muted"><?php echo date('F Y', strtotime($event['date'])); ?></small>
                                    </div>
                                </div>

                                <!-- Event Title -->
                                <h5 class="card-title text-primary"><?php echo htmlspecialchars($event['title']); ?></h5>

                                <!-- Event Description -->
                                <?php if (!empty($event['description'])): ?>
                                    <p class="card-text"><?php echo htmlspecialchars($event['description']); ?></p>
                                <?php endif; ?>

                                <!-- Event Details -->
                                <div class="event-details">
                                    <?php if (!empty($event['time'])): ?>
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-clock text-secondary me-2"></i>
                                            <span class="text-muted"><?php echo date('g:i A', strtotime($event['time'])); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($event['location'])): ?>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-map-marker-alt text-secondary me-2"></i>
                                            <span class="text-muted"><?php echo htmlspecialchars($event['location']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-12 text-center">
                    <div class="py-5">
                        <i class="fas fa-calendar-plus text-muted" style="font-size: 4rem;"></i>
                        <h3 class="mt-3 text-muted">No Events Scheduled</h3>
                        <p class="text-muted">Check back soon for upcoming events and activities.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

      
    </div>
</section>

<!-- Custom Styles for Events Page -->
<style>
    .event-details {
        border-top: 1px solid #e9ecef;
        padding-top: 15px;
        margin-top: 15px;
    }
    
    .card {
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.1);
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    }
    
    .bg-primary {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
    }
    
    .text-primary {
        color: var(--primary-color) !important;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border: none;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
        transform: translateY(-2px);
    }
</style>

<?php include 'components/footer.inc.php'; ?>