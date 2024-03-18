<?php

// Check if user_id is set
if (!isset($_SESSION['user_id'])) {
    die("error getting user_id");
}
$user_id = $_SESSION['user_id'];

// Database connection settings
$servername = $_ENV["DB_SERVERNAME"];
$username = $_ENV["DB_USERNAME"];
$password = $_ENV["DB_PASSWORD"];
$dbname = "reviews";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// sql query
$query = "SELECT user_id, service_id, service_type, review_rating, review_body, review_date 
    FROM reviews WHERE user_id == " . $user_id;

// execute query
$stmt = $conn->prepare($query);
$stmt->bind_param("issd", $user_id, $service_id, $service_type, $review_rating, $review_body, $review_date);
$stmt->execute();

// output data
if ($result->num_rows > 0) {
    $reviews = array();
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
    
    // output as JSON
    echo json_encode($reviews);
} 
// no data
else {
    echo "[]";
}

// close database connection
$stmt->close();
$conn->close();
?>
