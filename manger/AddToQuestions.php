<?php
session_start();
include '../connect.php';
include '../GetClient.php';

// Redirect if admin is not logged in
if (empty($_SESSION['adminUserName'])) {
    header("Location: LogInToAdmin.php");
    exit();
}

include('../manger/nav.php');

// ===== FUNCTIONS =====
function addQuestion($conn, $question_text, $option1, $clientID) {
    $stmt = $conn->prepare("
        INSERT INTO questions (client_id, question_text, option1, created_at)
        VALUES (?, ?, ?, NOW())
    ");
    if (!$stmt) return false;

    $stmt->bind_param("iss", $clientID, $question_text, $option1);
    return $stmt->execute();
}

function updateQuestion($conn, $id, $question_text, $option1) {
    $stmt = $conn->prepare("
        UPDATE questions 
        SET question_text=?, option1=?
        WHERE id=?
    ");
    if (!$stmt) return false;

    $stmt->bind_param("ssi", $question_text, $option1, $id);
    return $stmt->execute();
}

function deleteQuestion($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM questions WHERE id=?");
    if (!$stmt) return false;

    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// ===== HANDLE FORM =====
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // delete לא צריך טקסט
    if (isset($_POST["delete_question"])) {
        deleteQuestion($conn, $_POST["question_id"]);

    } else {
        $question_text = $conn->real_escape_string($_POST["question_text"]);
        $option1       = $conn->real_escape_string($_POST["option1"]);

        if (isset($_POST["add_question"])) {
            addQuestion($conn, $question_text, $option1, $clientID);
        } elseif (isset($_POST["edit_question"])) {
            updateQuestion($conn, $_POST["question_id"], $question_text, $option1);
        }
    }

    // רענון כדי לא לשלוח שוב POST
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// ===== FETCH QUESTIONS =====
$questions = $conn->query("
    SELECT * FROM questions 
    WHERE client_id = $clientID 
    ORDER BY created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Questions</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
    body {
        background: #f3f4f8;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    /* GENERAL PAGE LAYOUT */
    .page-container {
        max-width: 1200px;
        margin: 20px auto 40px;
        padding: 0 15px;
    }

    /* HEADER */
    .page-header {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #fff;
        padding: 28px 24px;
        border-radius: 18px;
        margin-bottom: 25px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .page-header h1 {
        margin: 0;
        font-size: 1.9rem;
        font-weight: 700;
    }
    .page-header p {
        margin: 0;
        opacity: .9;
    }

    /* GRID: ADD FORM + LIST */
    .content-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(0, 1.6fr);
        gap: 24px;
    }

    /* CARD */
    .card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        border: 1px solid #e2e8f0;
    }

    /* ADD FORM CARD */
    .form-card {
        padding: 22px 22px 24px;
    }
    .card-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
    }
    .card-header i {
        color: #667eea;
    }
    .card-header h2 {
        margin: 0;
        font-size: 1.2rem;
        color: #1f2937;
    }

    /* INPUTS / LABELS */
    .form-group {
        margin-bottom: 16px;
    }
    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        font-size: 0.9rem;
        color: #4b5563;
    }
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 10px 12px;
        border-radius: 10px;
        border: 2px solid #e5e7eb;
        background: #f9fafb;
        font-size: 0.95rem;
        transition: .25s;
    }
    .form-group textarea {
        min-height: 80px;
        resize: vertical;
    }
    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #667eea;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.25);
    }

    /* BUTTONS */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 9px 18px;
        border-radius: 999px;
        border: none;
        cursor: pointer;
        font-size: 0.95rem;
        font-weight: 600;
        transition: .2s;
        text-decoration: none;
    }
    .btn i {
        font-size: 0.9rem;
    }
    .btn-primary {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #fff;
    }
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 16px rgba(79, 70, 229, 0.45);
    }
    .btn-danger {
        background: #ef4444;
        color: #fff;
    }
    .btn-danger:hover {
        background: #dc2626;
        transform: translateY(-1px);
    }

    /* QUESTIONS LIST CARD */
    .list-card {
        padding: 18px 18px 6px;
    }
    .list-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    .list-header h2 {
        margin: 0;
        font-size: 1.1rem;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .list-header h2 i {
        color: #667eea;
    }
    .list-count {
        font-size: 0.85rem;
        color: #6b7280;
    }

    /* QUESTION CARD */
    .question-card {
        margin-bottom: 14px;
        padding: 14px 14px 12px;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
    }
    .question-header {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 6px;
    }
    .question-title {
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }
    .question-id {
        background: #6366f1;
        color: #fff;
        font-size: 0.75rem;
        border-radius: 999px;
        padding: 2px 8px;
        font-weight: 600;
        margin-top: 2px;
    }
    .question-text {
        font-weight: 600;
        color: #111827;
        font-size: 0.96rem;
    }
    .question-meta {
        font-size: 0.8rem;
        color: #6b7280;
    }

    /* ANSWER DISPLAY */
    .answer-display {
        margin: 6px 0 10px;
        padding: 8px 10px;
        border-radius: 10px;
        background: #eef2ff;
        border-left: 4px solid #6366f1;
        font-size: 0.9rem;
    }
    .answer-display span {
        font-weight: 600;
        color: #4f46e5;
    }

    /* EDIT FORM INSIDE CARD */
    .edit-form {
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px dashed #e5e7eb;
    }
    .edit-form-row {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(0, 1.5fr);
        gap: 12px;
    }
    .edit-form .form-group {
        margin-bottom: 10px;
    }
    .card-actions {
        display: flex;
        gap: 10px;
        margin-top: 6px;
    }

    /* MOBILE */
    @media (max-width: 900px) {
        .content-grid {
            grid-template-columns: minmax(0,1fr);
        }
    }
    @media (max-width: 640px) {
        .edit-form-row {
            grid-template-columns: 1fr;
        }
        .card-actions {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>
</head>
<body>

<div class="page-container">

    <!-- HEADER -->
    <div class="page-header">
        <h1> Manage Questions</h1>
        <p>Create and edit questions with a clean, unified style.</p>
    </div>

    <div class="content-grid">

        <!-- ADD NEW QUESTION -->
        <div class="card form-card">
            <div class="card-header">
                <i class="fa-solid fa-plus"></i>
                <h2>Add New Question</h2>
            </div>

            <form method="post">
                <div class="form-group">
                    <label>Question Text *</label>
                    <textarea name="question_text" required placeholder="Write the question your visitors will see..."></textarea>
                </div>

                <div class="form-group">
                    <label>Answer *</label>
                    <input type="text" name="option1" required placeholder="Write the answer that will be displayed">
                </div>

                <button type="submit" name="add_question" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Add Question
                </button>
            </form>
        </div>

        <!-- LIST QUESTIONS -->
        <div class="card list-card">
            <div class="list-header">
                <h2><i class="fa-solid fa-list"></i> Existing Questions</h2>
                <span class="list-count">
                    <?php echo $questions ? $questions->num_rows : 0; ?> items
                </span>
            </div>

            <?php if ($questions && $questions->num_rows > 0): ?>
                <?php while ($row = $questions->fetch_assoc()): ?>
                    <div class="question-card">
                        <div class="question-header">
                            <div class="question-title">
                                <span class="question-id">#<?php echo (int)$row['id']; ?></span>
                                <div>
                                    <div class="question-text">
                                        <?php echo htmlspecialchars($row['question_text']); ?>
                                    </div>
                                    <div class="question-meta">
                                        Created: <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="answer-display">
                            <span>Answer:</span>
                            &nbsp;<?php echo htmlspecialchars($row['option1']); ?>
                        </div>

                        <!-- EDIT + DELETE -->
                        <form method="post" class="edit-form">
                            <input type="hidden" name="question_id" value="<?php echo (int)$row['id']; ?>">

                            <div class="edit-form-row">
                                <div class="form-group">
                                    <label>Edit Question</label>
                                    <textarea name="question_text" required><?php echo htmlspecialchars($row['question_text']); ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Edit Answer</label>
                                    <input type="text" name="option1" required value="<?php echo htmlspecialchars($row['option1']); ?>">
                                </div>
                            </div>

                            <div class="card-actions">
                                <button type="submit" name="edit_question" class="btn btn-primary">
                                    <i class="fa-solid fa-save"></i> Save
                                </button>

                        </form>
                        <form method="post" onsubmit="return confirm('Are you sure you want to delete this question?');">
                            <input type="hidden" name="question_id" value="<?php echo (int)$row['id']; ?>">
                            <button type="submit" name="delete_question" class="btn btn-danger">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button>
                        </form>

                            </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="padding: 28px; text-align: center; color:#6b7280;">
                    <i class="fa-regular fa-circle-question" style="font-size: 2.4rem; margin-bottom: 10px;"></i>
                    <div style="font-weight:600; margin-bottom:4px;">No questions yet</div>
                    <div style="font-size:0.9rem;">Start by adding a new question using the form on the left.</div>
                </div>
            <?php endif; ?>
        </div>

    </div>

</div>

</body>
</html>
