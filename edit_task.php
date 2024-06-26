<?php
session_start();
require_once 'includes/db.inc.php'; // Include your database connection script here

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_task'])) {
    $user_id = $_SESSION['user_id']; // Assuming $_SESSION['user'] holds user information
    $task_id = $_POST['task_id']; // Assuming 'task_id' is the name of the hidden input field
    $new_data = $_POST['new_data']; // Assuming 'new_data' is the name of the textarea field for updated data
    $new_description = $_POST['new_description'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Update task in database
        $stmt = $conn->prepare("UPDATE tasks SET data=?, description=? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssii", $new_data, $new_description, $task_id, $user_id);
        $stmt->execute();
        
        // Insert into task_version table
        $stmt_version = $conn->prepare("INSERT INTO task_version (task_id, data, modified) VALUES (?, ?, NOW())");
        $stmt_version->bind_param("is", $task_id, $new_data);
        $stmt_version->execute();

        // Commit transaction
        $conn->commit();
        header("Location: home.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
} else if (isset($_GET['id'])) {
    // Fetch task details for editing
    $task_id = $_GET['id'];
    $user_id = $_SESSION['user_id']; // Assuming $_SESSION['user'] holds user information
    
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $task = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/edit.css">
    <title>Edit Task</title>
</head>
<body>
    <h1>Edit Task</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
        <div>
            <label for="new_data">New Task Data:</label>
            <textarea id="new_data" name="new_data"><?php echo htmlspecialchars($task['data']); ?></textarea>
        </div>
        <div>
            <label for="new_description">New Task Description:</label>
            <textarea id="new_description" name="new_description"><?php echo htmlspecialchars($task['description']); ?></textarea>
        </div>
        <div>
            <input type="submit" name="edit_task" value="Edit Task">
        </div>
    </form>
</body>
</html>
