<?php
require('db_connection_mysqli.php');
session_start();

$username = $password = "";
$usernameErr = $passwordErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = cleanInput($_POST["username"]);
    $password = cleanInput($_POST["password"]);

    if (empty($username)) {
        $usernameErr = "Username is required";
    }
    if (empty($password)) {
        $passwordErr = "Password is required";
    }

    // Check credentials
    if (empty($usernameErr) && empty($passwordErr)) {
        $query = "SELECT * FROM users WHERE Username = ?";
        $stmt = mysqli_prepare($dbc, $query);
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['Password'])) {
            $_SESSION['admin_logged_in'] = true; // or set user role if needed
            header("Location: index.php");
            exit;
        } else {
            echo "<script>alert('Invalid username or password.');</script>";
        }
    }
}

function cleanInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css"> <!-- Link to the external CSS file -->
    <style>
        /* General Body Styles */
        body {
            background-color: #e8f5e9; /* Lighter grey background for a softer look */
            font-family: 'Arial', sans-serif; /* Font family */
            color: #333333; /* Darker grey text for better contrast */
        }

        /* Overlay Styles */
        .overlay {
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            background: rgba(0, 0, 0, 0.5); /* Dark transparent background */
            display: flex; 
            justify-content: center; 
            align-items: center; 
            z-index: 999; /* Above other content */
        }

        /* Dialog Box Styles */
        .dialog {
            background-color: white; /* White background for the dialog */
            border-radius: 10px; /* Rounded corners */
            padding: 20px; /* Padding inside the dialog */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Shadow for depth */
            width: 300px; /* Width of the dialog */
            animation: dance 1s infinite alternate; /* Dancing effect */
        }

        /* Dancing Animation */
        @keyframes dance {
            0% { transform: translateY(0); }
            25% { transform: translateY(-5px); }
            50% { transform: translateY(5px); }
            75% { transform: translateY(-5px); }
            100% { transform: translateY(0); }
        }

        /* Close Button Styles */
        .btn-close {
            float: right; /* Float to the right */
        }

        /* Error Text Styles */
        .text-danger {
            font-size: 0.9em; /* Slightly smaller error text */
            color: #e63946; /* A new vibrant red for error messages */
        }
    </style>
</head>
<body>
    <div class="overlay">
        <div class="dialog">
            <h2>Login</h2>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" value="<?php echo $username; ?>">
                    <span class="text-danger"><?php echo $usernameErr; ?></span>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password">
                    <span class="text-danger"><?php echo $passwordErr; ?></span>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
</body>
</html>
