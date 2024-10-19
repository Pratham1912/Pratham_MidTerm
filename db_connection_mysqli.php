<?php
$host = 'localhost'; // Database host
$user = 'root'; // Database username
$password = ''; // Database password
$dbname = 'art_gallery'; // Database name

// Create connection
$dbc = mysqli_connect($host, $user, $password, $dbname);

// Check connection
if (!$dbc) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
