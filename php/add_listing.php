<?php
session_start();

if (!isset($_SESSION["id"])) {
    header("Location: ../owner_login.html");
    exit();
}

require_once 'db.php';

$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve form inputs
    $listing_title = sanitize_input($_POST["listing_title"]);
    $total_floor_area = sanitize_input($_POST["total_floor_area"]);
    $property_type = sanitize_input($_POST["property_type"]);
    $address = sanitize_input($_POST["address"]);
    $monthly_rent = sanitize_input($_POST["monthly_rent"]);
    $status = sanitize_input($_POST["status"] ?? 'available');
    $owner_id = $_SESSION["id"];
    
    // Validation
    $errors = [];
    
    if (empty($listing_title)) {
        $errors[] = "Listing title is required.";
    }
    
    if (empty($total_floor_area) || !is_numeric($total_floor_area) || $total_floor_area <= 0) {
        $errors[] = "Please enter a valid floor area (positive number).";
    }
    
    if (empty($property_type)) {
        $errors[] = "Property type is required.";
    }
    
    if (empty($address)) {
        $errors[] = "Address is required.";
    }
    
    if (empty($monthly_rent) || !is_numeric($monthly_rent) || $monthly_rent <= 0) {
        $errors[] = "Please enter a valid monthly rent (positive number).";
    }
    
    // If no errors, insert into database
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO listings (listing_title, total_floor_area, property_type, address, monthly_rent, status, owner_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
        
        $stmt->bind_param("sdssdsi", $listing_title, $total_floor_area, $property_type, $address, $monthly_rent, $status, $owner_id);
        
        if ($stmt->execute()) {
            $success_message = "Listing added successfully!";
            // Clear form data on success
            $listing_title = $total_floor_area = $property_type = $address = $monthly_rent = '';
        } else {
            $error_message = "Error adding listing: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        $error_message = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Listing · DreamSpace</title>
    <link rel="stylesheet" href="../css/owner.css">
    <link rel="icon" href="../css/LOGO.png" type="image/png">
</head>
<body>
    <nav class="top-nav">
        <a href="../index.html" class="dreamspace-logo">Dream<span>Space</span></a>
        <div style="font-weight: 600;">Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</div>
        <a href="owner_dash.php" style="text-decoration: none; color: var(--text-muted);">← Back to Dashboard</a>
    </nav>

    <div class="form-container">
        <div class="form-card">
            <h1 class="form-title">Add New <span>Listing</span></h1>
            <p class="form-subtitle">Fill in the details below to list your space</p>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="add-listing-form">
                <!-- Listing Title -->
                <div class="form-group">
                    <label for="listing_title">Listing Title <span class="required">*</span></label>
                    <input type="text" id="listing_title" name="listing_title" 
                        value="<?php echo htmlspecialchars($listing_title ?? ''); ?>" 
                        placeholder="e.g. Modern Office Space in BGC" required>
                </div>

                <!-- Total Floor Area -->
                <div class="form-group">
                    <label for="total_floor_area">Total Floor Area (sqm) <span class="required">*</span></label>
                    <input type="number" id="total_floor_area" name="total_floor_area" 
                        value="<?php echo htmlspecialchars($total_floor_area ?? ''); ?>" 
                        placeholder="e.g. 120.50" step="0.01" min="0.01" required>
                </div>

                <!-- Property Type -->
                <div class="form-group">
                    <label for="property_type">Property Type <span class="required">*</span></label>
                    <select id="property_type" name="property_type" required>
                        <option value="" disabled <?php echo empty($property_type) ? 'selected' : ''; ?>>Select property type</option>
                        <option value="Office" <?php echo ($property_type ?? '') == 'Office' ? 'selected' : ''; ?>>Office</option>
                        <option value="Co-working space" <?php echo ($property_type ?? '') == 'Co-working space' ? 'selected' : ''; ?>>Co-working Space</option>
                        <option value="Shared office" <?php echo ($property_type ?? '') == 'Shared office' ? 'selected' : ''; ?>>Shared Office</option>
                        <option value="Commercial building" <?php echo ($property_type ?? '') == 'Commercial building' ? 'selected' : ''; ?>>Commercial Building</option>
                    </select>
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label for="address">Address <span class="required">*</span></label>
                    <textarea id="address" name="address" rows="3" placeholder="Full address of your property" required><?php echo htmlspecialchars($address ?? ''); ?></textarea>
                </div>

                <!-- Monthly Rent -->
                <div class="form-group">
                    <label for="monthly_rent">Monthly Rent (₱) <span class="required">*</span></label>
                    <div class="currency-input">
                        <span class="currency-symbol">₱</span>
                        <input type="number" id="monthly_rent" name="monthly_rent" 
                            value="<?php echo htmlspecialchars($monthly_rent ?? ''); ?>" 
                            placeholder="e.g. 25000" step="0.01" min="0.01" required>
                    </div>
                </div>

                <!-- Status (Hidden by default, but can be included) -->
                <input type="hidden" name="status" value="available">

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn-submit">Add Listing</button>
                    <a href="owner_dash.php" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>