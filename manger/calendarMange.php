<?php

session_start();
include '../connect.php';

// Redirect if admin is not logged in
if (!isset($_SESSION['adminUserName']) || empty($_SESSION['adminUserName'])) {
    header("Location: LogInToAdmin.php");
    exit();
}

$success_message = '';
$error_message = '';

// Function to add a program item
if (isset($_POST['add'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $date = $conn->real_escape_string($_POST['date']);
    $time = $conn->real_escape_string($_POST['time']);
    $location = $conn->real_escape_string($_POST['location']);
    
    // Debug: Log the values being inserted
    error_log("Adding event - Title: $title, Date: $date, Time: $time, Location: $location");
    
    $stmt = $conn->prepare("INSERT INTO weekly_program (title, description, date, time, location) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        $error_message = "Prepare failed: " . $conn->error;
        error_log("SQL Prepare Error: " . $conn->error);
    } else {
        $stmt->bind_param("sssss", $title, $description, $date, $time, $location);

        if ($stmt->execute()) {
            $success_message = "Event added successfully!";
            error_log("Event added successfully with ID: " . $conn->insert_id);
        } else {
            $error_message = "Error adding program item: " . $stmt->error;
            error_log("SQL Execute Error: " . $stmt->error);
        }
        $stmt->close();
    }
}

// Function to update a program item
if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $date = $conn->real_escape_string($_POST['date']);
    $time = $conn->real_escape_string($_POST['time']);
    $location = $conn->real_escape_string($_POST['location']);

    // Debug: Log the values being updated
    error_log("Updating event ID: $id - Title: $title, Date: $date, Time: $time, Location: $location");

    $stmt = $conn->prepare("UPDATE weekly_program SET title=?, description=?, date=?, time=?, location=? WHERE id=?");
    if (!$stmt) {
        $error_message = "Prepare failed: " . $conn->error;
        error_log("SQL Prepare Error: " . $conn->error);
    } else {
        $stmt->bind_param("sssssi", $title, $description, $date, $time, $location, $id);

        if ($stmt->execute()) {
            $success_message = "Event updated successfully!";
            error_log("Event updated successfully. Affected rows: " . $stmt->affected_rows);
        } else {
            $error_message = "Error updating program item: " . $stmt->error;
            error_log("SQL Execute Error: " . $stmt->error);
        }
        $stmt->close();
    }
}

// Function to delete a program item
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Debug: Log the deletion attempt
    error_log("Attempting to delete event with ID: $id");

    $stmt = $conn->prepare("DELETE FROM weekly_program WHERE id=?");
    if (!$stmt) {
        $error_message = "Prepare failed: " . $conn->error;
        error_log("SQL Prepare Error: " . $conn->error);
    } else {
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $success_message = "Event deleted successfully!";
            error_log("Event deleted successfully. Affected rows: " . $stmt->affected_rows);
        } else {
            $error_message = "Error deleting program item: " . $stmt->error;
            error_log("SQL Execute Error: " . $stmt->error);
        }
        $stmt->close();
    }
}

// Fetch program data
$result = $conn->query("SELECT * FROM weekly_program ORDER BY date, time");
if (!$result) {
    error_log("Error fetching program data: " . $conn->error);
    $error_message = "Error loading events: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar Management - Church Management </title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .debug-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            font-family: monospace;
            font-size: 0.9rem;
        }
        
        .page-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }

        .page-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 600;
        }

        .page-header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 400px 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .form-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 25px;
            border: 1px solid #e1e5e9;
            height: fit-content;
        }

        .form-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f1f2f6;
        }

        .form-header h2 {
            margin: 0;
            color: #2c3e50;
            font-size: 1.3rem;
        }

        .form-header i {
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

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            min-height: 80px;
            resize: vertical;
        }

        .btn {
            padding: 12px 25px;
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
            width: 100%;
            justify-content: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .table-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid #e1e5e9;
        }

        .table-header {
            background: #f8f9fa;
            padding: 20px 25px;
            border-bottom: 1px solid #e1e5e9;
        }

        .table-header h2 {
            margin: 0;
            color: #2c3e50;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-header i {
            color: #667eea;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            background: #667eea;
            color: #fff;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .data-table td {
            padding: 15px;
            border-bottom: 1px solid #e1e5e9;
            vertical-align: top;
        }

        .data-table tr:hover {
            background: #f8f9fa;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-sm {
            padding: 8px 12px;
            font-size: 0.85rem;
        }

        .btn-danger {
            background: #dc3545;
            color: #fff;
        }

        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .btn-success {
            background: #28a745;
            color: #fff;
        }

        .btn-success:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #dee2e6;
        }

        .empty-state h3 {
            margin: 0 0 10px;
            color: #495057;
        }

        .empty-state p {
            margin: 0;
            font-size: 0.9rem;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert i {
            font-size: 1.2rem;
        }

        .view-toggle {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            justify-content: center;
        }

        .view-btn {
            padding: 10px 20px;
            border: 2px solid #667eea;
            background: transparent;
            color: #667eea;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .view-btn.active {
            background: #667eea;
            color: white;
        }

        .view-btn:hover {
            background: #667eea;
            color: white;
        }

        .calendar-view {
            display: none;
        }

        .calendar-view.active {
            display: block;
        }

        .list-view.active {
            display: block;
        }

        .list-view {
            display: none;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: #e1e5e9;
            border-radius: 8px;
            overflow: hidden;
        }

        .calendar-header {
            background: #667eea;
            color: white;
            padding: 15px;
            text-align: center;
            font-weight: 600;
        }

        .calendar-day {
            background: white;
            padding: 10px;
            min-height: 80px;
            border: 1px solid #e1e5e9;
            position: relative;
        }

        .calendar-day.other-month {
            background: #f8f9fa;
            color: #999;
        }

        .calendar-day.today {
            background: #e3f2fd;
            border-color: #2196f3;
        }

        .day-number {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .event-indicator {
            background: #667eea;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.75rem;
            margin: 2px 0;
            cursor: pointer;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .event-indicator:hover {
            background: #5a6fd8;
        }

        .month-navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 0 20px;
        }

        .month-nav-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .month-nav-btn:hover {
            background: #5a6fd8;
        }

        .current-month {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .date-time-display {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .event-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .event-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .event-location {
            color: #667eea;
            font-size: 0.85rem;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .data-table {
                font-size: 0.85rem;
            }
            
            .data-table th,
            .data-table td {
                padding: 10px 8px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <?php include('nav.php'); ?>
    
    <div class="page-container">
        
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-calendar-alt"></i> Calendar Management </h1>
            <p>Manage your church events and weekly programs - </p>
        </div>

        <!-- Success/Error Messages -->
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Main Content Grid -->
        <div class="content-grid">
            <!-- Add/Edit Event Form -->
            <div class="form-card">
                <div class="form-header">
                    <i class="fas fa-plus-circle"></i>
                    <h2><?php echo isset($_GET['edit']) ? 'Edit Event' : 'Add New Event'; ?></h2>
                </div>

                <form method="post" action="">
                    <?php 
                    $edit_id = '';
                    $edit_title = '';
                    $edit_description = '';
                    $edit_date = '';
                    $edit_time = '';
                    $edit_location = '';

                    // If editing, fetch the event data
                    if (isset($_GET['edit'])) {
                        $edit_id = (int)$_GET['edit'];
                        $edit_stmt = $conn->prepare("SELECT * FROM weekly_program WHERE id = ?");
                        if ($edit_stmt) {
                            $edit_stmt->bind_param("i", $edit_id);
                            $edit_stmt->execute();
                            $edit_result = $edit_stmt->get_result();
                            if ($edit_result->num_rows > 0) {
                                $edit_row = $edit_result->fetch_assoc();
                                $edit_title = $edit_row['title'];
                                $edit_description = $edit_row['description'];
                                $edit_date = $edit_row['date'];
                                $edit_time = $edit_row['time'];
                                $edit_location = $edit_row['location'];
                            } else {
                                error_log("No event found with ID: $edit_id");
                                $error_message = "Event not found.";
                            }
                            $edit_stmt->close();
                        } else {
                            error_log("Error preparing edit query: " . $conn->error);
                            $error_message = "Error loading event for editing.";
                        }
                    }
                    ?>

                    <?php if ($edit_id): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_id; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="title">Event Title *</label>
                        <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($edit_title); ?>" required placeholder="Enter event title">
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" placeholder="Enter event description"><?php echo htmlspecialchars($edit_description); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="date">Date *</label>
                        <input type="date" name="date" id="date" value="<?php echo $edit_date; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="time">Time</label>
                        <input type="time" name="time" id="time" value="<?php echo $edit_time; ?>">
                    </div>

                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" name="location" id="location" value="<?php echo htmlspecialchars($edit_location); ?>" placeholder="Enter event location">
                    </div>

                    <button type="submit" name="<?php echo $edit_id ? 'update' : 'add'; ?>" class="btn btn-primary">
                        <i class="fas fa-<?php echo $edit_id ? 'save' : 'plus'; ?>"></i>
                        <?php echo $edit_id ? 'Update Event' : 'Add Event'; ?>
                    </button>

                    <?php if ($edit_id): ?>
                        <a href="calendarMange_debug.php" class="btn btn-secondary" style="margin-top: 10px;">
                            <i class="fas fa-times"></i>
                            Cancel Edit
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Events List -->
            <div class="table-card">
                <div class="table-header">
                    <i class="fas fa-list"></i>
                    <h2>Current Events</h2>
                </div>

                <!-- View Toggle -->
                <div class="view-toggle">
                    <button class="view-btn active" onclick="toggleView('list')">
                        <i class="fas fa-list"></i> List View
                    </button>
                    <button class="view-btn" onclick="toggleView('calendar')">
                        <i class="fas fa-calendar-alt"></i> Calendar View
                    </button>
                </div>

                <!-- List View -->
                <div class="list-view active" id="listView">
                    <?php if ($result && $result->num_rows > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Date & Time</th>
                                <th>Location</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div class="event-title"><?php echo htmlspecialchars($row['title']); ?></div>
                                        <?php if (!empty($row['description'])): ?>
                                            <div class="event-description"><?php echo htmlspecialchars($row['description']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="date-time-display">
                                            <strong><?php echo date('M j, Y', strtotime($row['date'])); ?></strong>
                                            <?php if (!empty($row['time'])): ?>
                                                <br><?php echo date('g:i A', strtotime($row['time'])); ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['location'])): ?>
                                            <div class="event-location">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <?php echo htmlspecialchars($row['location']); ?>
                                            </div>
                                        <?php else: ?>
                                            <span style="color: #999;">Not specified</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="calendarMange_debug.php?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-success">
                                                <i class="fas fa-edit"></i>
                                                Edit
                                            </a>
                                            <a href="calendarMange_debug.php?delete=<?php echo $row['id']; ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Are you sure you want to delete this event?')">
                                                <i class="fas fa-trash"></i>
                                                Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-plus"></i>
                            <h3>No Events Yet</h3>
                            <p>Start by adding your first event using the form on the left.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Calendar View -->
                <div class="calendar-view" id="calendarView">
                    <?php
                    // Get current month and year
                    $currentMonth = isset($_GET['month']) ? $_GET['month'] : date('n');
                    $currentYear = isset($_GET['year']) ? $_GET['year'] : date('Y');
                    
                    // Get first day of month and number of days
                    $firstDay = mktime(0, 0, 0, $currentMonth, 1, $currentYear);
                    $daysInMonth = date('t', $firstDay);
                    $startDay = date('w', $firstDay); // 0 = Sunday
                    
                    // Get events for the current month
                    $monthStart = $currentYear . '-' . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . '-01';
                    $monthEnd = $currentYear . '-' . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . '-' . $daysInMonth;
                    
                    $calendar_stmt = $conn->prepare("SELECT * FROM weekly_program WHERE date BETWEEN ? AND ? ORDER BY date, time");
                    if ($calendar_stmt) {
                        $calendar_stmt->bind_param("ss", $monthStart, $monthEnd);
                        $calendar_stmt->execute();
                        $calendarEvents = $calendar_stmt->get_result();
                    } else {
                        error_log("Error preparing calendar query: " . $conn->error);
                        $calendarEvents = false;
                    }
                    
                    $eventsByDate = [];
                    if ($calendarEvents) {
                        while ($event = $calendarEvents->fetch_assoc()) {
                            $eventsByDate[$event['date']][] = $event;
                        }
                    }
                    ?>
                    
                    <!-- Month Navigation -->
                    <div class="month-navigation">
                        <a href="?month=<?php echo $currentMonth == 1 ? 12 : $currentMonth - 1; ?>&year=<?php echo $currentMonth == 1 ? $currentYear - 1 : $currentYear; ?>" class="month-nav-btn">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                        <div class="current-month">
                            <?php echo date('F Y', $firstDay); ?>
                        </div>
                        <a href="?month=<?php echo $currentMonth == 12 ? 1 : $currentMonth + 1; ?>&year=<?php echo $currentMonth == 12 ? $currentYear + 1 : $currentYear; ?>" class="month-nav-btn">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="calendar-grid">
                        <!-- Day headers -->
                        <div class="calendar-header">Sun</div>
                        <div class="calendar-header">Mon</div>
                        <div class="calendar-header">Tue</div>
                        <div class="calendar-header">Wed</div>
                        <div class="calendar-header">Thu</div>
                        <div class="calendar-header">Fri</div>
                        <div class="calendar-header">Sat</div>

                        <?php
                        // Fill in empty cells for days before the first day of the month
                        for ($i = 0; $i < $startDay; $i++) {
                            $prevMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
                            $prevYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;
                            $prevMonthDays = date('t', mktime(0, 0, 0, $prevMonth, 1, $prevYear));
                            $dayNumber = $prevMonthDays - $startDay + $i + 1;
                            echo '<div class="calendar-day other-month">';
                            echo '<div class="day-number">' . $dayNumber . '</div>';
                            echo '</div>';
                        }

                        // Fill in the days of the current month
                        for ($day = 1; $day <= $daysInMonth; $day++) {
                            $date = $currentYear . '-' . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                            $isToday = $date == date('Y-m-d');
                            $hasEvents = isset($eventsByDate[$date]);
                            
                            echo '<div class="calendar-day' . ($isToday ? ' today' : '') . '">';
                            echo '<div class="day-number">' . $day . '</div>';
                            
                            if ($hasEvents) {
                                foreach ($eventsByDate[$date] as $event) {
                                    $timeDisplay = !empty($event['time']) ? date('g:i A', strtotime($event['time'])) : '';
                                    echo '<div class="event-indicator" title="' . htmlspecialchars($event['title']) . ($timeDisplay ? ' at ' . $timeDisplay : '') . '">';
                                    echo htmlspecialchars($event['title']);
                                    echo '</div>';
                                }
                            }
                            
                            echo '</div>';
                        }

                        // Fill in empty cells for days after the last day of the month
                        $remainingCells = 42 - ($startDay + $daysInMonth); // 42 = 6 weeks * 7 days
                        for ($i = 1; $i <= $remainingCells; $i++) {
                            echo '<div class="calendar-day other-month">';
                            echo '<div class="day-number">' . $i . '</div>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for enhanced functionality -->
    <script>
        // Toggle between list and calendar view
        function toggleView(view) {
            const listView = document.getElementById('listView');
            const calendarView = document.getElementById('calendarView');
            const buttons = document.querySelectorAll('.view-btn');
            
            // Remove active class from all buttons
            buttons.forEach(btn => btn.classList.remove('active'));
            
            if (view === 'list') {
                listView.classList.add('active');
                calendarView.classList.remove('active');
                document.querySelector('.view-btn[onclick="toggleView(\'list\')"]').classList.add('active');
            } else {
                listView.classList.remove('active');
                calendarView.classList.add('active');
                document.querySelector('.view-btn[onclick="toggleView(\'calendar\')"]').classList.add('active');
            }
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const date = document.getElementById('date').value;
            
            if (!title) {
                alert('Please enter an event title.');
                e.preventDefault();
                return;
            }
            
            if (!date) {
                alert('Please select a date.');
                e.preventDefault();
                return;
            }
            
            // Check if date is in the past
            const selectedDate = new Date(date);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                if (!confirm('The selected date is in the past. Are you sure you want to add this event?')) {
                    e.preventDefault();
                    return;
                }
            }
        });

        // Auto-focus on title field when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const titleField = document.getElementById('title');
            if (titleField && !titleField.value) {
                titleField.focus();
            }
        });
    </script>
</body>

</html>
