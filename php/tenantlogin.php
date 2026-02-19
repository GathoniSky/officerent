<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Include database connection
require_once 'db.php';

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST["fullname"]) && isset($_POST["password"])) {
        
        // Sanitize and retrieve form inputs
        $fullname = sanitize_input($_POST["fullname"]);
        $password = sanitize_input($_POST["password"]);
        
        // Get user by fullname only (don't check password in SQL)
        $stmt = $conn->prepare("SELECT id, fullname, email, password FROM tenants WHERE fullname = ?");
        
        if ($stmt === false) {
            error_log("Error preparing statement: " . $conn->error);
            header("Location: ../tenantlogin.html?msg=error");
            exit();
        }
        
        $stmt->bind_param("s", $fullname);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Check if the user exists
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            
            // Verify password using password_verify() (secure)
            if (password_verify($password, $data['password'])) {
                $_SESSION['id'] = $data['id'];
                $_SESSION['fullname'] = $data['fullname'];
                $_SESSION['email'] = $data['email'];
                $_SESSION['logged_in'] = true;
                
                // Redirect to tenant dashboard
                header("Location: tenantdash.php?msg=welcome");
                exit();
            } else {
                // Wrong password alert
                echo "<script>alert('Wrong password!'); window.location.href='../tenantlogin.html';</script>";
                exit();
            }
        } else {
            // User not found alert
            echo "<script>alert('User not found!'); window.location.href='../tenantlogin.html';</script>";
            exit();
        }
        $stmt->close();
        
    } else {
        header("Location: ../tenantlogin.html?msg=error");
        exit();
    }
} else {
    header("Location: ../tenantlogin.html");
    exit();
}

$conn->close();
?>