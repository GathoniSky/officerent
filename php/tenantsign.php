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
    $fullname = sanitize_input($_POST["fullname"]);
    $email = sanitize_input($_POST["email"]);
    $password = sanitize_input($_POST["password"]);

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO tenants(fullname, password, email) VALUES (?, ?, ?)");
    
    // Check if prepare() was successful
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("sss", $fullname, $hashed_password, $email);

    // Execute the statement
    if ($stmt->execute()) {
        echo "New tenant registered successfully!";
        header("Location: ../tenantlogin.html");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
