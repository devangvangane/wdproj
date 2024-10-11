<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['id']) || !isset($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Use your MySQL password if applicable
$dbname = "appointment_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the logged-in user's name from the session
$userName = isset($_SESSION['name']) ? $_SESSION['name'] : "Guest"; // Get name from session

// Handle form submission for new appointments
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data with fallback to avoid undefined index notices
    $doctor = $_POST['doctor'] ?? '';
    $date = $_POST['date'] ?? '';
    $message = $_POST['message'] ?? ''; // Use null coalescing to avoid undefined index
    $patient_id = $_SESSION['id']; // Using the session-stored user ID

    // Insert appointment data into the database
    $sql = "INSERT INTO appointments (appointment_date, doctor, message, patient_id)
            VALUES ('$date', '$doctor', '$message', '$patient_id')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Appointment booked successfully!');</script>";
    } else {
        echo "<script>alert('Error booking appointment: " . $conn->error . "');</script>";
    }
}

// Retrieve all booked appointments for the logged-in user
$patient_id = $_SESSION['id'];
$sql = "SELECT appointment_date, doctor, message FROM appointments WHERE patient_id = '$patient_id'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book an Appointment</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Header Section with Logout link -->
        <div class="header">
            <h2>Welcome, <?php echo htmlspecialchars($userName); ?>!</h2>
            <a href="logout.php" style="float: right;">Logout</a>
        </div>

        <!-- Appointment Booking Form -->
        <h3>Book a New Appointment</h3>
        <form action="book_appointment.php" method="POST">
            <label for="date">Appointment Date:</label>
            <input type="date" id="date" name="date" required><br><br>

            <label for="doctor">Choose Doctor:</label>
            <select id="doctor" name="doctor" required>
                <option value="Dr. Smith">Dr. Smith</option>
                <option value="Dr. Jane">Dr. Jane</option>
                <option value="Dr. Adams">Dr. Adams</option>
            </select><br><br>

            <label for="message">Reason for Appointment:</label>
            <textarea id="message" name="message" rows="4" cols="50"></textarea><br><br>

            <button type="submit">Book Appointment</button>
        </form>

        <!-- Display User's Booked Appointments -->
        <h3>Your Booked Appointments</h3>
        <?php if ($result->num_rows > 0): ?>
            <table border="1">
                <tr>
                    <th>Appointment Date</th>
                    <th>Doctor</th>
                    <th>Message</th>
                </tr>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['doctor']); ?></td>
                        <td><?php echo htmlspecialchars($row['message']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No appointments booked yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close(); // Close database connection
?>
