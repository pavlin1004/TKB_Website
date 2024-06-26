<?php
session_start();

if (isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

$errors = [];

if (isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    require_once "includes/db.inc.php"; 

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        die("SQL statement preparation failed.");
    } else {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            if (password_verify($password, $user["password"])) {
                $_SESSION["user"] = $user["username"]; 
                header("Location: home.php");
                exit();
            } else {
                $errors[] = "Password does not match.";
            }
        } else {
            $errors[] = "Username not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css"> <!-- Include your CSS file -->
    <title>Login Form</title>
    <style>
        .error-message {
            color: red;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <form action="login.php" method="post">
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <input type="text" placeholder="Enter Username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <input type="password" placeholder="Enter Password" name="password" class="form-control" required>
            </div>
            <div class="form-btn">
                <input type="submit" value="Login" name="login" class="btn btn-primary">
            </div>
        </form>
        <div>
            <p>Not registered yet? <a href="registration.php">Register Here</a></p>
        </div>
    </div>
</body>
</html>
