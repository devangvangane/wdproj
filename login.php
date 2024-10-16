<?php
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Database connection
    $conn = new mysqli("localhost", "root", "", "appointment_db");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to validate the user's email and password
    $sql = "SELECT id, name, email, password FROM patients WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify the password (assuming passwords are hashed in the database)
        if (password_verify($password, $row['password'])) {
            // Store the user id, email, and name in session
            $_SESSION['id'] = $row['id'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['name'] = $row['name']; // Store the name in session

            // Redirect to the appointment page after successful login
            header("Location: book_appointment.php");
            exit();
        } else {
            echo "<script>alert('Invalid email or password.');</script>";
        }
    } else {
        echo "<script>alert('Invalid email or password.');</script>";
    }

    $stmt->close(); // Close the prepared statement
    $conn->close(); // Close the database connection
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="loginstyle.css">

</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required><br><br>
            
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required><br><br>
            
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php"><b>Register here</b></a>.</p>
        <p><a href="index.html"><b>Home Page</b></a></p>
    </div>
</body>
</html>
 