<?php
session_start();
include_once("connection/connection.php");
$con = connection();

if(isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Check if passwords match
    if($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        // Check if email already exists
        $check_email = "SELECT * FROM clients WHERE email = '$email'";
        $email_result = $con->query($check_email) or die ($con->error);
        
        if($email_result->num_rows > 0) {
            echo "<script>alert('Email already exists!');</script>";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $sql = "INSERT INTO clients (user_name, email, password, created_at) 
                    VALUES ('$username', '$email', '$hashed_password', NOW())";
            
            if($con->query($sql)) {
                echo "<script>
                    alert('Registration Successful!');
                    window.location.href='login.php';
                    </script>";
            } else {
                echo "<script>alert('Error in registration!');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .register-container {
            background-color: #ffffff;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            margin: 2rem auto;
        }

        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .register-header h1 {
            color: #2c3e50;
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
        }

        .register-header p {
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

        .password-requirements {
            font-size: 0.85rem;
            color: #7f8c8d;
            margin-top: 0.5rem;
            padding-left: 15px;
        }

        .register-button {
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

        .register-button:hover {
            background-color: #2980b9;
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #7f8c8d;
        }

        .login-link a {
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
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

        .input-group .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #95a5a6;
            cursor: pointer;
            padding: 5px;
        }

        .validation-check {
            margin-top: 0.25rem;
            font-size: 0.8rem;
            color: #7f8c8d;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .validation-check i {
            font-size: 0.9rem;
        }

        .validation-check.valid {
            color: #27ae60;
        }

        .validation-check.invalid {
            color: #e74c3c;
        }

        @media (max-width: 480px) {
            .register-container {
                margin: 1rem;
                padding: 1.5rem;
            }

            .register-header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>Create Account</h1>
            <p>Please fill in your information</p>
        </div>
        
        <div class="error-message" id="errorMessage"></div>
        
        <form action="" method="POST" id="registerForm">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" id="username" name="username" placeholder="Username" required>
            </div>

            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" id="email" name="email" placeholder="Email address" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" placeholder="Password" required minlength="6">
                <i class="fas fa-eye password-toggle" onclick="togglePassword('password')"></i>
            </div>
            <div class="validation-check">
                <i class="fas fa-check"></i>
                Minimum 6 characters
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" required minlength="6">
                <i class="fas fa-eye password-toggle" onclick="togglePassword('confirm_password')"></i>
            </div>
            <div class="validation-check">
                <i class="fas fa-check"></i>
                Passwords match
            </div>

            <button type="submit" name="register" class="register-button">
                Create Account
            </button>
            
            <div class="login-link">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </form>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling;
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Real-time password validation
        const form = document.getElementById('registerForm');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const validationChecks = document.querySelectorAll('.validation-check');

        function validatePassword() {
            // Check minimum length
            const lengthValid = password.value.length >= 6;
            validationChecks[0].classList.toggle('valid', lengthValid);
            validationChecks[0].classList.toggle('invalid', !lengthValid);

            // Check passwords match
            const passwordsMatch = password.value === confirmPassword.value;
            validationChecks[1].classList.toggle('valid', passwordsMatch);
            validationChecks[1].classList.toggle('invalid', !passwordsMatch);

            return lengthValid && passwordsMatch;
        }

        password.addEventListener('input', validatePassword);
        confirmPassword.addEventListener('input', validatePassword);

        form.addEventListener('submit', function(e) {
            if (!validatePassword()) {
                e.preventDefault();
                document.getElementById('errorMessage').style.display = 'block';
                document.getElementById('errorMessage').textContent = 'Please fix the password requirements';
            }
        });

        // Show error message if PHP generates an alert
        <?php if(isset($error_message)): ?>
        document.getElementById('errorMessage').style.display = 'block';
        document.getElementById('errorMessage').textContent = '<?php echo $error_message; ?>';
        <?php endif; ?>
    </script>
</body>
</html>