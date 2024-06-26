<?php
session_start();

// Redirect if user is already logged in
if (isset($_SESSION["user"])) {
   header("Location: index.php");
   exit(); // Ensure that script stops executing after redirect
}

// Handle form submission
if (isset($_POST["submit"])) {
    require_once "includes/db.inc.php"; // Ensure your database connection is included properly

    $username = $_POST["username"];
    $password = $_POST["password"];
    $passwordRepeat = $_POST["repeat_password"];
    
    $errors = array();
    
    // Basic validation
    if (empty($username) || empty($password) || empty($passwordRepeat)) {
        $errors[] = "All fields are required.";
    }
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }
    if ($password !== $passwordRepeat) {
        $errors[] = "Passwords do not match.";
    }

    // Check if username already exists
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        die("SQL statement preparation failed.");
    } else {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $rowCount = mysqli_stmt_num_rows($stmt);
        if ($rowCount > 0) {
            $errors[] = "Username already exists.";
        }
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            die("SQL statement preparation failed.");
        } else {
            mysqli_stmt_bind_param($stmt, "ss", $username, $passwordHash);
            mysqli_stmt_execute($stmt);
            echo "<div class='alert alert-success'>You are registered successfully.</div>";
            // Redirect to login page or index.php after successful registration
            header("Location: login.php");
            exit();
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
    <link rel="stylesheet" href="css/login.css">
    <title>Registration Form</title>
</head>
<body>
    <div class="container">
        <form action="registration.php" method="post">
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <input type="text" class="form-control" name="username" placeholder="Username">
            </div>      
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password" >
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password">
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Register" name="submit">
            </div>
        </form>
        <div>
            <p>Already Registered? <a href="login.php">Login Here</a></p>
        </div>
    </div>
</body>
</html>
