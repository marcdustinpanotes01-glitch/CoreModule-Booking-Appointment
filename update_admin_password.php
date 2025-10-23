<?php
include_once("connection/connection.php");
$con = connection();

// Hash the password 'password'
$hashedPassword = password_hash('password', PASSWORD_DEFAULT);

// Update the admin password with the hashed version
$sql = "UPDATE admin SET password = '$hashedPassword' WHERE user_name = 'admin'";
$con->query($sql);

echo "Password updated successfully!";
?>