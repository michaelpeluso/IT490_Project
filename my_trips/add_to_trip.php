<?php
// Database connection settings
$servername = "sql1.njit.edu";
$username = "atj2";
$password = "A.Cherry8890";
$dbname = "trips";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the restaurant data from the AJAX request
$name = $_POST['name'];
$address = $_POST['address'];
$rating = $_POST['rating'];

// Get the user ID (assuming you have a logged-in user)
$user_id = 1; // Replace with the actual user ID

// Prepare and execute the SQL query to insert the restaurant data into the database
$stmt = $conn->prepare("INSERT INTO trips (user_id, restaurant_name, restaurant_address, restaurant_rating) VALUES (?, ?, ?, ?)");
$stmt->bind_param("issd", $user_id, $name, $address, $rating);
$stmt->execute();

// Close the statement and database connection
$stmt->close();
$conn->close();
?>