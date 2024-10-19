<?php
require('db_connection_mysqli.php');
session_start();

$username = $email = $password = "";
$usernameErr = $emailErr = $passwordErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = cleanInput($_POST["username"]);
    $email = cleanInput($_POST["email"]);
    $password = password_hash(cleanInput($_POST["password"]), PASSWORD_DEFAULT);

    // Validate inputs
    if (empty($username)) {
        $usernameErr = "Username is required";
    }
    if (empty($email)) {
        $emailErr = "Email is required";
    }
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    }

    // Insert into database if validation passes
    if (empty($usernameErr) && empty($emailErr) && empty($passwordErr)) {
        $query = "INSERT INTO users (Username, Email, Password) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($dbc, $query);
        mysqli_stmt_bind_param($stmt, 'sss', $username, $email, $password);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: login.php");
            exit;
        } else {
            echo "Error: " . mysqli_error($dbc);
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
    <title>Register</title>
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
            <h2>Register</h2>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" value="<?php echo $username; ?>">
                    <span class="text-danger"><?php echo $usernameErr; ?></span>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo $email; ?>">
                    <span class="text-danger"><?php echo $emailErr; ?></span>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password">
                    <span class="text-danger"><?php echo $passwordErr; ?></span>
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>
