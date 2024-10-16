<?php
// Start session to manage registration state
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";  // Add your MySQL password if applicable
    $dbname = "appointment_db";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve and sanitize form data
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $conn->real_escape_string(trim($_POST['password']));
    $confirm_password = $conn->real_escape_string(trim($_POST['confirm_password']));

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "Passwords do not match!";
    } else {
        // Check if email already exists
        $check_email_sql = "SELECT * FROM patients WHERE email = '$email'";
        $check_email_result = $conn->query($check_email_sql);

        if ($check_email_result->num_rows > 0) {
            echo "Email is already registered. Please use a different email.";
        } else {
            // Insert new patient record into the database using a prepared statement
            $stmt = $conn->prepare("INSERT INTO patients (name, email, password) VALUES (?, ?, ?)");
            $hashed_password = password_hash($password, PASSWORD_BCRYPT); // Hash the password
            $stmt->bind_param("sss", $name, $email, $hashed_password);

            if ($stmt->execute()) {
                echo "Registration successful! You can now <a href='login.php'>login</a>.";
            } else {
                echo "Error: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        }
    }

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration</title>
    <link rel="stylesheet" href="registerstyle.css">
</head>
<body>
    <div class="container">
        <h2>Register as a Patient</h2>

        <!-- Registration form -->
        <form action="register.php" method="POST">
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" required><br><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required><br><br>

            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php"><b>Login here</b></a>.</p>
        <p><a href="index.html"><b>Home Page</b></a></p>
    </div>
</body>
</html>
