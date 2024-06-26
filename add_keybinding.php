<?php
session_start();
require_once 'includes/db.inc.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = $_POST['data'];
    $description = $_POST['description'];
    $user_id = $_SESSION['user_id'];

 
    $conn->begin_transaction();

    try {
    
        $stmt = $conn->prepare("INSERT INTO keybindings (data, description, user_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $data, $description, $user_id);

       
        if (!$stmt->execute()) {
            throw new Exception("Error inserting keybinding: " . $stmt->error);
        }


        $keybinding_id = $conn->insert_id;

        $stmt_version = $conn->prepare("INSERT INTO keybinding_version (keybinding_id, data, modified) VALUES (?, ?, NOW())");
        $stmt_version->bind_param("is", $keybinding_id, $data);

        if (!$stmt_version->execute()) {
            throw new Exception("Error inserting keybinding version: " . $stmt_version->error);
        }

        $conn->commit();

        header('Location: home.php');
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/add.css">
    <title>Add Keybinding</title>
</head>
<body>
    <<div class="container">
        <h1>Add Keybinding</h1>
        <form method="post">
            <div class="form-group">
                <label for="data">Keybinding:</label>
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
