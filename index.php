<?php
ob_start();       // Start output buffering
session_start();  // Start the session at the very beginning

// Check if the user is already logged in
if (isset($_SESSION['userid'])) {
    header('Location: dashboard.php');  // Redirect to user dashboard if already logged in
    exit();
}

// Include the database connection file
require_once 'includes/database-connection.php';

$error = '';  // Variable to store error messages

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['userEmail'], $_POST['password'])) {
    $userEmail = trim($_POST['userEmail']);
    $password = trim($_POST['password']);

    // SQL to check the existence of the user
    $sql = "SELECT userID, userEmail, password FROM user WHERE userEmail = ?";

    if ($stmt = $pdo->prepare($sql)) {
        $stmt->execute([$userEmail]);  // Execute the query

        // Check if the user exists
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch();

            // Check the password (plaintext comparison)
            if ($password === $user['password']) {
                // Password is correct, start the session
                $_SESSION['userid'] = $user['userID'];
                $_SESSION['userEmail'] = $user['userEmail'];

                // Redirect to the user dashboard
                header('Location: dashboard.php');
                exit();
                ob_end_flush();   // End buffering and flush all output
            } else {
                $error = 'Invalid password.';
            }
        } else {
            $error = 'Invalid email.';
        }
    } else {
        $error = 'Oops! Something went wrong. Please try again later.';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php if ($error != '') echo '<p style="color:red;">' . $error . '</p>'; ?>
    <form action="index.php" method="post">
        <div>
            <label for="userEmail">Email:</label>
            <input type="email" name="userEmail" id="userEmail" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div>
            <button type="submit">Login</button>
        </div>
    </form>
</body>
</html>