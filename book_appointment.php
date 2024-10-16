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

// Retrieve the logged-in user's name and email from the session
$userName = isset($_SESSION['name']) ? $_SESSION['name'] : "Guest"; // Get name from session
$userEmail = isset($_SESSION['email']) ? $_SESSION['email'] : "";  // Get email from session

// Handle form submission for new appointments
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data with fallback to avoid undefined index notices
    $doctor = $_POST['doctor'] ?? '';
    $date = $_POST['date'] ?? '';
    $message = $_POST['message'] ?? ''; // Use null coalescing to avoid undefined index
    $phone = $_POST['phone'] ?? ''; // Add phone field
    $email = $_POST['email'] ?? ''; // Add email field
    $address = $_POST['address'] ?? ''; 
    $patient_id = $_SESSION['id']; // Using the session-stored user ID

    // Insert appointment data into the database
    $sql = "INSERT INTO appointments (appointment_date, name, email, address,phone, doctor, message, patient_id)
            VALUES ('$date','$userName','$email','$address','$phone','$doctor', '$message', '$patient_id')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Appointment booked successfully!');</script>";
    } else {
        echo "<script>alert('Error booking appointment: " . $conn->error . "');</script>";
    }
}

// Retrieve all booked appointments for the logged-in user
$patient_id = $_SESSION['id'];
$sql = "SELECT appointment_date, doctor, message, phone, email, address FROM appointments WHERE patient_id = '$patient_id'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book an Appointment</title>
    <link rel="stylesheet" href="bookappointmentstyle.css">
</head>
<body>
    <div class="container">
        <!-- Header Section with Logout link -->
        <div class="header">
            <h2>Welcome, <?php echo htmlspecialchars($userName); ?>!</h2>
            <a href="logout.php" style="float: right; font-size: 20px;"><b>Logout</b></a>
        </div>
        
        <h3>Your Booked Appointments</h3>
        <?php if ($result->num_rows > 0): ?>
            <table border="1" id="appointmentsTable">
                <tr>
                    <th>Appointment Date</th>
                    <th>Doctor</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Message</th>
                    <th>Status</th> <!-- Add Status column -->
                </tr>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['doctor']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td><?php echo htmlspecialchars($row['message']); ?></td>
                        <td class="status">Pending</td> <!-- Add a default Pending status -->
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No appointments booked yet.</p>
        <?php endif; ?>
        <!-- Appointment Booking Form -->
        <h3>Book a New Appointment</h3>
        <form action="book_appointment.php" method="POST">
            <label for="date">Appointment Date:</label>
            <input type="date" id="date" name="date" required><br><br>

            <label for="doctor">Choose Doctor:</label>
            <select id="doctor" name="doctor" required>
                <option value="Dr. ABC">Dr. ABC</option>
                <option value="Dr. PQR">Dr. PQR</option>
                <option value="Dr. XYZ">Dr. XYZ</option>
            </select><br><br>

            <label for="phone">Phone Number:</label>
            <input type="text" id="phone" name="phone" required><br><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($userEmail); ?>"><br><br>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required><br><br>

            <label for="message">Reason for Appointment:</label>
            <textarea id="message" name="message" rows="4" cols="50"></textarea><br><br>

            <button type="submit">Book Appointment</button>
        </form>

        <!-- Display User's Booked Appointments -->
        <!-- Display User's Booked Appointments -->
    </div>
    <script src="bookappointment.js"></script>
</body>
</html>

<?php
$conn->close(); // Close database connection
?>
