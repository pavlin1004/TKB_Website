<?php
session_start();
require_once 'includes/db.inc.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_keybinding'])) {
    $user_id = $_SESSION['user_id']; 
    $keybinding_id = $_POST['keybinding_id']; 
    $new_data = $_POST['new_data']; 
    $new_description = $_POST['new_description'];

    $conn->begin_transaction();

    try {

        $stmt = $conn->prepare("UPDATE keybindings SET data=?, description=? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssii", $new_data, $new_description, $keybinding_id, $user_id);
        $stmt->execute();
        
        $stmt_version = $conn->prepare("INSERT INTO keybinding_version (keybinding_id, data, modified) VALUES (?, ?, NOW())");
        $stmt_version->bind_param("is", $keybinding_id, $new_data);
        $stmt_version->execute();

        $conn->commit();
        header("Location: home.php");
        exit();
    } catch (Exception $e) {

        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
   
} elseif (isset($_GET['id'])) {
    
    $keybinding_id = $_GET['id'];
    $user_id = $_SESSION['user_id']; 
    
    $stmt = $conn->prepare("SELECT * FROM keybindings WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $keybinding_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $keybinding = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/edit.css">
    <title>Edit Keybinding</title>
</head>
<body>
    <h1>Edit Keybinding</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input type="hidden" name="keybinding_id" value="<?php echo $keybinding['id']; ?>">
        <div>
            <label for="new_data">New Keybinding Data:</label>
            <textarea id="new_data" name="new_data"><?php echo htmlspecialchars($keybinding['data']); ?></textarea>
        </div>
        <div>
            <label for="new_description">New Keybinding Description:</label>
            <textarea id="new_description" name="new_description"><?php echo htmlspecialchars($keybinding['description']); ?></textarea>
        </div>
        <div>
            <input type="submit" name="edit_keybinding" value="Edit Keybinding">
        </div>
    </form>
</body>
</html>
