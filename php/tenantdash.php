<?php
session_start();

if (!isset($_SESSION["id"])) {
    header("Location: ../tenantlogin.html");
    exit();
}

require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DreamSpace · Tenant Dashboard</title>
    <link rel="stylesheet" href="../css/tenantstyle.css">
    <link rel="icon" href="../css/LOGO.png" type="image/png">
</head>
<body>
    <div class="top-nav">
        <div class="dreamspace-logo">
            Dream<span>Space</span>
        </div>
        <div class="nav-links">
            <a href="../index.html">Home</a>
            <a href="../index.html">Log out</a>
        </div>
    </div>
            <div class="user-info">
                <div class="user-name"><?php echo htmlspecialchars($_SESSION["fullname"]); ?></div>
            </div>
        </div>
    </div>
    <div class="footer">
        <p>© 2026 DreamSpace. All rights reserved.</p>
    </div>
</body>
</html>