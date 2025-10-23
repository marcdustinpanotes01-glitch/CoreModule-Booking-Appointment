<?php
session_start();
include_once("connection/connection.php");
$con = connection();

if(isset($_POST['login'])) {
    $username = $_POST['user_name'];
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM admin WHERE username = '$username'";
    $user = $con->query($sql) or die ($con->error);
    $row = $user->fetch_assoc();
    $total = $user->num_rows;
    
    if($total > 0) {
        if(password_verify($password,$row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['username'];
            header("Location: admin_dashboard.php");
            exit;
        } else {
            echo "<script>alert('Wrong Password');</script>";
        }
    } else {
        echo "<script>alert('User Not Registered');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .login-container {
            background-color: #ffffff;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            margin: 2rem auto;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h1 {
            color: #2c3e50;
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #7f8c8d;
            font-size: 1rem;
        }

        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #95a5a6;
        }

        .input-group input {
            width: 100%;
            padding: 12px 40px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .input-group input:focus {
            border-color: #3498db;
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.3);
        }

        .login-button {
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 1rem;
        }

        .login-button:hover {
            background-color: #2980b9;
        }

        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #7f8c8d;
        }

        .register-link a {
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .register-link a:hover {
            color: #2980b9;
        }

        .error-message {
            background-color: #ff6b6b;
            color: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
            display: none;
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 1rem;
                padding: 1.5rem;
            }

            .login-header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Admin Login</h1>
            <p>Please sign in to continue</p>
        </div>
        
        <div class="error-message" id="errorMessage"></div>
        
        <form action="" method="POST">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="text" id="user_name" name="user_name" placeholder="Username" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" name="login" class="login-button">
                Sign In
            </button>
        
            </div>
        </form>
    </div>

    <script>
        // Show error message if PHP generates an alert
        <?php if(isset($error_message)): ?>
        document.getElementById('errorMessage').style.display = 'block';
        document.getElementById('errorMessage').textContent = '<?php echo $error_message; ?>';
        <?php endif; ?>
    </script>
</body>
</html>