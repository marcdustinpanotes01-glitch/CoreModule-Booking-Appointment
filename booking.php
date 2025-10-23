<?php
session_start();
include_once("connection/connection.php");
$con = connection();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST["submit"])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $service = $_POST['service'];
    $appointmentType = $_POST['appointmentType'];

    $user_id = $_SESSION['user_id'];
    $sql = "INSERT INTO `customer`(`user_id`, `first_name`, `last_name`, `address`, `phone_number`, `appointment_date`, `appointment_time`, `service_type`, `appointment_type`, `status`, `added_at`) VALUES ('$user_id', '$firstName', '$lastName', '$address', '$phone', '$date', '$time', '$service', '$appointmentType', 'pending', NOW())";

    $con->query($sql) or die(mysqli_error($con));
    echo "<script>
        alert('Appointment booked successfully!');
        window.location.href='index.php';
    </script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book an Appointment</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="booking-container">
        <h1>Book an Appointment</h1>
        <form id="appointmentForm" action="" method="POST">
            <div class="form-group">
                <label for="firstName">First Name:</label>
                <input type="text" id="firstName" name="firstName" required>
            </div>

            <div class="form-group">
                <label for="lastName">Last Name:</label>
                <input type="text" id="lastName" name="lastName" required>
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <textarea id="address" name="address" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" required>
            </div>

            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" required>
            </div>

            <div class="form-group">
                <label for="time">Time:</label>
                <select id="time" name="time" required>
                    <option value="">Select time</option>
                    <option value="AM">AM</option>
                    <option value="PM">PM</option>
                </select>
            </div>

            <div class="form-group">
                <label for="service">Service Type:</label>
                <select id="service" name="service" required>
                    <option value="">Select a service</option>
                    <option value="Egg Hatching">Egg Hatching</option>
                    <option value="Calibration">Calibration</option>
                    <option value="Repair">Repair</option>
                </select>
            </div>

            <div class="form-group">
                <label for="appointmentType">Appointment Type:</label>
                <select id="appointmentType" name="appointmentType" required>
                    <option value="">Select appointment type</option>
                    <option value="inhouse">In-house</option>
                    <option value="home-service">Home Service</option>
                </select>
            </div>

            <input class="button" type="submit" value="Book Appointment" name="submit" id="submitBtn">
        </form>
    </div>
</body>
</html>