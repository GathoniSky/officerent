<?php
session_start();

if (!isset($_SESSION["id"])) {
    header("Location: ../owner_login.html");
    exit();
}

require_once 'db.php';

// Fetch ONLY the listings belonging to this specific owner
$owner_id = $_SESSION["id"];
$listings_query = "SELECT * FROM listings WHERE owner_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($listings_query);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$listings_result = $stmt->get_result();

// Check if the query was successful
if (!$listings_result) {
    die("Error fetching listings: " . $conn->error);
}

// Count listings by status
$available_count = 0;
$rented_count = 0;
$pending_count = 0;

// You can loop through results or do separate counts
$count_query = "SELECT 
    SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available,
    SUM(CASE WHEN status = 'rented' THEN 1 ELSE 0 END) as rented,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending
    FROM listings WHERE owner_id = ?";
$count_stmt = $conn->prepare($count_query);
$count_stmt->bind_param("i", $owner_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$counts = $count_result->fetch_assoc();

if ($counts) {
    $available_count = $counts['available'] ?? 0;
    $rented_count = $counts['rented'] ?? 0;
    $pending_count = $counts['pending'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard · DreamSpace</title>
    <link rel="stylesheet" href="../css/owner.css">
    <link rel="icon" href="../css/LOGO.png" type="image/png">
</head>
<body>

    <nav class="top-nav">
        <a href="../index.html" class="dreamspace-logo">Dream<span>Space</span></a>
        <div style="font-weight: 600;">Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</div>
        <a href="../logout.php" style="text-decoration: none; color: var(--text-muted);">Logout</a>
    </nav>

    <!-- Optional: Quick Stats Cards -->
    <div class="stats-container" style="display: flex; gap: 20px; max-width: 1200px; margin: 20px auto 0; padding: 0 20px;">
        <div style="flex: 1; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
            <h3 style="margin: 0; color: #4a4560;">Available</h3>
            <p style="font-size: 2rem; font-weight: 700; margin: 10px 0 0; color: #28a745;"><?php echo $available_count; ?></p>
        </div>
        <div style="flex: 1; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
            <h3 style="margin: 0; color: #4a4560;">Rented</h3>
            <p style="font-size: 2rem; font-weight: 700; margin: 10px 0 0; color: #dc3545;"><?php echo $rented_count; ?></p>
        </div>
        <div style="flex: 1; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
            <h3 style="margin: 0; color: #4a4560;">Pending</h3>
            <p style="font-size: 2rem; font-weight: 700; margin: 10px 0 0; color: #ffc107;"><?php echo $pending_count; ?></p>
        </div>
    </div>

    <div class="dash-container">
        <div class="mgmt-box">
            <h2>Manage <span>Listings</span></h2>
            <div class="btn-stack">
                <button onclick="window.location.href='add_listing.php'" class="dash-btn btn-add">+ Add New Space</button>
                <button onclick="window.location.href='update_listing.php'" class="dash-btn btn-update">Update Existing</button>
                <button onclick="window.location.href='delete_listing.php'" class="dash-btn btn-delete">Delete Listing</button>
            </div>
            <p style="margin-top: 20px; font-size: 0.8rem; color: var(--text-muted);">
                Need help? Contact support@dreamspace.com
            </p>
        </div>

        <div class="listings-box">
            <h2>Your <span>Current Listings</span></h2>
            
            <?php if ($listings_result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Listing Title</th>
                            <th>Property Type</th>
                            <th>Floor Area (sqm)</th>
                            <th>Location</th>
                            <th>Monthly Rent</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($listing = $listings_result->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($listing['listing_title']); ?></strong></td>
                                <td><?php echo htmlspecialchars($listing['property_type']); ?></td>
                                <td><?php echo htmlspecialchars($listing['total_floor_area']); ?> m²</td>
                                <td><?php echo htmlspecialchars(substr($listing['address'], 0, 30)) . (strlen($listing['address']) > 30 ? '...' : ''); ?></td>
                                <td class="price-tag">₱<?php echo number_format($listing['monthly_rent'], 2); ?>/mo</td>
                                <td>
                                    <?php 
                                    $status = $listing['status'] ?? 'available';
                                    $status_class = '';
                                    switch($status) {
                                        case 'available':
                                            $status_class = 'status-available';
                                            break;
                                        case 'rented':
                                            $status_class = 'status-rented';
                                            break;
                                        case 'pending':
                                            $status_class = 'status-pending';
                                            break;
                                    }
                                    ?>
                                    <span class="status-badge <?php echo $status_class; ?>"><?php echo ucfirst($status); ?></span>
                                </td>
                                <td>
                                    <a href="edit_listing.php?id=<?php echo $listing['id']; ?>" style="color: #6c47ff; text-decoration: none; margin-right: 10px;">Edit</a>
                                    <a href="view_listing.php?id=<?php echo $listing['id']; ?>" style="color: #28a745; text-decoration: none;">View</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 40px 20px; background: #f9f9f9; border-radius: 12px; margin-top: 20px;">
                    <p style="font-size: 1.2rem; color: #7b788b; margin-bottom: 20px;">You haven't added any listings yet.</p>
                    <button onclick="window.location.href='add_listing.php'" class="dash-btn btn-add" style="display: inline-block; width: auto;">+ Add Your First Listing</button>
                </div>
            <?php endif; ?>
        </div>
    </div>


</body>
</html>