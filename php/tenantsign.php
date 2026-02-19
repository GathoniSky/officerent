<?php
require_once 'db.php';

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve form inputs
    $driver_name = sanitize_input($_POST["username"]);
    $driver_email = sanitize_input($_POST["email"]);
    $driver_phone = sanitize_input($_POST["phone_number"]);
    $driver_password = sanitize_input($_POST["password"]);
    $driver_license = sanitize_input($_POST["license_number"]);

    // Hash the password for security
    $hashed_password = password_hash($driver_password, PASSWORD_DEFAULT);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO drivers (username, password, email, phone_number, license_number) VALUES (?, ?, ?, ?, ?)");
    
    // Check if prepare() was successful
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("sssss", $driver_name, $hashed_password, $driver_email, $driver_phone, $driver_license);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect to login page
        header("Location: ../tenantlogin.html");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
