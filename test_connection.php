<?php
include_once("connection/connection.php");
$con = connection();

if($con) {
    echo "Database connection successful!<br>";
    
    // Test clients table
    $result = $con->query("SELECT * FROM clients LIMIT 1");
    if($result) {
        echo "Clients table is accessible!<br>";
        echo "Number of registered users: " . $con->query("SELECT COUNT(*) as count FROM clients")->fetch_assoc()['count'];
    } else {
        echo "Error accessing clients table: " . $con->error;
    }
} else {
    echo "Database connection failed!";
}
?>