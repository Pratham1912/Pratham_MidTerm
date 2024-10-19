<?php
require('db_connection_mysqli.php');
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$artworkId = $_GET['id'];
$title = $artist = $description = $price = $medium = $size = $image = "";
$titleErr = $artistErr = $descriptionErr = $priceErr = $mediumErr = $sizeErr = $imageErr = "";

// Fetch existing artwork details
$query = "SELECT * FROM artworks WHERE ArtworkID = ?";
$stmt = mysqli_prepare($dbc, $query);
mysqli_stmt_bind_param($stmt, 'i', $artworkId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$artwork = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = cleanInput($_POST["title"]);
    $artist = cleanInput($_POST["artist"]);
    $description = cleanInput($_POST["description"]);
    $price = cleanInput($_POST["price"]);
    $medium = cleanInput($_POST["medium"]);
    $size = cleanInput($_POST["size"]);
    
    // Handle image upload
    if ($_FILES["image"]["error"] == UPLOAD_ERR_NO_FILE) {
        $image = $artwork['Image']; // Keep the old image
    } else {
        $image = 'uploads/' . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
    }

    // Validate inputs
    if (empty($title)) {
        $titleErr = "Title is required";
    }
    if (empty($artist)) {
        $artistErr = "Artist is required";
    }
    if (empty($description)) {
        $descriptionErr = "Description is required";
    }
    if (empty($price) || !is_numeric($price)) {
        $priceErr = "Valid price is required";
    }
    if (empty($medium)) {
        $mediumErr = "Medium is required";
    }
    if (empty($size)) {
        $sizeErr = "Size is required";
    }

    // Update artwork in database
    if (empty($titleErr) && empty($artistErr) && empty($descriptionErr) && empty($priceErr) && empty($mediumErr) && empty($sizeErr)) {
        $query = "UPDATE artworks SET Title=?, Artist=?, Description=?, Price=?, Medium=?, Size=?, Image=? WHERE ArtworkID=?";
        $stmt = mysqli_prepare($dbc, $query);
        mysqli_stmt_bind_param($stmt, 'sssssssi', $title, $artist, $description, $price, $medium, $size, $image, $artworkId);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php");
            exit;
        } else {
            echo "Error: " . mysqli_error($dbc);
        }
    }
}

function cleanInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Artwork</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Artwork</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($artwork['Title']); ?>">
                <span class="text-danger"><?php echo $titleErr; ?></span>
            </div>
            <div class="mb-3">
                <label for="artist" class="form-label">Artist</label>
                <input type="text" class="form-control" name="artist" value="<?php echo htmlspecialchars($artwork['Artist']); ?>">
                <span class="text-danger"><?php echo $artistErr; ?></span>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" name="description"><?php echo htmlspecialchars($artwork['Description']); ?></textarea>
                <span class="text-danger"><?php echo $descriptionErr; ?></span>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="text" class="form-control" name="price" value="<?php echo htmlspecialchars($artwork['Price']); ?>">
                <span class="text-danger"><?php echo $priceErr; ?></span>
            </div>
            <div class="mb-3">
                <label for="medium" class="form-label">Medium</label>
                <select class="form-select" name="medium">
                    <option value="">Select Medium</option>
                    <option value="Oil" <?php echo ($artwork['Medium'] == 'Oil') ? 'selected' : ''; ?>>Oil</option>
                    <option value="Acrylic" <?php echo ($artwork['Medium'] == 'Acrylic') ? 'selected' : ''; ?>>Acrylic</option>
                    <option value="Watercolor" <?php echo ($artwork['Medium'] == 'Watercolor') ? 'selected' : ''; ?>>Watercolor</option>
                    <option value="Digital" <?php echo ($artwork['Medium'] == 'Digital') ? 'selected' : ''; ?>>Digital</option>
                    <option value="Other" <?php echo ($artwork['Medium'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
                <span class="text-danger"><?php echo $mediumErr; ?></span>
            </div>
            <div class="mb-3">
                <label for="size" class="form-label">Size</label>
                <input type="text" class="form-control" name="size" value="<?php echo htmlspecialchars($artwork['Size']); ?>">
                <span class="text-danger"><?php echo $sizeErr; ?></span>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" class="form-control" name="image">
                <span class="text-danger"><?php echo $imageErr; ?></span>
                <!-- Display the existing image -->
                <?php if (!empty($artwork['Image'])): ?>
                    <div class="mt-3">
                        <img src="<?php echo htmlspecialchars($artwork['Image']); ?>" alt="Current Artwork Image" class="img-fluid" style="max-width: 100%;">
                    </div>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Update Artwork</button>
        </form>
        <a href="index.php" class="btn btn-secondary mt-3">Back to Gallery</a>
    </div>
</body>
</html>
