<?php
session_start();
include_once("connection/connection.php");
$con = connection();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// Handle password update
if(isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verify current password
    $sql = "SELECT password FROM clients WHERE id = $user_id";
    $result = $con->query($sql);
    $user = $result->fetch_assoc();

    if(password_verify($current_password, $user['password'])) {
        if($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE clients SET password = '$hashed_password' WHERE id = $user_id";
            if($con->query($update_sql)) {
                $message = '<div class="alert success">Password updated successfully!</div>';
            } else {
                $message = '<div class="alert error">Error updating password. Please try again.</div>';
            }
        } else {
            $message = '<div class="alert error">New passwords do not match.</div>';
        }
    } else {
        $message = '<div class="alert error">Current password is incorrect.</div>';
    }
}

// Fetch user data
$sql = "SELECT * FROM clients WHERE id = $user_id";
$result = $con->query($sql);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - ENNO</title>
    <link rel="stylesheet" href="css/user/my-account.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Home</a>
        <h1>My Account</h1>

        <?php echo $message; ?>

        <div class="user-info">
            <h2 class="section-title">Profile Information</h2>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['user_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        </div>

        <div class="password-section">
            <h2 class="section-title">Change Password</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" name="update_password" class="submit-btn">Update Password</button>
            </form>
        </div>
    </div>
</body>
</html>