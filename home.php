<?php
session_start();
require_once 'includes/db.inc.php'; // Include your database connection script here

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch tasks
$stmt_tasks = $conn->prepare("SELECT id, data, description FROM tasks WHERE user_id = ?");
$stmt_tasks->bind_param("i", $user_id);
$stmt_tasks->execute();
$tasks = $stmt_tasks->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch keybindings
$stmt_keybindings = $conn->prepare("SELECT id, data, description FROM keybindings WHERE user_id = ?");
$stmt_keybindings->bind_param("i", $user_id);
$stmt_keybindings->execute();
$keybindings = $stmt_keybindings->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/home.css">
    <title>Home Page</title>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-center">
                <h1>Hello, <?php echo htmlspecialchars($_SESSION['user']); ?></h1>
            </div>
            <div class="header-top">
                <a href="logout.php" class="btn btn-logout">Logout</a>
            </div>
        </div>
    </header>

    <main class="container">
        <section class="section">
            <div class="section-header">
                <h2>Your Tasks</h2>
                <a href="add_task.php" class="btn">Add Task</a>
            </div>
            <div class="card">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($task['id']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($task['data'])); ?></td>
                                <td><?php echo htmlspecialchars($task['description']); ?></td>
                                <td class="actions">
                                    <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="btn btn-secondary">Edit</a>
                                    <a href="delete_task.php?id=<?php echo $task['id']; ?>" class="btn btn-danger">Delete</a>
                                    <a href="task_history.php?id=<?php echo $task['id']; ?>" class="btn btn-info">History</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="section">
            <div class="section-header">
                <h2>Your Keybindings</h2>
                <a href="add_keybinding.php" class="btn btn-primary">Add Keybinding</a>
            </div>
            <div class="card">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($keybindings as $keybinding): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($keybinding['id']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($keybinding['data'])); ?></td>
                                <td><?php echo htmlspecialchars($keybinding['description']); ?></td>
                                <td class="actions">
                                    <a href="edit_keybinding.php?id=<?php echo $keybinding['id']; ?>" class="btn btn-secondary">Edit</a>
                                    <a href="delete_keybinding.php?id=<?php echo $keybinding['id']; ?>" class="btn btn-danger">Delete</a>
                                    <a href="keybinding_history.php?id=<?php echo $keybinding['id']; ?>" class="btn btn-info">History</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    
</body>
</html>
