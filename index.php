<?php
require('db_connection_mysqli.php');
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Initialize an array to hold artworks
$artworks = [];

// Fetch artworks from the database
$query = "SELECT * FROM artworks";
$result = mysqli_query($dbc, $query);

if (!$result) {
    // Handle error
    echo "Error fetching artworks: " . mysqli_error($dbc);
    exit;
}

// Fetch all artworks into the array
while ($row = mysqli_fetch_assoc($result)) {
    $artworks[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Art Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css"> <!-- Link to the external CSS file -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome -->
    <style>
        .subheading {
            font-size: 1.2rem; /* Slightly larger font size */
            font-weight: bold; /* Make it bold */
            margin: 0; /* Remove default margin */
            color: #343a40; /* Dark color */
        }
        .header-title {
            margin: 0; /* Remove margin */
            text-align: center; /* Center the title */
        }
    </style>
</head>

<body>
    <!-- Header Section -->
    <header class="bg-primary text-white py-3">
        <div class="container text-center">
            <h1 class="header-title">Welcome to the Art Gallery</h1>
            <p class="subheading">Discover the beauty of art!</p>
            <div class="mt-3">
                <a href="index.php" class="btn btn-light me-2">Home</a>
                <a href="add_artwork.php" class="btn btn-light me-2">Add Product</a>
                <a href="logout.php" class="btn btn-light">Logout</a>
            </div>
        </div>
    </header>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mx-auto text-center">Art Gallery</h2>
            <a href="add_artwork.php" class="btn btn-primary">Add Artwork</a>
        </div>
        
        <div class="row">
            <?php if (count($artworks) > 0): ?>
                <?php foreach ($artworks as $artwork): ?>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <img src="<?php echo htmlspecialchars($artwork['Image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($artwork['Title']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($artwork['Title']); ?></h5>
                                <p class="card-text">Artist: <?php echo htmlspecialchars($artwork['Artist']); ?></p>
                                <p class="card-text">Price: $<?php echo htmlspecialchars($artwork['Price']); ?></p>
                                <p class="card-text">Medium: <?php echo htmlspecialchars($artwork['Medium']); ?></p>
                                <p class="card-text">Size: <?php echo htmlspecialchars($artwork['Size']); ?></p>
                                <p class="card-text">Description: <?php echo htmlspecialchars($artwork['Description']); ?></p>
                                
                                <!-- For Sale Status -->
                                <p class="card-text">
                                    <strong>For Sale:</strong> 
                                    <?php echo htmlspecialchars($artwork['ForSale']) ? 'Yes' : 'No'; ?>
                                </p>
                                
                                <!-- Edit and Delete Buttons with Icons -->
                                <div>
                                    <a href="edit_artwork.php?id=<?php echo htmlspecialchars($artwork['ArtworkID']); ?>" class="btn btn-warning btn-custom">
                                        <i class="fas fa-pencil-alt"></i> Edit
                                    </a>
                                    <a href="delete_artwork.php?id=<?php echo htmlspecialchars($artwork['ArtworkID']); ?>" class="btn btn-danger btn-custom" onclick="return confirm('Are you sure you want to delete this artwork?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">No artworks found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer Section -->
    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; <?php echo date("Y"); ?> Art Gallery. All Rights Reserved.</p>
    </footer>
</body>
</html>
