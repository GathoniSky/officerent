<?php
session_start();

if (!isset($_SESSION["id"])) {
    header("Location: ../tenantlogin.html");
    exit();
}

require_once 'db.php';

// Fetch all listings from the database
$listings_query = "SELECT * FROM listings WHERE status = 'available' OR status IS NULL ORDER BY created_at DESC";
$listings_result = $conn->query($listings_query);

// Check if the query was successful
if (!$listings_result) {
    die("Error fetching listings: " . $conn->error);
}
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
            <a href="../logout.php">Log out</a>
        </div>
        <div class="user-info">
            <div class="user-name">Welcome, <?php echo htmlspecialchars($_SESSION["fullname"]); ?>!</div>
        </div>
    </div>

    <!-- Main Dashboard Content -->
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Available Workspaces</h1>
            <p>Browse through our curated selection of spaces</p>
        </div>

        <!-- Listings Grid -->
        <div class="listings-grid">
            <?php if ($listings_result->num_rows > 0): ?>
                <?php while($listing = $listings_result->fetch_assoc()): ?>
                    <div class="listing-card">
                        <?php if(!empty($listing['image_path'])): ?>
                            <div class="listing-image">
                                <img src="../<?php echo htmlspecialchars($listing['image_path']); ?>" alt="<?php echo htmlspecialchars($listing['title']); ?>">
                            </div>
                        <?php else: ?>
                            <div class="listing-image placeholder-image">
                                <div class="placeholder-text">No Image</div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="listing-details">
                            <h3 class="listing-title"><?php echo htmlspecialchars($listing['title']); ?></h3>
                            
                            <div class="listing-location">
                                <span class="location-icon">📍</span>
                                <?php echo htmlspecialchars($listing['city'] . ', ' . $listing['address']); ?>
                            </div>
                            
                            <div class="listing-features">
                                <?php if(!empty($listing['size'])): ?>
                                    <span class="feature">📐 <?php echo htmlspecialchars($listing['size']); ?> m²</span>
                                <?php endif; ?>
                                
                                <?php if(!empty($listing['capacity'])): ?>
                                    <span class="feature">👥 Up to <?php echo htmlspecialchars($listing['capacity']); ?> people</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="listing-description">
                                <?php 
                                $description = htmlspecialchars($listing['description']);
                                echo strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description;
                                ?>
                            </div>
                            
                            <div class="listing-footer">
                                <div class="listing-price">
                                    <span class="price">₱<?php echo number_format($listing['price'], 2); ?></span>
                                    <span class="price-period">/<?php echo htmlspecialchars($listing['price_period'] ?? 'month'); ?></span>
                                </div>
                                
                                <?php if(!empty($listing['owner_name'])): ?>
                                    <div class="listing-owner">
                                        <span class="owner-label">Hosted by</span>
                                        <span class="owner-name"><?php echo htmlspecialchars($listing['owner_name']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="listing-actions">
                                <a href="view_listing.php?id=<?php echo $listing['id']; ?>" class="btn-view">View Details</a>
                                <a href="inquiry.php?listing_id=<?php echo $listing['id']; ?>" class="btn-inquire">Inquire</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-listings">
                    <div class="no-listings-content">
                        <span class="no-listings-icon">🏢</span>
                        <h3>No Listings Available</h3>
                        <p>There are currently no available workspaces. Please check back later.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">
        <p>© 2026 DreamSpace. All rights reserved.</p>
    </div>
</body>
</html>