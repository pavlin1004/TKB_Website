<?php
session_start();
require_once 'includes/db.inc.php'; // Include your database connection script here

if (isset($_GET['id'])) {
    $keybinding_id = $_GET['id'];
    $user_id = $_SESSION['user_id']; // Assuming $_SESSION['user'] holds user information

    $stmt_deleteHistory = $conn->prepare("DELETE FROM keybinding_version WHERE keybinding_id = ?");
    $stmt_deleteHistory->bind_param("i",$keybinding_id);
    $stmt_deleteHistory->execute();
    // Delete task from database
    $stmt = $conn->prepare("DELETE FROM keybindings WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $keybinding_id, $user_id);
    
    if ($stmt->execute()) {
        header("Location: home.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
