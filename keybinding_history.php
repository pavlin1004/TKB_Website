<?php
session_start();
require_once 'includes/db.inc.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    echo "No keybinding ID provided";
    exit;
}

$keybinding_id = $_GET['id'];

$stmt_versions = $conn->prepare("SELECT id, data, modified FROM keybinding_version WHERE keybinding_id = ? ORDER BY modified DESC");
$stmt_versions->bind_param("i", $keybinding_id);
$stmt_versions->execute();
$versions = $stmt_versions->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/history.css">
    <title>Keybinding Versions</title>
</head>
<body>
    <h1>Keybinding Versions</h1>
    <div class="back-button">
        <a href="home.php">Back to Home</a>
    </div>
    <?php foreach ($versions as $version): ?>
        <div class="version-container">
            <div class="version-header">Version ID: <?php echo htmlspecialchars($version['id']); ?> | Modified: <?php echo htmlspecialchars($version['modified']); ?></div>
            <div class="version-data"><?php echo nl2br(htmlspecialchars($version['data'])); ?></div>
        </div>
    <?php endforeach; ?>
</body>
</html>

