<?php
require('db_connection_mysqli.php');
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$artworkId = $_GET['id'];

// Delete artwork from the database
$query = "DELETE FROM artworks WHERE ArtworkID = ?";
$stmt = mysqli_prepare($dbc, $query);
mysqli_stmt_bind_param($stmt, 'i', $artworkId);
if (mysqli_stmt_execute($stmt)) {
    header("Location: index.php");
    exit;
} else {
    echo "Error: " . mysqli_error($dbc);
}
?>
