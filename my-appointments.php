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

// Handle appointment cancellation
if(isset($_POST['cancel_appointment'])) {
    $appointment_id = $_POST['appointment_id'];
    $sql = "UPDATE customer SET status = 'cancelled', cancelled_by = 'user' WHERE id = $appointment_id AND user_id = $user_id";
    $con->query($sql) or die(mysqli_error($con));
}

// Fetch user's appointments
$sql = "SELECT * FROM customer WHERE user_id = $user_id ORDER BY appointment_date DESC, appointment_time DESC";
$appointments = $con->query($sql) or die(mysqli_error($con));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - ENNO</title>
    <link rel="stylesheet" href="css/user/my-appointments.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

      
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Home</a>
        <h1>My Appointments</h1>

        <?php if($appointments->num_rows > 0): ?>
            <div class="appointments-grid">
                <?php while($row = $appointments->fetch_assoc()): ?>
                    <div class="appointment-card">
                        <h3><?php echo $row['service_type']; ?></h3>
                        <div class="appointment-info">
                            <p><strong>Date:</strong> <?php echo date('F d, Y', strtotime($row['appointment_date'])); ?></p>
                            <p><strong>Time:</strong> <?php echo $row['appointment_time']; ?></p>
                            <p><strong>Type:</strong> <?php echo ucfirst($row['appointment_type']); ?></p>
                            <p><strong>Status:</strong> 
                                <span class="status <?php echo strtolower($row['status']); ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </p>
                        </div>
                        <?php if($row['status'] == 'pending'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="cancel_appointment" class="cancel-btn">
                                    Cancel Appointment
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="far fa-calendar-times"></i>
                <h3>No Appointments Found</h3>
                <p>You haven't made any appointments yet.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>