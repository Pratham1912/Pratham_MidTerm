<?php
require('db_connection_mysqli.php');
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Initialize variables
$title = $artist = $description = $price = $medium = $size = $image = "";
$titleErr = $artistErr = $descriptionErr = $priceErr = $mediumErr = $sizeErr = $imageErr = "";
$for_sale = 0; // Initialize 'for_sale'

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = cleanInput($_POST["title"]);
    $artist = cleanInput($_POST["artist"]);
    $description = cleanInput($_POST["description"]);
    $price = cleanInput($_POST["price"]);
    $medium = cleanInput($_POST["medium"]);
    $size = cleanInput($_POST["size"]);
    
    // Handle image upload
    if (!empty($_FILES["image"]["name"])) {
        // Specify the target directory for image upload
        $target_dir = "uploads/";
        $image = $target_dir . basename($_FILES["image"]["name"]);
        
        // Check if the uploaded file is an image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $imageErr = "File is not an image.";
        }

        // Check file size (limit to 8MB for example)
        if ($_FILES["image"]["size"] > 8000000) {
            $imageErr = "Sorry, your file is too large.";
        }

        // Allow certain file formats
        $imageFileType = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $imageErr = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }
    } else {
        $imageErr = "Image is required.";
    }

    // Validate other inputs
    if (empty($title)) {
        $titleErr = "Title is required.";
    }
    if (empty($artist)) {
        $artistErr = "Artist is required.";
    }
    if (empty($description)) {
        $descriptionErr = "Description is required.";
    }
    if (empty($price) || !is_numeric($price)) {
        $priceErr = "Valid price is required.";
    }
    if (empty($medium)) {
        $mediumErr = "Medium is required.";
    }
    if (empty($size)) {
        $sizeErr = "Size is required.";
    }

    // Check if the 'for_sale' checkbox is checked
    $for_sale = isset($_POST["for_sale"]) ? 1 : 0; // Default to 0 if not checked

    // If there are no errors, proceed with the upload and database insert
    if (empty($titleErr) && empty($artistErr) && empty($descriptionErr) && empty($priceErr) && empty($mediumErr) && empty($sizeErr) && empty($imageErr)) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $image)) {
            // Insert into database
            $query = "INSERT INTO artworks (Title, Artist, Description, Price, Medium, Size, Image, ForSale) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($dbc, $query);
            mysqli_stmt_bind_param($stmt, 'sssssssi', $title, $artist, $description, $price, $medium, $size, $image, $for_sale);
            
            if (mysqli_stmt_execute($stmt)) {
                header("Location: index.php"); // Redirect to the gallery page
                exit;
            } else {
                echo "Error: " . mysqli_error($dbc);
            }
        } else {
            $imageErr = "Sorry, there was an error uploading your file.";
        }
    }
}

// Function to clean input data
function cleanInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Artwork</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css"> <!-- Link to your external CSS file -->
</head>
<body>
    <header class="bg-primary text-white text-center py-3">
        <h1>Online Art Gallery</h1>
        <p>Admin Panel - Add Artwork</p>
    </header>

    <div class="container mt-5">
        <h2>Add Artwork</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($title); ?>">
                <span class="text-danger"><?php echo $titleErr; ?></span>
            </div>
            <div class="mb-3">
                <label for="artist" class="form-label">Artist</label>
                <input type="text" class="form-control" name="artist" value="<?php echo htmlspecialchars($artist); ?>">
                <span class="text-danger"><?php echo $artistErr; ?></span>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" name="description"><?php echo htmlspecialchars($description); ?></textarea>
                <span class="text-danger"><?php echo $descriptionErr; ?></span>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="text" class="form-control" name="price" value="<?php echo htmlspecialchars($price); ?>">
                <span class="text-danger"><?php echo $priceErr; ?></span>
            </div>
            <div class="mb-3">
                <label for="medium" class="form-label">Medium</label>
                <select class="form-select" name="medium">
                    <option value="">Select Medium</option>
                    <option value="Oil" <?php echo ($medium == 'Oil') ? 'selected' : ''; ?>>Oil</option>
                    <option value="Acrylic" <?php echo ($medium == 'Acrylic') ? 'selected' : ''; ?>>Acrylic</option>
                    <option value="Watercolor" <?php echo ($medium == 'Watercolor') ? 'selected' : ''; ?>>Watercolor</option>
                    <option value="Digital" <?php echo ($medium == 'Digital') ? 'selected' : ''; ?>>Digital</option>
                </select>
                <span class="text-danger"><?php echo $mediumErr; ?></span>
            </div>
            <div class="mb-3">
                <label for="size" class="form-label">Size</label>
                <input type="text" class="form-control" name="size" value="<?php echo htmlspecialchars($size); ?>">
                <span class="text-danger"><?php echo $sizeErr; ?></span>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" class="form-control" name="image">
                <span class="text-danger"><?php echo $imageErr; ?></span>
            </div>
            <div class="mb-3">
                <label class="form-label">For Sale</label><br>
                <input type="checkbox" name="for_sale" value="1" <?php echo ($for_sale == 1) ? 'checked' : ''; ?>> Yes
            </div>

            <button type="submit" class="btn btn-primary">Add Artwork</button>
        </form>
        <a href="index.php" class="btn btn-secondary mt-3">Back to Gallery</a>
    </div>

    <footer class="bg-dark text-white text-center p-3 mt-5">
        <p>&copy; <?php echo date("Y"); ?> Online Art Gallery. All Rights Reserved.</p>
    </footer>
</body>
</html>
