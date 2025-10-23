<?php
include_once("connection/connection.php");
$con = connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['customer_id'];
    $newStatus = $_POST['new_status'];

    // First check if the appointment status and who cancelled it
    $checkSql = "SELECT status, cancelled_by FROM customer WHERE id = ?";
    $stmt = $con->prepare($checkSql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();

    // If cancelled by user, prevent any changes
    if ($booking['status'] === 'cancelled' && $booking['cancelled_by'] === 'user') {
        $_SESSION['status_message'] = "This booking has been cancelled by the user and cannot be modified.";
        header("Location: admin_dashboard.php");
        exit;
    }

    // If admin is cancelling, set cancelled_by
    if ($newStatus === 'cancelled') {
        $updateSql = "UPDATE customer SET status = ?, cancelled_by = 'admin' WHERE id = ?";
    } else {
        // If changing to another status, clear cancelled_by
        $updateSql = "UPDATE customer SET status = ?, cancelled_by = NULL WHERE id = ?";
    }
    
    $stmt = $con->prepare($updateSql);
    $stmt->bind_param("si", $newStatus, $id);
    
    if ($stmt->execute()) {
        $_SESSION['status_message'] = "Booking status updated successfully.";
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $_SESSION['status_message'] = "Error updating status: " . $con->error;
        header("Location: admin_dashboard.php");
        exit;
    }
} else {
    echo "Invalid request.";
}
?>
