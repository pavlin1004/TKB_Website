<?php
session_start();
require_once 'includes/db.inc.php'; // Ensure db.inc.php is correctly pathed

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = $_POST['data'];
    $description = $_POST['description'];
    $user_id = $_SESSION['user_id'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Prepare INSERT statement for tasks table
        $stmt = $conn->prepare("INSERT INTO tasks (data, description, user_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $data, $description, $user_id);

        // Execute the statement
        if (!$stmt->execute()) {
            throw new Exception("Error inserting task: " . $stmt->error);
        }

        // Get the task ID of the newly inserted task
        $task_id = $conn->insert_id;

        // Prepare and execute INSERT statement for task_version table
        $stmt_version = $conn->prepare("INSERT INTO task_version (task_id, data, modified) VALUES (?, ?, NOW())");
        $stmt_version->bind_param("is", $task_id, $data);

        if (!$stmt_version->execute()) {
            throw new Exception("Error inserting task version: " . $stmt_version->error);
        }

        // Commit the transaction
        $conn->commit();

        // Redirect to home page
        header('Location: home.php');
        exit;
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/add.css">
    <title>Add Task</title>
</head>
<body>
    <div class="container">
        <h1>Add Task</h1>
        <form method="post">
            <div class="form-group">
                <label for="data">Task:</label>
                <textarea id="data" name="data" required></textarea>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div class="form-btn">
                <button type="submit">Add</button>
            </div>
        </form>
    </div>
</body>
</html>
